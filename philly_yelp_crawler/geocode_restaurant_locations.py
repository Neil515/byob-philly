import argparse
import os
import sys
import time
from pathlib import Path
from typing import Dict, Optional, Tuple

import pandas as pd
import requests
from dotenv import load_dotenv


def load_api_key() -> str:
    env_path = Path(__file__).resolve().parents[1] / ".env"
    load_dotenv(env_path)
    api_key = os.getenv("GOOGLE_API_KEY") or os.getenv("GOOGLE_PLACES_API_KEY")
    if not api_key:
        raise RuntimeError(
            "無法載入 Google API Key，請在 .env 設定 GOOGLE_API_KEY 或 GOOGLE_PLACES_API_KEY"
        )
    return api_key


GOOGLE_API_KEY = load_api_key()
PLACES_SEARCH_URL = "https://maps.googleapis.com/maps/api/place/textsearch/json"
GEOCODE_URL = "https://maps.googleapis.com/maps/api/geocode/json"
REQUEST_SLEEP_SECONDS = 0.25  # 控制呼叫速率，避免超出額度

# 欄位設定
NAME_COLUMN_OPTIONS = ("Name", "Restaurant Name", "餐廳名稱")
ADDRESS_COLUMN_OPTIONS = ("Address", "Add", "地址")
LAT_COLUMN = "Latitude"
LNG_COLUMN = "Longitude"
STATUS_COLUMN = "Geocode_Status"
MATCHED_ADDRESS_COLUMN = "Matched_Address"


def _clean_value(value: Optional[str]) -> str:
    if pd.isna(value):
        return ""
    return str(value).strip()


def geocode_with_places(session: requests.Session, query: str) -> Optional[dict]:
    response = session.get(
        PLACES_SEARCH_URL,
        params={
            "query": query,
            "key": GOOGLE_API_KEY,
            "language": "en",
        },
        timeout=15,
    )
    response.raise_for_status()
    data = response.json()

    if data.get("status") != "OK":
        return None

    results = data.get("results", [])
    return results[0] if results else None


def geocode_with_geocode_api(session: requests.Session, address: str) -> Optional[dict]:
    response = session.get(
        GEOCODE_URL,
        params={
            "address": address,
            "key": GOOGLE_API_KEY,
            "language": "en",
        },
        timeout=15,
    )
    response.raise_for_status()
    data = response.json()

    if data.get("status") != "OK":
        return None

    results = data.get("results", [])
    return results[0] if results else None


def extract_lat_lng(result: Optional[dict]) -> Tuple[Optional[float], Optional[float], Optional[str]]:
    if not result:
        return None, None, None
    geometry = result.get("geometry", {})
    location = geometry.get("location", {})
    lat = location.get("lat")
    lng = location.get("lng")
    formatted_address = result.get("formatted_address")
    return lat, lng, formatted_address


def geocode_restaurant(
    session: requests.Session,
    name: str,
    address: str,
    cache: Dict[str, Tuple[Optional[float], Optional[float], str, Optional[str]]],
) -> Tuple[Optional[float], Optional[float], str, Optional[str]]:
    cache_key = f"{name}|{address}"
    if cache_key in cache:
        return cache[cache_key]

    query = f"{name} {address} Philadelphia, PA"
    status = "FAILED"
    matched_address = None
    lat = lng = None

    try:
        result = geocode_with_places(session, query)
        lat, lng, matched_address = extract_lat_lng(result)
        if lat is None or lng is None:
            # fallback to Geocoding API
            result = geocode_with_geocode_api(session, f"{name}, {address}, Philadelphia, PA")
            lat, lng, matched_address = extract_lat_lng(result)
        if lat is not None and lng is not None:
            status = "OK"
    except requests.HTTPError as http_error:
        status = f"HTTP_ERROR:{http_error.response.status_code}"
    except Exception as exc:  # pragma: no cover - 網路或其他未知錯誤
        status = f"ERROR:{exc.__class__.__name__}"

    cache[cache_key] = (lat, lng, status, matched_address)
    return cache[cache_key]


