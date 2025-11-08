"""
Google Places + Programmable Search 工具
---------------------------------------
讀取 Excel（需包含 Name / Add / Phone 欄位），
對每筆餐廳名稱使用 Google Places API 搜尋，根據地址比對確認同一家餐廳，
再透過 Google Programmable Search (Custom Search API) 尋找 Yelp 連結。

輸出欄位包含：
    - Matched_Name / Matched_Address（Google 確認資訊）
    - Type_1 / Type_2（Google Places 回傳的前兩項類別）
    - Yelp_URL（Google Custom Search 找到的第一個 Yelp 連結）
    - Match_Status（matched / not_matched / error 等）
    - Message（除錯訊息）

流程：
    python google_yelp_lookup.py input.xlsx --output result.xlsx --limit 20
"""

from __future__ import annotations

import argparse
import logging
import os
import re
import sys
import time
import unicodedata
from datetime import datetime
from typing import Dict, List, Optional, Tuple

import pandas as pd
import requests
from dotenv import load_dotenv

# API 端點
PLACES_FIND_URL = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json"
CUSTOM_SEARCH_URL = "https://www.googleapis.com/customsearch/v1"

# 常數設定
DEFAULT_CITY = "Philadelphia"
DEFAULT_STATE = "PA"
REQUEST_DELAY_SECONDS = 0.35  # 避免過度頻繁呼叫
LOGGER = logging.getLogger("google_yelp_lookup")

# 地址清理替換詞
ADDRESS_REPLACEMENTS = {
    "street": "st",
    "avenue": "ave",
    "road": "rd",
    "boulevard": "blvd",
    "drive": "dr",
    "lane": "ln",
    "court": "ct",
    "circle": "cir",
    "square": "sq",
    "place": "pl",
    "terrace": "ter",
    "parkway": "pkwy",
    "highway": "hwy",
    "north": "n",
    "south": "s",
    "east": "e",
    "west": "w",
}


# --------------------------------------------------------------------------- #
# 初始化
# --------------------------------------------------------------------------- #
def setup_logging(verbose: bool = False) -> None:
    """設定 logging 格式。"""
    handler = logging.StreamHandler(sys.stdout)
    handler.setFormatter(
        logging.Formatter("[%(levelname)s] %(asctime)s - %(message)s", "%Y-%m-%d %H:%M:%S")
    )
    LOGGER.addHandler(handler)
    LOGGER.setLevel(logging.DEBUG if verbose else logging.INFO)


def load_credentials() -> Tuple[str, str, str]:
    """載入 .env / 環境變數中的 API 金鑰與 cx。"""
    load_dotenv()
    places_key = os.getenv("GOOGLE_PLACES_API_KEY")
    search_key = os.getenv("GOOGLE_CUSTOM_SEARCH_API_KEY")
    search_cx = os.getenv("GOOGLE_CUSTOM_SEARCH_CX")

    missing = []
    if not places_key:
        missing.append("GOOGLE_PLACES_API_KEY")
    if not search_key:
        missing.append("GOOGLE_CUSTOM_SEARCH_API_KEY")
    if not search_cx:
        missing.append("GOOGLE_CUSTOM_SEARCH_CX")

    if missing:
        raise RuntimeError(f"缺少環境變數：{', '.join(missing)}")

    return places_key, search_key, search_cx


# --------------------------------------------------------------------------- #
# 資料處理與比對
# --------------------------------------------------------------------------- #
def normalize_text(value: str) -> str:
    """轉小寫、移除重音與多餘空白。"""
    value = unicodedata.normalize("NFKD", value)
    value = value.encode("ascii", "ignore").decode("ascii")
    value = value.lower()
    value = re.sub(r"\s+", " ", value)
    return value.strip()


def normalize_address(address: str) -> str:
    """將地址標準化，便於比對。"""
    if not address:
        return ""

    address = normalize_text(address)

    # 移除常見分隔符號
    address = re.sub(r"[.,]", " ", address)

    # 取代常見詞
    for full, short in ADDRESS_REPLACEMENTS.items():
        address = re.sub(rf"\b{full}\b", short, address)

    # 移除城市 / 州 / 國家等字樣
    address = re.sub(r"\bphiladelphia\b", "", address)
    address = re.sub(r"\bpa\b", "", address)
    address = re.sub(r"\bpennsylvania\b", "", address)
    address = re.sub(r"\busa\b|\bunited states\b", "", address)

    # 移除多餘空白後合併
    address = re.sub(r"\s+", "", address)
    return address


