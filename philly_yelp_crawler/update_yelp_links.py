#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
更新合併檔中 2025-11-16 的餐廳之 Yelp 連結
資料源：data/Philly BYOB Restaurant_with_websites_merged_20251116.xlsx
寫回：同一檔案（覆寫），在 Google_Website 右側插入/排序 Yelp_URL 欄位
查詢方式：Google Custom Search，query = site:yelp.com "<Name>" "Philadelphia"
環境變數：
  - GOOGLE_CUSTOM_SEARCH_API_KEY
  - GOOGLE_CUSTOM_SEARCH_CX
"""

import os
import sys
import time
import requests
import pandas as pd
from pathlib import Path
from typing import Optional

SEARCH_URL = "https://www.googleapis.com/customsearch/v1"
INPUT_FILE = Path(__file__).parent / "data" / "Philly BYOB Restaurant_with_websites_merged_20251116.xlsx"


def find_yelp_link(name: str, api_key: str, cx: str) -> Optional[str]:
	"""使用 Google Custom Search 搜尋 Yelp 連結（第一個 /biz/ 結果）"""
	if not name:
		return None
	q = f'site:yelp.com "{name}" "Philadelphia"'
	params = {"key": api_key, "cx": cx, "q": q, "num": 5}
	try:
		resp = requests.get(SEARCH_URL, params=params, timeout=10)
		resp.raise_for_status()
		data = resp.json()
		for item in (data.get("items") or []):
			link = item.get("link")
			if link and "yelp.com" in link and "/biz/" in link:
				return link
	except requests.RequestException:
		return None
	return None


def main() -> int:
	api_key = os.environ.get("GOOGLE_CUSTOM_SEARCH_API_KEY")
	cx = os.environ.get("GOOGLE_CUSTOM_SEARCH_CX")
	if not api_key or not cx:
		print("❌ 缺少 GOOGLE_CUSTOM_SEARCH_API_KEY 或 GOOGLE_CUSTOM_SEARCH_CX", file=sys.stderr)
		return 1

	if not INPUT_FILE.exists():
		print(f"❌ 找不到檔案：{INPUT_FILE}", file=sys.stderr)
		return 1

	df = pd.read_excel(INPUT_FILE)

	# 準備 Yelp_URL 欄位
	if "Yelp_URL" not in df.columns:
		df["Yelp_URL"] = ""

	# 僅處理 2025-11-16 的資料列
	mask = df["Date"].astype(str).str.strip().isin(["2025-11-16", "2025/11/16", "2025.11.16"])
	target_indices = df[mask].index.tolist()

	updated = 0
	samples = []

	for idx in target_indices:
		name = str(df.at[idx, "Name"]).strip() if "Name" in df.columns else ""
		if not name:
			continue
		# 已有 Yelp_URL 則跳過
		current = str(df.at[idx, "Yelp_URL"]).strip()
		if current:
			continue
		link = find_yelp_link(name, api_key, cx)
		if link:
			df.at[idx, "Yelp_URL"] = link
			updated += 1
			if len(samples) < 5:
				samples.append((name, link))
		# 避免頻率過快（保險）
		time.sleep(0.2)

	# 將 Yelp_URL 欄位移到 Google_Website 右側
	cols = list(df.columns)
	if "Google_Website" in cols and "Yelp_URL" in cols:
		gw_idx = cols.index("Google_Website")
		cols.remove("Yelp_URL")
		cols.insert(gw_idx + 1, "Yelp_URL")
		df = df.reindex(columns=cols)

	# 覆寫同檔
	with pd.ExcelWriter(INPUT_FILE, engine="openpyxl", mode="w") as writer:
		df.to_excel(writer, index=False)

	print(f"✅ 更新完成，寫回：{INPUT_FILE.name}")
	print(f"   11/16 列數：{len(target_indices)}，成功寫入 Yelp_URL：{updated}")
	if samples:
		print("   範例（最多5筆）：")
		for n, l in samples:
			print(f"   - {n} -> {l}")

	return 0


if __name__ == "__main__":
	sys.exit(main())


