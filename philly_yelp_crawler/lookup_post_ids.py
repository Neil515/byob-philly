#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
依據 Excel（需含 slug 欄位）批次查詢 WordPress 文章 ID，並將結果寫回同一份檔案。
會在 slug 欄後面新增 `WP_Post_ID` 欄位，內容為 REST API 回傳的 post id。

使用方式：
    python philly_yelp_crawler/lookup_post_ids.py
"""

import re
import sys
import time
from pathlib import Path
from typing import Optional

import pandas as pd
import requests

DEFAULT_EXCEL = Path(__file__).parent / "data" / "Philly BYOB Restaurant_with_websites_merged.xlsx"
API_ENDPOINT = "https://byobmap.com/wp-json/wp/v2/restaurant"
OUTPUT_COLUMN = "WP_Post_ID"
REQUEST_DELAY = 0.25  # 秒，避免觸發 rate limit
NAME_COLUMN = "Name"


def find_slug_column(df: pd.DataFrame) -> Optional[str]:
    """嘗試在欄位中找到 slug 欄位（不分大小寫）"""
    for column in df.columns:
        if str(column).strip().lower() == "slug":
            return column
    return None


def slugify(value: str) -> str:
    """把餐廳名稱轉成 WordPress 文章 slug"""
    if not value:
        return ""
    text = str(value).strip().lower()
    text = re.sub(r"[^a-z0-9\s-]", "", text)  # 移除非英數
    text = re.sub(r"[\s_-]+", "-", text)      # 空白/底線轉換為 -
    return text.strip("-")


def fetch_post_id(slug: str) -> Optional[int]:
    """透過 REST API 依 slug 取得文章 ID"""
    params = {"slug": slug, "per_page": 1}
    try:
        response = requests.get(API_ENDPOINT, params=params, timeout=15)
        response.raise_for_status()
        items = response.json()
        if items:
            return items[0].get("id")
    except requests.RequestException as exc:
        print(f"[WARN] slug={slug} API error: {exc}")
    return None


def main(excel_path: Path) -> None:
    if not excel_path.exists():
        raise FileNotFoundError(f"找不到 Excel 檔案：{excel_path}")

    df = pd.read_excel(excel_path)
    slug_column = find_slug_column(df)
    if not slug_column and NAME_COLUMN not in df.columns:
        raise ValueError("Excel 沒有 Slug 欄位，也找不到 Name 欄位，無法推算 slug")

    total = len(df)
    post_ids = []
    for idx, row in df.iterrows():
        if slug_column:
            slug_raw = row.get(slug_column, "")
            slug = str(slug_raw).strip().lower()
        else:
            slug = slugify(row.get(NAME_COLUMN, ""))

        if not slug or slug == "nan":
            post_ids.append(None)
            continue

        print(f"[{idx + 1}/{total}] 查詢 slug={slug}（{'slug欄' if slug_column else 'Name推算'}）")
        post_id = fetch_post_id(slug)
        post_ids.append(post_id)
        time.sleep(REQUEST_DELAY)

    # 先刪除舊有欄位，避免重複
    if OUTPUT_COLUMN in df.columns:
        df.drop(columns=[OUTPUT_COLUMN], inplace=True)

    insert_pos = 0  # 放到 A 欄
    df.insert(insert_pos, OUTPUT_COLUMN, post_ids)
    df.to_excel(excel_path, index=False)
    print(f"✅ 完成，結果寫入：{excel_path.name}")


if __name__ == "__main__":
    try:
        target = Path(sys.argv[1]) if len(sys.argv) > 1 else DEFAULT_EXCEL
        if not target.is_absolute():
            target = DEFAULT_EXCEL.parent / target
        main(target)
    except Exception as exc:  # pylint: disable=broad-except
        print(f"❌ 執行失敗：{exc}")
        sys.exit(1)