def addresses_match(addr_a: str, addr_b: str) -> bool:
    """比對兩個地址是否可能為同一位置。"""
    if not addr_a or not addr_b:
        return False

    norm_a = normalize_address(addr_a)
    norm_b = normalize_address(addr_b)

    if not norm_a or not norm_b:
        return False

    if norm_a == norm_b:
        return True

    # 若其中一方是另一方的子字串（處理單位號、郵遞區號差異）
    if norm_a in norm_b or norm_b in norm_a:
        return True

    # 比較門牌號碼與主要街道名稱
    number_a = re.findall(r"^\d+", norm_a)
    number_b = re.findall(r"^\d+", norm_b)
    if number_a and number_b and number_a[0] != number_b[0]:
        return False

    # 取前 20 個字作為部分比對
    return norm_a[:20] == norm_b[:20]


def format_types(types: List[str]) -> List[str]:
    """將 Google Places 的 types 改為 Title Case 並移除常見泛用類別。"""
    if not types:
        return []

    filtered = []
    skip_keywords = {"point_of_interest", "establishment", "food"}
    for t_value in types:
        t_value = t_value.strip()
        if not t_value or t_value in skip_keywords:
            continue
        friendly = t_value.replace("_", " ").title()
        if friendly not in filtered:
            filtered.append(friendly)
    return filtered[:2]


# --------------------------------------------------------------------------- #
# Google Places 查詢
# --------------------------------------------------------------------------- #
def query_place(candidate_name: str, places_key: str) -> Tuple[Optional[Dict], str]:
    """呼叫 Google Places Find Place API。"""
    params = {
        "input": f"{candidate_name} {DEFAULT_CITY}",
        "inputtype": "textquery",
        "fields": "place_id,formatted_address,name,types",
        "key": places_key,
    }

    try:
        response = requests.get(PLACES_FIND_URL, params=params, timeout=10)
        response.raise_for_status()
    except requests.RequestException as exc:
        return None, f"places_request_error: {exc}"

    payload = response.json()
    status = payload.get("status", "")
    if status != "OK":
        return None, f"places_status_{status.lower()}"

    candidates = payload.get("candidates", [])
    if not candidates:
        return None, "places_no_candidates"

    return candidates[0], "places_ok"


# --------------------------------------------------------------------------- #
# Google Custom Search 查詢（抓 Yelp 連結）
# --------------------------------------------------------------------------- #
def query_yelp_link(
    name: str,
    search_key: str,
    search_cx: str,
    location: str = DEFAULT_CITY,
) -> Tuple[Optional[str], str]:
    """透過 Google Custom Search 找出第一個 Yelp 連結。"""
    query = f'site:yelp.com "{name}" "{location}"'
    params = {
        "key": search_key,
        "cx": search_cx,
        "q": query,
        "num": 5,
    }

    try:
        response = requests.get(CUSTOM_SEARCH_URL, params=params, timeout=10)
        response.raise_for_status()
    except requests.RequestException as exc:
        return None, f"custom_search_error: {exc}"

    data = response.json()
    items = data.get("items", [])
    if not items:
        return None, "custom_search_no_results"

    for item in items:
        link = item.get("link")
        if link and "yelp.com" in link and "/biz/" in link:
            return link, "custom_search_found"

    return None, "custom_search_not_found"


