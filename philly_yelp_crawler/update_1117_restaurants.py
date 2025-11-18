#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
更新 2025-11-17 餐廳的官網、Yelp 連結和經緯度
- 用餐廳名稱 + Philadelphia 作為關鍵字
- 使用 Google Places API 找官網（寫入 D 欄：Google_Website）
- 使用 Google Custom Search API 找 Yelp link（寫入 E 欄：Yelp_URL）
- 使用 Google Geocoding API 根據地址找經緯度（寫入 F 欄：Latitude，G 欄：Longitude）
- 不理會現有資料，一律重新搜尋
"""

import os
import sys
import time
import requests
import pandas as pd
from pathlib import Path
from typing import Optional, Tuple
import logging

# 設定日誌
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('update_1117_restaurants.log', encoding='utf-8'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

# API 端點
GOOGLE_PLACES_BASE_URL = "https://maps.googleapis.com/maps/api/place"
GOOGLE_CUSTOM_SEARCH_URL = "https://www.googleapis.com/customsearch/v1"
GOOGLE_GEOCODE_URL = "https://maps.googleapis.com/maps/api/geocode/json"

# 輸入檔案
INPUT_FILE = Path(__file__).parent / "data" / "Philly BYOB Restaurant_with_websites_merged.xlsx"


def get_website_from_places(restaurant_name: str, api_key: str) -> Optional[str]:
    """
    使用 Google Places API 搜尋餐廳並取得官網
    
    Args:
        restaurant_name: 餐廳名稱
        api_key: Google Places API Key
        
    Returns:
        官網 URL，如果找不到則返回 None
    """
    query = f"{restaurant_name} Philadelphia"
    url = f"{GOOGLE_PLACES_BASE_URL}/textsearch/json"
    params = {
        'query': query,
        'key': api_key,
        'type': 'restaurant'
    }
    
    try:
        logger.info(f"搜尋 Google Places: {query}")
        time.sleep(0.3)  # 避免請求過快
        response = requests.get(url, params=params, timeout=10)
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('status') == 'OK':
                results = data.get('results', [])
                if results:
                    place_id = results[0].get('place_id')
                    if place_id:
                        # 取得 Place Details 來獲取 website
                        return get_place_details_website(place_id, api_key)
            elif data.get('status') == 'ZERO_RESULTS':
                logger.warning(f"Google Places 無結果: {query}")
            else:
                logger.warning(f"Google Places API 錯誤: {data.get('status')}")
    
    except Exception as e:
        logger.error(f"搜尋 Google Places 時發生錯誤: {str(e)}")
    
    return None


def get_place_details_website(place_id: str, api_key: str) -> Optional[str]:
    """
    使用 Place ID 取得餐廳的 website
    
    Args:
        place_id: Google Places ID
        api_key: Google Places API Key
        
    Returns:
        官網 URL，如果沒有則返回 None
    """
    url = f"{GOOGLE_PLACES_BASE_URL}/details/json"
    params = {
        'place_id': place_id,
        'fields': 'website',
        'key': api_key
    }
    
    try:
        time.sleep(0.3)  # 避免請求過快
        response = requests.get(url, params=params, timeout=10)
        
        if response.status_code == 200:
            data = response.json()
            if data.get('status') == 'OK':
                result = data.get('result', {})
                website = result.get('website')
                if website:
                    logger.info(f"找到官網: {website}")
                    return website
    
    except Exception as e:
        logger.error(f"取得 Place Details 時發生錯誤: {str(e)}")
    
    return None


def get_yelp_link(restaurant_name: str, api_key: str, cx: str) -> Optional[str]:
    """
    使用 Google Custom Search 搜尋 Yelp 連結
    
    Args:
        restaurant_name: 餐廳名稱
        api_key: Google Custom Search API Key
        cx: Custom Search Engine ID
        
    Returns:
        Yelp URL，如果找不到則返回 None
    """
    query = f'site:yelp.com "{restaurant_name}" "Philadelphia"'
    params = {
        "key": api_key,
        "cx": cx,
        "q": query,
        "num": 5
    }
    
    try:
        logger.info(f"搜尋 Yelp: {query}")
        time.sleep(0.3)  # 避免請求過快
        response = requests.get(GOOGLE_CUSTOM_SEARCH_URL, params=params, timeout=10)
        response.raise_for_status()
        
        data = response.json()
        items = data.get("items", [])
        
        for item in items:
            link = item.get("link", "")
            if link and "yelp.com" in link and "/biz/" in link:
                logger.info(f"找到 Yelp link: {link}")
                return link
        
        logger.warning(f"未找到 Yelp 連結: {restaurant_name}")
    
    except requests.RequestException as e:
        logger.error(f"搜尋 Yelp 時發生錯誤: {str(e)}")
    
    return None


def get_lat_lng_from_address(address: str, api_key: str) -> Tuple[Optional[float], Optional[float]]:
    """
    使用 Google Geocoding API 根據地址取得經緯度
    
    Args:
        address: 餐廳地址
        api_key: Google API Key
        
    Returns:
        (latitude, longitude) 元組，如果找不到則返回 (None, None)
    """
    if not address or not address.strip():
        logger.warning("地址為空，無法搜尋經緯度")
        return None, None
    
    # 確保地址包含 Philadelphia, PA
    if "Philadelphia" not in address:
        address = f"{address}, Philadelphia, PA"
    
    params = {
        "address": address,
        "key": api_key
    }
    
    try:
        logger.info(f"搜尋經緯度: {address}")
        time.sleep(0.3)  # 避免請求過快
        response = requests.get(GOOGLE_GEOCODE_URL, params=params, timeout=10)
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('status') == 'OK':
                results = data.get('results', [])
                if results:
                    location = results[0].get('geometry', {}).get('location', {})
                    lat = location.get('lat')
                    lng = location.get('lng')
                    
                    if lat and lng:
                        logger.info(f"找到經緯度: ({lat}, {lng})")
                        return lat, lng
            elif data.get('status') == 'ZERO_RESULTS':
                logger.warning(f"Geocoding API 無結果: {address}")
            else:
                logger.warning(f"Geocoding API 錯誤: {data.get('status')}")
    
    except Exception as e:
        logger.error(f"搜尋經緯度時發生錯誤: {str(e)}")
    
    return None, None


def process_restaurant(restaurant_name: str, address: str, places_api_key: str, 
                      custom_search_api_key: str, custom_search_cx: str) -> Tuple[Optional[str], Optional[str], Optional[float], Optional[float]]:
    """
    處理單一餐廳：搜尋官網、Yelp 連結和經緯度
    
    Returns:
        (website, yelp_link, latitude, longitude) 元組
    """
    logger.info(f"\n處理餐廳: {restaurant_name}")
    
    # 搜尋官網
    website = get_website_from_places(restaurant_name, places_api_key)
    
    # 搜尋 Yelp 連結
    yelp_link = get_yelp_link(restaurant_name, custom_search_api_key, custom_search_cx)
    
    # 搜尋經緯度
    lat, lng = get_lat_lng_from_address(address, places_api_key)
    
    return website, yelp_link, lat, lng


def main() -> int:
    """主程式"""
    # 檢查環境變數
    places_api_key = os.environ.get("GOOGLE_API_KEY")
    custom_search_api_key = os.environ.get("GOOGLE_CUSTOM_SEARCH_API_KEY")
    custom_search_cx = os.environ.get("GOOGLE_CUSTOM_SEARCH_CX")
    
    if not places_api_key:
        logger.error("❌ 缺少環境變數: GOOGLE_API_KEY")
        print("請設定 Google Places API Key: set GOOGLE_API_KEY=your_key", file=sys.stderr)
        return 1
    
    if not custom_search_api_key:
        logger.error("❌ 缺少環境變數: GOOGLE_CUSTOM_SEARCH_API_KEY")
        print("請設定 Google Custom Search API Key: set GOOGLE_CUSTOM_SEARCH_API_KEY=your_key", file=sys.stderr)
        return 1
    
    if not custom_search_cx:
        logger.error("❌ 缺少環境變數: GOOGLE_CUSTOM_SEARCH_CX")
        print("請設定 Custom Search Engine ID: set GOOGLE_CUSTOM_SEARCH_CX=your_cx", file=sys.stderr)
        return 1
    
    # 檢查檔案是否存在
    if not INPUT_FILE.exists():
        logger.error(f"❌ 找不到檔案: {INPUT_FILE}")
        print(f"檔案不存在: {INPUT_FILE}", file=sys.stderr)
        return 1
    
    # 讀取 Excel 檔案
    logger.info(f"讀取檔案: {INPUT_FILE}")
    try:
        df = pd.read_excel(INPUT_FILE)
    except Exception as e:
        logger.error(f"讀取 Excel 檔案失敗: {e}")
        print(f"讀取檔案失敗: {e}", file=sys.stderr)
        return 1
    
    # 檢查必要欄位
    required_columns = ['Name', 'Date', 'Add', 'Google_Website', 'Yelp_URL', 'Latitude', 'Longitude']
    missing_columns = [col for col in required_columns if col not in df.columns]
    if missing_columns:
        logger.error(f"Excel 檔案缺少必要欄位: {missing_columns}")
        print(f"缺少欄位: {missing_columns}", file=sys.stderr)
        return 1
    
    # 篩選 Date = 2025-11-17 的餐廳
    date_mask = df['Date'].astype(str).str.strip().str.contains('2025-11-17', na=False, regex=False)
    target_indices = df[date_mask].index.tolist()
    
    if not target_indices:
        logger.warning("未找到 Date = 2025-11-17 的餐廳")
        print("未找到目標餐廳", file=sys.stderr)
        return 1
    
    logger.info(f"找到 {len(target_indices)} 筆餐廳需要處理")
    
    # 處理每家餐廳
    success_count = 0
    website_count = 0
    yelp_count = 0
    latlng_count = 0
    
    for idx in target_indices:
        restaurant_name = str(df.at[idx, 'Name']).strip()
        address = str(df.at[idx, 'Add']).strip() if pd.notna(df.at[idx, 'Add']) else ''
        
        if not restaurant_name:
            logger.warning(f"第 {idx} 筆餐廳名稱為空，跳過")
            continue
        
        # 重新搜尋，不理會現有資料
        website, yelp_link, lat, lng = process_restaurant(
            restaurant_name,
            address,
            places_api_key,
            custom_search_api_key,
            custom_search_cx
        )
        
        # 更新資料（即使為 None 也要寫入，覆蓋舊資料）
        df.at[idx, 'Google_Website'] = website if website else ''
        df.at[idx, 'Yelp_URL'] = yelp_link if yelp_link else ''
        df.at[idx, 'Latitude'] = lat if lat is not None else ''
        df.at[idx, 'Longitude'] = lng if lng is not None else ''
        
        if website:
            website_count += 1
        if yelp_link:
            yelp_count += 1
        if lat is not None and lng is not None:
            latlng_count += 1
        if website or yelp_link or (lat is not None and lng is not None):
            success_count += 1
        
        logger.info(f"✅ {restaurant_name}: 官網={bool(website)}, Yelp={bool(yelp_link)}, 經緯度={bool(lat and lng)}")
    
    # 儲存結果（覆寫原檔案）
    try:
        with pd.ExcelWriter(INPUT_FILE, engine="openpyxl", mode="w") as writer:
            df.to_excel(writer, index=False)
        logger.info(f"\n結果已寫回: {INPUT_FILE}")
    except Exception as e:
        logger.error(f"儲存檔案失敗: {e}")
        print(f"儲存失敗: {e}", file=sys.stderr)
        return 1
    
    # 顯示統計結果
    logger.info("\n" + "="*60)
    logger.info("處理完成統計")
    logger.info("="*60)
    logger.info(f"處理餐廳數: {len(target_indices)}")
    logger.info(f"成功找到官網: {website_count}")
    logger.info(f"成功找到 Yelp: {yelp_count}")
    logger.info(f"成功找到經緯度: {latlng_count}")
    logger.info(f"至少找到一項: {success_count}")
    logger.info("="*60)
    
    print(f"\n✅ 更新完成！")
    print(f"   處理餐廳數: {len(target_indices)}")
    print(f"   找到官網: {website_count}")
    print(f"   找到 Yelp: {yelp_count}")
    print(f"   找到經緯度: {latlng_count}")
    print(f"   結果已寫回: {INPUT_FILE.name}")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

