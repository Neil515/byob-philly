#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
費城 BYOB 餐廳 Google Places API 爬蟲
Philadelphia BYOB Restaurant Google Places Crawler

使用 Google Places API 收集費城地區的 BYOB 餐廳資料
"""

import requests
import json
import time
import pandas as pd
from datetime import datetime
from google_config import (
    GOOGLE_API_KEY, GOOGLE_PLACES_BASE_URL, GOOGLE_PLACES_SEARCH_ENDPOINT,
    DEFAULT_LOCATION, DEFAULT_RADIUS, DEFAULT_LIMIT,
    SEARCH_TERMS, OUTPUT_CSV, OUTPUT_JSON,
    DAILY_REQUEST_LIMIT, MONTHLY_REQUEST_LIMIT, SAFETY_BUFFER
)

class GooglePlacesBYOBCrawler:
    def __init__(self):
        """初始化爬蟲"""
        self.api_key = GOOGLE_API_KEY
        self.base_url = GOOGLE_PLACES_BASE_URL
        self.search_endpoint = GOOGLE_PLACES_SEARCH_ENDPOINT
        self.restaurants = []
        self.seen_restaurants = set()  # 用於去重
        self.request_count = 0  # 追蹤 API 請求次數
        self.daily_limit = DAILY_REQUEST_LIMIT - SAFETY_BUFFER  # 實際每日限制
        self.monthly_limit = MONTHLY_REQUEST_LIMIT - SAFETY_BUFFER  # 實際每月限制
        
    def check_request_limit(self):
        """
        檢查是否超過 API 請求限制
        
        Returns:
            bool: True 表示可以繼續請求，False 表示已達限制
        """
        if self.request_count >= self.daily_limit:
            print(f"已達到每日請求限制 ({self.daily_limit} 次)")
            print(f"今日已使用: {self.request_count} 次")
            print("請明天再試，或考慮升級 API 方案")
            return False
        
        if self.request_count >= self.monthly_limit:
            print(f"已達到每月請求限制 ({self.monthly_limit} 次)")
            print(f"本月已使用: {self.request_count} 次")
            print("請下個月再試，或考慮升級 API 方案")
            return False
        
        return True
    
    def search_restaurants(self, query, location=DEFAULT_LOCATION, radius=DEFAULT_RADIUS):
        """
        搜尋餐廳
        
        Args:
            query (str): 搜尋關鍵字
            location (str): 搜尋地點
            radius (int): 搜尋半徑（公尺）
            
        Returns:
            list: 餐廳資料列表
        """
        url = f"{self.base_url}{self.search_endpoint}"
        params = {
            'query': query,
            'location': location,
            'radius': radius,
            'key': self.api_key,
            'type': 'restaurant'
        }
        
        try:
            # 檢查請求限制
            if not self.check_request_limit():
                return []
            
            print(f"搜尋關鍵字: '{query}' (第 {self.request_count + 1} 次請求)")
            response = requests.get(url, params=params)
            self.request_count += 1  # 增加請求計數
            
            if response.status_code == 200:
                data = response.json()
                results = data.get('results', [])
                print(f"找到 {len(results)} 家餐廳")
                return results
            else:
                print(f"API 請求失敗: {response.status_code}")
                print(f"錯誤訊息: {response.text}")
                return []
                
        except Exception as e:
            print(f"搜尋過程中發生錯誤: {str(e)}")
            return []
    
    def get_place_details(self, place_id):
        """
        獲取餐廳詳細資訊
        
        Args:
            place_id (str): Google Places ID
            
        Returns:
            dict: 餐廳詳細資訊
        """
        url = f"{self.base_url}/details/json"
        params = {
            'place_id': place_id,
            'fields': 'name,rating,user_ratings_total,formatted_address,formatted_phone_number,website,types,reviews',
            'key': self.api_key
        }
        
        try:
            if not self.check_request_limit():
                return None
            
            print(f"獲取詳細資訊: {place_id} (第 {self.request_count + 1} 次請求)")
            response = requests.get(url, params=params)
            self.request_count += 1
            
            if response.status_code == 200:
                data = response.json()
                return data.get('result', {})
            else:
                print(f"詳細資訊請求失敗: {response.status_code}")
                return None
                
        except Exception as e:
            print(f"獲取詳細資訊時發生錯誤: {str(e)}")
            return None
    
    def process_restaurant(self, restaurant, search_term):
        """
        處理單一餐廳資料
        
        Args:
            restaurant (dict): 餐廳原始資料
            search_term (str): 搜尋關鍵字
            
        Returns:
            dict: 處理後的餐廳資料
        """
        # 建立唯一識別碼（名稱+地址）
        restaurant_id = f"{restaurant.get('name', '')}_{restaurant.get('formatted_address', '')}"
        
        # 檢查是否已存在
        if restaurant_id in self.seen_restaurants:
            return None
            
        self.seen_restaurants.add(restaurant_id)
        
        # 提取所需資料
        processed = {
            'restaurant_id': restaurant.get('place_id', ''),
            'name': restaurant.get('name', ''),
            'rating': restaurant.get('rating', 0),
            'review_count': restaurant.get('user_ratings_total', 0),
            'address': restaurant.get('formatted_address', ''),
            'phone': restaurant.get('formatted_phone_number', ''),
            'website': restaurant.get('website', ''),
            'types': restaurant.get('types', []),
            'latitude': restaurant.get('geometry', {}).get('location', {}).get('lat', 0),
            'longitude': restaurant.get('geometry', {}).get('location', {}).get('lng', 0),
            'search_term': search_term,
            'confidence_score': self.calculate_confidence(restaurant, search_term),
            'crawled_at': datetime.now().isoformat(),
            'source': 'Google Places'
        }
        
        return processed
    
    def calculate_confidence(self, restaurant, search_term):
        """
        計算餐廳為 BYOB 的信心度
        
        Args:
            restaurant (dict): 餐廳資料
            search_term (str): 搜尋關鍵字
            
        Returns:
            str: 信心度等級 (High/Medium/Low)
        """
        name = restaurant.get('name', '').lower()
        types = [t.lower() for t in restaurant.get('types', [])]
        all_text = f"{name} {' '.join(types)}".lower()
        
        # 高信心度關鍵字
        high_confidence = ['byob', 'bring your own', 'corkage']
        
        # 中信心度關鍵字
        medium_confidence = ['byo']
        
        # 檢查信心度
        if any(keyword in all_text for keyword in high_confidence):
            return 'High'
        elif any(keyword in all_text for keyword in medium_confidence):
            return 'Medium'
        else:
            return 'Low'
    
    def crawl_all_terms(self):
        """執行所有搜尋關鍵字的爬取"""
        print("開始爬取費城 BYOB 餐廳資料 (Google Places)...")
        print(f"將使用 {len(SEARCH_TERMS)} 個搜尋關鍵字")
        print(f"API 限制: 每日最多 {self.daily_limit} 次請求 (安全緩衝: {SAFETY_BUFFER} 次)")
        print("=" * 50)
        
        for i, term in enumerate(SEARCH_TERMS, 1):
            print(f"\n[{i}/{len(SEARCH_TERMS)}] 處理關鍵字: '{term}'")
            
            # 搜尋餐廳
            restaurants = self.search_restaurants(term)
            
            # 處理每間餐廳
            for restaurant in restaurants:
                processed = self.process_restaurant(restaurant, term)
                if processed:
                    self.restaurants.append(processed)
                    print(f"  新增: {processed['name']} (信心度: {processed['confidence_score']})")
                else:
                    print(f"  跳過重複: {restaurant.get('name', 'Unknown')}")
            
            # 避免 API 限制，稍作休息
            if i < len(SEARCH_TERMS):
                print("  等待 1 秒避免 API 限制...")
                time.sleep(1)
        
        print(f"\n爬取完成！總共收集到 {len(self.restaurants)} 家不重複的餐廳")
        print(f"API 使用統計: 共使用 {self.request_count} 次請求")
        print(f"剩餘額度: 每日 {self.daily_limit - self.request_count} 次，每月 {self.monthly_limit - self.request_count} 次")
        return self.restaurants
    
    def save_to_csv(self, filename=OUTPUT_CSV):
        """儲存資料到 CSV 檔案"""
        if not self.restaurants:
            print("沒有資料可儲存")
            return
        
        df = pd.DataFrame(self.restaurants)
        df.to_csv(filename, index=False, encoding='utf-8-sig')
        print(f"資料已儲存到: {filename}")
        
        # 顯示統計資訊
        print(f"統計資訊:")
        print(f"  - 總餐廳數: {len(self.restaurants)}")
        print(f"  - 高信心度: {len(df[df['confidence_score'] == 'High'])}")
        print(f"  - 中信心度: {len(df[df['confidence_score'] == 'Medium'])}")
        print(f"  - 低信心度: {len(df[df['confidence_score'] == 'Low'])}")
    
    def save_to_json(self, filename=OUTPUT_JSON):
        """儲存資料到 JSON 檔案"""
        if not self.restaurants:
            print("沒有資料可儲存")
            return
        
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(self.restaurants, f, ensure_ascii=False, indent=2)
        print(f"資料已儲存到: {filename}")

def main():
    """主程式"""
    print("費城 BYOB 餐廳爬蟲 (Google Places)")
    print("=" * 50)
    
    # 檢查 API Key
    if GOOGLE_API_KEY == "YOUR_GOOGLE_API_KEY_HERE":
        print("請先在 google_config.py 中設定你的 Google Places API Key")
        return
    
    # 建立爬蟲實例
    crawler = GooglePlacesBYOBCrawler()
    
    # 執行爬取
    restaurants = crawler.crawl_all_terms()
    
    if restaurants:
        # 儲存資料
        crawler.save_to_csv()
        crawler.save_to_json()
        
        print("\n下一步建議:")
        print("1. 檢查 CSV 檔案中的資料品質")
        print("2. 手動驗證高信心度的餐廳")
        print("3. 與 Yelp 資料進行交叉比對")
    else:
        print("沒有收集到任何餐廳資料，請檢查 API Key 和網路連線")

if __name__ == "__main__":
    main()