# --------------------------------------------------------------------------- #
# 主流程
# --------------------------------------------------------------------------- #
def process_file(
    input_path: str,
    output_path: Optional[str],
    limit: Optional[int],
    places_key: str,
    search_key: str,
    search_cx: str,
) -> str:
    """處理 Excel，並回傳輸出檔案路徑。"""
    df = pd.read_excel(input_path)
    required_columns = {"Name", "Add"}
    missing_columns = required_columns - set(df.columns)
    if missing_columns:
        raise ValueError(f"Excel 缺少欄位：{', '.join(missing_columns)}")

    if limit:
        df = df.head(limit)

    results: List[Dict] = []
    for idx, row in df.iterrows():
        name = str(row["Name"]).strip()
        address = str(row["Add"]).strip()
        phone = str(row.get("Phone", "")).strip()

        LOGGER.info("(%d/%d) 處理：%s", idx + 1, len(df), name)

        if not name:
            results.append(
                {
                    "Name": name,
                    "Add": address,
                    "Phone": phone,
                    "Matched_Name": "",
                    "Matched_Address": "",
                    "Type_1": "",
                    "Type_2": "",
                    "Yelp_URL": "",
                    "Match_Status": "invalid_name",
                    "Message": "原始資料缺少餐廳名稱",
                }
            )
            continue

        candidate, status = query_place(name, places_key)
        if not candidate:
            LOGGER.warning("  無匹配結果（Google Places） - %s", status)
            results.append(
                {
                    "Name": name,
                    "Add": address,
                    "Phone": phone,
                    "Matched_Name": "",
                    "Matched_Address": "",
                    "Type_1": "",
                    "Type_2": "",
                    "Yelp_URL": "",
                    "Match_Status": "not_matched",
                    "Message": status,
                }
            )
            time.sleep(REQUEST_DELAY_SECONDS)
            continue

        matched_name = candidate.get("name", "")
        matched_address = candidate.get("formatted_address", "")
        matched_types = format_types(candidate.get("types", []))

        if not addresses_match(address, matched_address):
            LOGGER.warning("  地址比對失敗：%s <> %s", address, matched_address)
            results.append(
                {
                    "Name": name,
                    "Add": address,
                    "Phone": phone,
                    "Matched_Name": matched_name,
                    "Matched_Address": matched_address,
                    "Type_1": matched_types[0] if len(matched_types) > 0 else "",
                    "Type_2": matched_types[1] if len(matched_types) > 1 else "",
                    "Yelp_URL": "",
                    "Match_Status": "address_mismatch",
                    "Message": "地址比對未通過",
                }
            )
            time.sleep(REQUEST_DELAY_SECONDS)
            continue

        yelp_url, yelp_status = query_yelp_link(matched_name, search_key, search_cx)
        if yelp_url:
            LOGGER.info("  找到 Yelp 連結：%s", yelp_url)
        else:
            LOGGER.warning("  Yelp 連結未找到：%s", yelp_status)

        results.append(
            {
                "Name": name,
                "Add": address,
                "Phone": phone,
                "Matched_Name": matched_name,
                "Matched_Address": matched_address,
                "Type_1": matched_types[0] if len(matched_types) > 0 else "",
                "Type_2": matched_types[1] if len(matched_types) > 1 else "",
                "Yelp_URL": yelp_url or "",
                "Match_Status": "matched" if yelp_url else "matched_no_yelp",
                "Message": yelp_status,
            }
        )

        time.sleep(REQUEST_DELAY_SECONDS)

    result_df = pd.DataFrame(results)

    if output_path is None:
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        base_name, _ = os.path.splitext(os.path.basename(input_path))
        output_path = os.path.join(
            os.path.dirname(input_path), f"{base_name}_with_google_yelp_{timestamp}.xlsx"
        )

    result_df.to_excel(output_path, index=False)
    return output_path


# --------------------------------------------------------------------------- #
# 參數解析
# --------------------------------------------------------------------------- #
def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="根據餐廳 Excel 名單查詢 Google Places 類型與 Yelp 連結"
    )
    parser.add_argument("input", help="輸入 Excel 檔案路徑，需包含 Name / Add 欄位")
    parser.add_argument(
        "--output",
        help="輸出檔案路徑（含副檔名，例如 result.xlsx）；若未指定則自動產生",
    )
    parser.add_argument(
        "--limit",
        type=int,
        help="只處理前 N 筆資料（測試用）",
    )
    parser.add_argument(
        "--verbose",
        action="store_true",
        help="顯示除錯訊息",
    )
    return parser.parse_args()


def main() -> None:
    args = parse_args()
    setup_logging(verbose=args.verbose)

    try:
        places_key, search_key, search_cx = load_credentials()
    except RuntimeError as exc:
        LOGGER.error("%s", exc)
        sys.exit(1)

    try:
        output_path = process_file(
            input_path=args.input,
            output_path=args.output,
            limit=args.limit,
            places_key=places_key,
            search_key=search_key,
            search_cx=search_cx,
        )
    except Exception as exc:  # pylint: disable=broad-except
        LOGGER.exception("處理失敗：%s", exc)
        sys.exit(1)

    LOGGER.info("完成！結果寫入：%s", output_path)


if __name__ == "__main__":
    main()