def ensure_columns(df: pd.DataFrame) -> None:
    for column in (LAT_COLUMN, LNG_COLUMN, STATUS_COLUMN, MATCHED_ADDRESS_COLUMN):
        if column not in df.columns:
            df[column] = ""


def resolve_column(df: pd.DataFrame, candidates: Tuple[str, ...], column_desc: str) -> str:
    for candidate in candidates:
        if candidate in df.columns:
            return candidate
    raise ValueError(f"缺少必要欄位：{column_desc}（可接受：{', '.join(candidates)}）")


def process_file(path: Path, output_dir: Path, cache: Dict[str, Tuple[Optional[float], Optional[float], str, Optional[str]]]) -> dict:
    df = pd.read_excel(path)
    name_column = resolve_column(df, NAME_COLUMN_OPTIONS, "名稱")
    address_column = resolve_column(df, ADDRESS_COLUMN_OPTIONS, "地址")

    ensure_columns(df)

    session = requests.Session()
    success_count = 0
    failure_count = 0

    for idx, row in df.iterrows():
        name = _clean_value(row.get(name_column))
        address = _clean_value(row.get(address_column))

        if not name or not address:
            df.at[idx, LAT_COLUMN] = ""
            df.at[idx, LNG_COLUMN] = ""
            df.at[idx, STATUS_COLUMN] = "FAILED_MISSING_DATA"
            df.at[idx, MATCHED_ADDRESS_COLUMN] = ""
            failure_count += 1
            continue

        lat, lng, status, matched_address = geocode_restaurant(session, name, address, cache)

        df.at[idx, LAT_COLUMN] = lat if lat is not None else ""
        df.at[idx, LNG_COLUMN] = lng if lng is not None else ""
        df.at[idx, STATUS_COLUMN] = status
        df.at[idx, MATCHED_ADDRESS_COLUMN] = matched_address or ""

        if status == "OK":
            success_count += 1
        else:
            failure_count += 1

        time.sleep(REQUEST_SLEEP_SECONDS)

    output_dir.mkdir(parents=True, exist_ok=True)
    output_path = output_dir / f"{path.stem}_with_latlng.xlsx"
    df.to_excel(output_path, index=False)

    failure_report_path = output_dir / f"{path.stem}_geocode_failed.xlsx"
    failed_df = df[df[STATUS_COLUMN] != "OK"]
    failed_df.to_excel(failure_report_path, index=False)

    return {
        "total": len(df),
        "success": success_count,
        "failed": failure_count,
        "output_path": output_path,
        "failure_path": failure_report_path,
    }


def parse_arguments() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="為餐廳 Excel 檔產生經緯度欄位")
    parser.add_argument(
        "--files",
        nargs="+",
        default=[
            "data/Philly BYOB Restaurant.xlsx",
            "data/Philly BYOB Restaurant google form.xlsx",
        ],
        help="要處理的 Excel 檔案路徑",
    )
    parser.add_argument(
        "--output-dir",
        default="data",
        help="輸出檔案目錄",
    )
    return parser.parse_args()


def main() -> None:
    args = parse_arguments()
    output_dir = Path(args.output_dir)
    cache: Dict[str, Tuple[Optional[float], Optional[float], str, Optional[str]]] = {}

    summaries = []
    for file_path in args.files:
        path = Path(file_path)
        if not path.exists():
            print(f"[SKIP] 找不到檔案：{path}", file=sys.stderr)
            continue

        print(f"[INFO] 處理檔案：{path.name}")
        summary = process_file(path, output_dir, cache)
        summaries.append((path.name, summary))
        print(
            f"  → 成功：{summary['success']} / 失敗：{summary['failed']} / 輸出：{summary['output_path'].name}"
        )

    print("\n[SUMMARY]")
    for name, summary in summaries:
        print(
            f"  {name}: total={summary['total']}, success={summary['success']}, failed={summary['failed']}"
        )


if __name__ == "__main__":
    main()

