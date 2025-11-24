#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
讀取指定 Excel 檔案，依據 WP_Post_ID / Latitude_New / Longitude_New 欄位，
透過 WordPress ACF REST API 批次更新餐廳文章的經緯度欄位。

必要環境變數：
    WP_API_BASE_URL   - 例如 https://byobmap.com/wp-json/acf/v3/restaurants
    WP_API_USERNAME   - WordPress 帳號或 Application Password 的使用者
    WP_API_PASSWORD   - 對應密碼 / Application Password

可選環境變數：
    WP_LAT_FIELD_NAME - 預設 latitude
    WP_LNG_FIELD_NAME - 預設 longitude

使用方式：
    python philly_yelp_crawler/push_acf_latlng.py <excel_path>
"""

import os
import sys
import time
from pathlib import Path
from typing import Optional

import pandas as pd
import requests
from dotenv import load_dotenv

DEFAULT_LAT_COL = "Latitude_New"
DEFAULT_LNG_COL = "Longitude_New"
DEFAULT_POST_COL = "WP_Post_ID"
DEFAULT_API_BASE = "https://byobmap.com/wp-json/acf/v3/restaurants"
RATE_DELAY = 0.3  # 避免打太快


def load_env() -> dict:
    """載入 .env 並回傳 API 設定"""
    env_path = Path(__file__).resolve().parents[1] / ".env"
    if env_path.exists():
        load_dotenv(env_path)
    else:
        load_dotenv(Path(__file__).parent / ".env")

    config = {
        "api_base": os.getenv("WP_API_BASE_URL", DEFAULT_API_BASE),
        "username": os.getenv("WP_API_USERNAME"),
        "password": os.getenv("WP_API_PASSWORD"),
        "lat_field": os.getenv("WP_LAT_FIELD_NAME", "latitude"),
        "lng_field": os.getenv("WP_LNG_FIELD_NAME", "longitude"),
    }

    missing = [k for k in ("username", "password") if not config[k]]
    if missing:
        raise RuntimeError(
            f"缺少環境變數：{', '.join('WP_API_' + name.upper() for name in missing)}"
        )
    return config


def normalize_float(value) -> Optional[float]:
    """將欄位值轉成 float，無法轉換則回傳 None"""
    try:
        if pd.isna(value):
            return None
        value = float(value)
        return value
    except (TypeError, ValueError):
        return None


def update_acf_latlng(
    base_url: str,
    username: str,
    password: str,
    lat_field: str,
    lng_field: str,
    post_id: int,
    lat: float,
    lng: float,
) -> None:
    """呼叫 WordPress ACF REST API 更新經緯度"""
    url = f"{base_url.rstrip('/')}/{post_id}"
    payload = {"fields": {lat_field: lat, lng_field: lng}}
    response = requests.post(url, auth=(username, password), json=payload, timeout=20)
    try:
        response.raise_for_status()
    except requests.HTTPError as exc:
        detail = response.text[:200]
        raise RuntimeError(f"API 更新失敗（post_id={post_id}）：{detail}") from exc


def main() -> None:
    if len(sys.argv) < 2:
        print("用法：python push_acf_latlng.py <excel_path> [post_col lat_col lng_col]")
        sys.exit(1)

    excel_path = Path(sys.argv[1])
    if not excel_path.is_absolute():
        excel_path = Path(__file__).parent / excel_path

    post_col = sys.argv[2] if len(sys.argv) > 2 else DEFAULT_POST_COL
    lat_col = sys.argv[3] if len(sys.argv) > 3 else DEFAULT_LAT_COL
    lng_col = sys.argv[4] if len(sys.argv) > 4 else DEFAULT_LNG_COL

    if not excel_path.exists():
        raise FileNotFoundError(f"找不到 Excel：{excel_path}")

    config = load_env()
    df = pd.read_excel(excel_path)
    missing_cols = [col for col in (post_col, lat_col, lng_col) if col not in df.columns]
    if missing_cols:
        raise ValueError(f"Excel 缺少欄位：{', '.join(missing_cols)}")

    total = len(df)
    success = 0
    skipped = 0
    for idx, row in df.iterrows():
        post_id = row.get(post_col)
        lat = normalize_float(row.get(lat_col))
        lng = normalize_float(row.get(lng_col))

        if pd.isna(post_id) or post_id in (None, ""):
            print(f"[{idx+1}/{total}] 缺少 post id，跳過")
            skipped += 1
            continue

        if lat is None or lng is None:
            print(f"[{idx+1}/{total}] post_id={post_id} 缺少經緯度，跳過")
            skipped += 1
            continue

        post_id_int = int(post_id)
        print(f"[{idx+1}/{total}] 更新 post_id={post_id_int} -> ({lat}, {lng})")
        update_acf_latlng(
            base_url=config["api_base"],
            username=config["username"],
            password=config["password"],
            lat_field=config["lat_field"],
            lng_field=config["lng_field"],
            post_id=post_id_int,
            lat=lat,
            lng=lng,
        )
        success += 1
        time.sleep(RATE_DELAY)

    print(
        f"✅ 完成。成功 {success} 筆，跳過 {skipped} 筆。"
        f" 檔案：{excel_path.name}"
    )


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:  # pylint: disable=broad-except
        print(f"❌ 執行失敗：{exc}")
        sys.exit(1)

