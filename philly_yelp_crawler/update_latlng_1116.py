#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
從合併檔（with_websites_merged_20251116）挑出 Date=2025-11-16 的餐廳，
用地址（搭配名稱與城市）查找經緯度，將 Latitude/Longitude 兩欄插入在 Yelp_URL 的右側，
覆寫同一個 Excel，不新建檔案。

需求環境變數：
  - GOOGLE_API_KEY 或 GOOGLE_PLACES_API_KEY
"""

import os
import sys
import time
import requests
import pandas as pd
from pathlib import Path
from typing import Optional, Tuple

PLACES_SEARCH_URL = "https://maps.googleapis.com/maps/api/place/textsearch/json"
GEOCODE_URL = "https://maps.googleapis.com/maps/api/geocode/json"
REQUEST_SLEEP_SECONDS = 0.25

INPUT_FILE = Path(__file__).parent / "data" / "Philly BYOB Restaurant_with_websites_merged_20251116.xlsx"


def load_api_key() -> str:
	api_key = os.getenv("GOOGLE_API_KEY") or os.getenv("GOOGLE_PLACES_API_KEY")
	if not api_key:
		raise RuntimeError("缺少 GOOGLE_API_KEY/GOOGLE_PLACES_API_KEY")
	return api_key


def textsearch(session: requests.Session, key: str, query: str) -> Optional[dict]:
	r = session.get(
		PLACES_SEARCH_URL,
		params={"query": query, "key": key, "language": "en"},
		timeout=15,
	)
	r.raise_for_status()
	data = r.json()
	if data.get("status") != "OK":
		return None
	results = data.get("results", [])
	return results[0] if results else None


def geocode(session: requests.Session, key: str, address: str) -> Optional[dict]:
	r = session.get(
		GEOCODE_URL,
		params={"address": address, "key": key, "language": "en"},
		timeout=15,
	)
	r.raise_for_status()
	data = r.json()
	if data.get("status") != "OK":
		return None
	results = data.get("results", [])
	return results[0] if results else None


def extract_latlng(result: Optional[dict]) -> Tuple[Optional[float], Optional[float]]:
	if not result:
		return None, None
	loc = (result.get("geometry") or {}).get("location") or {}
	return loc.get("lat"), loc.get("lng")


def main() -> int:
	api_key = load_api_key()
	if not INPUT_FILE.exists():
		print(f"❌ 找不到檔案：{INPUT_FILE}", file=sys.stderr)
		return 1

	df = pd.read_excel(INPUT_FILE)

	# 必要欄位
	if "Date" not in df.columns:
		print("❌ 檔案缺少 Date 欄位", file=sys.stderr)
		return 1
	if "Name" not in df.columns:
		print("❌ 檔案缺少 Name 欄位", file=sys.stderr)
		return 1
	# 地址欄位名稱兼容
	addr_col = "Add" if "Add" in df.columns else ("Address" if "Address" in df.columns else None)
	if not addr_col:
		print("❌ 檔案缺少地址欄位（Add/Address）", file=sys.stderr)
		return 1

	# Yelp 欄位存在性
	if "Yelp_URL" not in df.columns:
		df["Yelp_URL"] = ""

	# 經緯度欄位
	if "Latitude" not in df.columns:
		df["Latitude"] = ""
	if "Longitude" not in df.columns:
		df["Longitude"] = ""

	# 僅處理 2025-11-16
	mask = df["Date"].astype(str).str.strip().isin(["2025-11-16", "2025/11/16", "2025.11.16"])
	target_idx = df[mask].index.tolist()

	session = requests.Session()
	updated = 0

	for i in target_idx:
		name = str(df.at[i, "Name"]).strip()
		address = str(df.at[i, addr_col]).strip()
		if not name or not address:
			continue

		# 跳過已存在經緯度者
		if str(df.at[i, "Latitude"]).strip() and str(df.at[i, "Longitude"]).strip():
			continue

		lat = lng = None
		# 先用 Places TextSearch（姓名 + 地址 + 城市）
		query = f"{name} {address} Philadelphia, PA"
		try:
			res = textsearch(session, api_key, query)
			lat, lng = extract_latlng(res)
			if lat is None or lng is None:
				# fallback: Geocoding API
				res = geocode(session, api_key, f"{name}, {address}, Philadelphia, PA")
				lat, lng = extract_latlng(res)
		except requests.RequestException:
			pass

		if lat is not None and lng is not None:
			df.at[i, "Latitude"] = lat
			df.at[i, "Longitude"] = lng
			updated += 1

		time.sleep(REQUEST_SLEEP_SECONDS)

	# 將 Latitude/Longitude 欄位移到 Yelp_URL 右側
	cols = list(df.columns)
	if "Yelp_URL" in cols and "Latitude" in cols and "Longitude" in cols:
		yidx = cols.index("Yelp_URL")
		for col in ("Latitude", "Longitude"):
			if col in cols:
				cols.remove(col)
		cols.insert(yidx + 1, "Latitude")
		cols.insert(yidx + 2, "Longitude")
		df = df.reindex(columns=cols)

	# 覆寫同檔
	with pd.ExcelWriter(INPUT_FILE, engine="openpyxl", mode="w") as writer:
		df.to_excel(writer, index=False)

	print(f"✅ Lat/Lng 更新完成：{updated} 筆，已寫回 {INPUT_FILE.name}")
	return 0


if __name__ == "__main__":
	sys.exit(main())


