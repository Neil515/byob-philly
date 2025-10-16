#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
費城 BYOB 餐廳 TripAdvisor 爬蟲
Philadelphia BYOB Restaurant TripAdvisor Crawler

使用 TripAdvisor 搜尋功能收集費城地區的 BYOB 餐廳資料
注意：此爬蟲使用網頁搜尋，需要遵守 TripAdvisor 的使用條款
"""

import requests
import json
import time
import pandas as pd
from datetime import datetime
from bs4 import BeautifulSoup
from tripadvisor_config import (
    TRIPADVISOR_BASE_URL, SEARCH_TERMS, OUTPUT_CSV, OUTPUT_JSON,
    REQUEST_DELAY, MAX_RETRIES, TIMEOUT
)

class TripAdvisorBYOBCrawler:
    def __init__(self):
        """初始化爬蟲"""
        self.base_url = TRIPADVISOR_BASE_URL
        self.restaurants = []
        self.seen_restaurants = set()  # 用於去重
        self.request_count = 0  # 追蹤請求次數
        
        # 設定請求標頭
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5',
            'Accept-Encoding': 'gzip, deflate',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
        }
        
    def search_restaurants(self, query):
        """
        搜尋餐廳
        
        Args:
            query (str): 搜尋關鍵字
            
        Returns:
            list: 餐廳資料列表
        """
        # 構建搜尋 URL
        search_url = f"{self.base_url}/Search"
        params = {
            'q': query,
            'geo': '60763',  # Philadelphia, PA 的 TripAdvisor 地理編碼
            'ssrc': 'A'
        }
        
        try:
            print(f"搜尋關鍵字: '{query}' (第 {self.request_count + 1} 次請求)")
            
            response = requests.get(
                search_url, 
                params=params, 
                headers=self.headers, 
                timeout=TIMEOUT
            )
            self.request_count += 1
            
            if response.status_code == 200:
                return self.parse_search_results(response.text, query)
            else:
                print(f"搜尋請求失敗: {response.status_code}")
                return []
                
        except Exception as e:
            print(f"搜尋過程中發生錯誤: {str(e)}")
            return []
    
    def parse_search_results(self, html_content, search_term):
        """
        解析搜尋結果頁面
        
        Args:
            html_content (str): HTML 內容
            search_term (str): 搜尋關鍵字
            
        Returns:
            list: 餐廳資料列表
        """
        restaurants = []
        
        try:
            soup = BeautifulSoup(html_content, 'html.parser')
            
            # 尋找餐廳結果容器
            # 注意：TripAdvisor 的 HTML 結構可能會變化，需要根據實際情況調整
            restaurant_elements = soup.find_all('div', class_='result')
            
            for element in restaurant_elements:
                restaurant_data = self.extract_restaurant_data(element, search_term)
                if restaurant_data:
                    restaurants.append(restaurant_data)
            
            print(f"找到 {len(restaurants)} 家餐廳")
            return restaurants
            
        except Exception as e:
            print(f"解析搜尋結果時發生錯誤: {str(e)}")
            return []
    
    def extract_restaurant_data(self, element, search_term):
        """
        從 HTML 元素中提取餐廳資料
        
        Args:
            element: BeautifulSoup 元素
            search_term (str): 搜尋關鍵字
            
        Returns:
            dict: 餐廳資料
        """
        try:
            # 提取餐廳名稱
            name_element = element.find('a', class_='result-title')
            name = name_element.get_text(strip=True) if name_element else 'Unknown'
            
            # 提取地址
            address_element = element.find('span', class_='address')
            address = address_element.get_text(strip=True) if address_element else ''
            
            # 提取評分
            rating_element = element.find('span', class_='rating')
            rating = float(rating_element.get('title', '0').split()[0]) if rating_element else 0
            
            # 提取評論數量
            review_element = element.find('span', class_='review-count')
            review_count = 0
            if review_element:
                review_text = review_element.get_text(strip=True)
                # 提取數字
                import re
                numbers = re.findall(r'\d+', review_text)
                review_count = int(numbers[0]) if numbers else 0
            
            # 提取連結
            link_element = element.find('a', class_='result-title')
            link = f"{self.base_url}{link_element.get('href')}" if link_element else ''
            
            # 建立唯一識別碼
            restaurant_id = f"{name}_{address}"
            
            # 檢查是否已存在
            if restaurant_id in self.seen_restaurants:
                return None
                
            self.seen_restaurants.add(restaurant_id)
            
            # 建立餐廳資料
            restaurant_data = {
                'restaurant_id': restaurant_id,
                'name': name,
                'rating': rating,
                'review_count': review_count,
                'address': address,
                'link': link,
                'search_term': search_term,
                'confidence_score': self.calculate_confidence(name, search_term),
                'crawled_at': datetime.now().isoformat(),
                'source': 'TripAdvisor'
            }
            
            return restaurant_data
            
        except Exception as e:
            print(f"提取餐廳資料時發生錯誤: {str(e)}")
            return None
    
    def calculate_confidence(self, name, search_term):
        """
        計算餐廳為 BYOB 的信心度
        
        Args:
            name (str): 餐廳名稱
            search_term (str): 搜尋關鍵字
            
        Returns:
            str: 信心度等級 (High/Medium/Low)
        """
        name_lower = name.lower()
        
        # 高信心度關鍵字
        high_confidence = ['byob', 'bring your own', 'corkage']
        
        # 中信心度關鍵字
        medium_confidence = ['byo']
        
        # 檢查信心度
        if any(keyword in name_lower for keyword in high_confidence):
            return 'High'
        elif any(keyword in name_lower for keyword in medium_confidence):
            return 'Medium'
        else:
            return 'Low'
    
    def crawl_all_terms(self):
        """執行所有搜尋關鍵字的爬取"""
        print("開始爬取費城 BYOB 餐廳資料 (TripAdvisor)...")
        print(f"將使用 {len(SEARCH_TERMS)} 個搜尋關鍵字")
        print("注意：此爬蟲使用網頁搜尋，請遵守 TripAdvisor 使用條款")
        print("=" * 50)
        
        for i, term in enumerate(SEARCH_TERMS, 1):
            print(f"\n[{i}/{len(SEARCH_TERMS)}] 處理關鍵字: '{term}'")
            
            # 搜尋餐廳
            restaurants = self.search_restaurants(term)
            
            # 處理每間餐廳
            for restaurant in restaurants:
                if restaurant:
                    self.restaurants.append(restaurant)
                    print(f"  新增: {restaurant['name']} (信心度: {restaurant['confidence_score']})")
            
            # 避免被阻擋，稍作休息
            if i < len(SEARCH_TERMS):
                print(f"  等待 {REQUEST_DELAY} 秒避免被阻擋...")
                time.sleep(REQUEST_DELAY)
        
        print(f"\n爬取完成！總共收集到 {len(self.restaurants)} 家不重複的餐廳")
        print(f"請求統計: 共使用 {self.request_count} 次請求")
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
    print("費城 BYOB 餐廳爬蟲 (TripAdvisor)")
    print("=" * 50)
    
    # 建立爬蟲實例
    crawler = TripAdvisorBYOBCrawler()
    
    # 執行爬取
    restaurants = crawler.crawl_all_terms()
    
    if restaurants:
        # 儲存資料
        crawler.save_to_csv()
        crawler.save_to_json()
        
        print("\n下一步建議:")
        print("1. 檢查 CSV 檔案中的資料品質")
        print("2. 手動驗證高信心度的餐廳")
        print("3. 與 Yelp 和 Google Places 資料進行交叉比對")
    else:
        print("沒有收集到任何餐廳資料，請檢查網路連線")

if __name__ == "__main__":
    main()
