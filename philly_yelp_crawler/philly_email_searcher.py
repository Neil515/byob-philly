#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
費城餐廳 Email 搜尋工具 - 第一階段：取得 Website
Philadelphia Restaurant Email Searcher - Phase 1: Get Website

從 Excel 檔案讀取餐廳資料，使用 Google Places API 搜尋並取得 website
"""

import pandas as pd
import requests
import time
import random
import logging
from datetime import datetime
from pathlib import Path
from google_config import (
    GOOGLE_API_KEY,
    GOOGLE_PLACES_BASE_URL,
    DAILY_REQUEST_LIMIT,
    MONTHLY_REQUEST_LIMIT,
    SAFETY_BUFFER
)

# 設定日誌
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('philly_email_searcher.log', encoding='utf-8'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)


class PhillyEmailSearcher:
    """費城餐廳 Email 搜尋器"""
    
    def __init__(self):
        """初始化搜尋器"""
        self.api_key = GOOGLE_API_KEY
        self.base_url = GOOGLE_PLACES_BASE_URL
        self.request_count = 0
        self.daily_limit = DAILY_REQUEST_LIMIT - SAFETY_BUFFER
        self.monthly_limit = MONTHLY_REQUEST_LIMIT - SAFETY_BUFFER
        
        # API 請求設定
        self.base_delay = 0.2  # 基礎延遲（秒）
        self.max_delay = 0.5   # 最大延遲（秒）
        self.max_retries = 3   # 最大重試次數
        self.retry_delay = 2   # 重試延遲（秒）
        
    def check_request_limit(self):
        """檢查是否超過 API 請求限制"""
        if self.request_count >= self.daily_limit:
            logger.warning(f"已達到每日請求限制 ({self.request_count}/{self.daily_limit})")
            return False
        
        if self.request_count >= self.monthly_limit:
            logger.warning(f"已達到每月請求限制 ({self.request_count}/{self.monthly_limit})")
            return False
        
        return True
    
    def random_delay(self):
        """隨機延遲，避免 API 限制"""
        delay = random.uniform(self.base_delay, self.max_delay)
        time.sleep(delay)
    
    def search_place_by_name(self, restaurant_name, location="Philadelphia"):
        """
        使用餐廳名稱搜尋 Google Places
        
        Args:
            restaurant_name (str): 餐廳名稱
            location (str): 搜尋地點
            
        Returns:
            dict: 搜尋結果，包含 place_id 和基本資訊
        """
        if not self.check_request_limit():
            return None
        
        # 建立搜尋查詢：餐廳名稱 + Philadelphia
        query = f"{restaurant_name} {location}"
        
        url = f"{self.base_url}/textsearch/json"
        params = {
            'query': query,
            'key': self.api_key,
            'type': 'restaurant'
        }
        
        for attempt in range(self.max_retries):
            try:
                self.random_delay()
                logger.info(f"搜尋: '{query}' (嘗試 {attempt + 1}/{self.max_retries})")
                
                response = requests.get(url, params=params, timeout=10)
                self.request_count += 1
                
                if response.status_code == 200:
                    data = response.json()
                    
                    if data.get('status') == 'OK':
                        results = data.get('results', [])
                        
                        if results:
                            # 選第一個結果
                            first_result = results[0]
                            logger.info(f"找到餐廳: {first_result.get('name', 'Unknown')}")
                            return {
                                'place_id': first_result.get('place_id'),
                                'name': first_result.get('name'),
                                'formatted_address': first_result.get('formatted_address'),
                                'status': 'found'
                            }
                        else:
                            logger.warning(f"未找到結果: {query}")
                            return {'status': 'not_found'}
                    
                    elif data.get('status') == 'ZERO_RESULTS':
                        logger.warning(f"無結果: {query}")
                        return {'status': 'not_found'}
                    
                    elif data.get('status') == 'OVER_QUERY_LIMIT':
                        logger.error("API 配額已用完")
                        return {'status': 'quota_exceeded'}
                    
                    else:
                        logger.error(f"API 錯誤: {data.get('status')}")
                        if attempt < self.max_retries - 1:
                            time.sleep(self.retry_delay * (attempt + 1))
                            continue
                        return {'status': 'error', 'message': data.get('status')}
                
                elif response.status_code == 429:
                    # 請求過於頻繁
                    wait_time = self.retry_delay * (attempt + 1)
                    logger.warning(f"請求過於頻繁，等待 {wait_time} 秒後重試...")
                    time.sleep(wait_time)
                    continue
                
                else:
                    logger.error(f"HTTP 錯誤: {response.status_code}")
                    if attempt < self.max_retries - 1:
                        time.sleep(self.retry_delay * (attempt + 1))
                        continue
                    return {'status': 'error', 'message': f'HTTP {response.status_code}'}
            
            except requests.exceptions.Timeout:
                logger.warning(f"請求超時，重試中... (嘗試 {attempt + 1}/{self.max_retries})")
                if attempt < self.max_retries - 1:
                    time.sleep(self.retry_delay * (attempt + 1))
                    continue
            
            except Exception as e:
                logger.error(f"搜尋過程中發生錯誤: {str(e)}")
                if attempt < self.max_retries - 1:
                    time.sleep(self.retry_delay * (attempt + 1))
                    continue
                return {'status': 'error', 'message': str(e)}
        
        return {'status': 'error', 'message': 'Max retries exceeded'}
    
    def get_place_website(self, place_id):
        """
        使用 Place ID 取得餐廳的 website
        
        Args:
            place_id (str): Google Places ID
            
        Returns:
            str: 餐廳 website URL，如果沒有則返回 None
        """
        if not self.check_request_limit():
            return None
        
        url = f"{self.base_url}/details/json"
        params = {
            'place_id': place_id,
            'fields': 'website',
            'key': self.api_key
        }
        
        for attempt in range(self.max_retries):
            try:
                self.random_delay()
                logger.info(f"獲取 website: {place_id} (嘗試 {attempt + 1}/{self.max_retries})")
                
                response = requests.get(url, params=params, timeout=10)
                self.request_count += 1
                
                if response.status_code == 200:
                    data = response.json()
                    
                    if data.get('status') == 'OK':
                        result = data.get('result', {})
                        website = result.get('website')
                        
                        if website:
                            logger.info(f"找到 website: {website}")
                            return website
                        else:
                            logger.warning("Place Details 中沒有 website")
                            return None
                    
                    elif data.get('status') == 'OVER_QUERY_LIMIT':
                        logger.error("API 配額已用完")
                        return None
                    
                    else:
                        logger.error(f"Place Details API 錯誤: {data.get('status')}")
                        if attempt < self.max_retries - 1:
                            time.sleep(self.retry_delay * (attempt + 1))
                            continue
                        return None
                
                elif response.status_code == 429:
                    wait_time = self.retry_delay * (attempt + 1)
                    logger.warning(f"請求過於頻繁，等待 {wait_time} 秒後重試...")
                    time.sleep(wait_time)
                    continue
                
                else:
                    logger.error(f"HTTP 錯誤: {response.status_code}")
                    if attempt < self.max_retries - 1:
                        time.sleep(self.retry_delay * (attempt + 1))
                        continue
                    return None
            
            except requests.exceptions.Timeout:
                logger.warning(f"請求超時，重試中... (嘗試 {attempt + 1}/{self.max_retries})")
                if attempt < self.max_retries - 1:
                    time.sleep(self.retry_delay * (attempt + 1))
                    continue
            
            except Exception as e:
                logger.error(f"獲取 website 時發生錯誤: {str(e)}")
                if attempt < self.max_retries - 1:
                    time.sleep(self.retry_delay * (attempt + 1))
                    continue
                return None
        
        return None
    
    def process_restaurant(self, restaurant_name, address=None, phone=None):
        """
        處理單一餐廳：搜尋並取得 website
        
        Args:
            restaurant_name (str): 餐廳名稱
            address (str): 餐廳地址（可選，目前未使用）
            phone (str): 餐廳電話（可選，目前未使用）
            
        Returns:
            dict: 處理結果
        """
        logger.info(f"處理餐廳: {restaurant_name}")
        
        # 步驟 1: 搜尋餐廳
        search_result = self.search_place_by_name(restaurant_name)
        
        if not search_result or search_result.get('status') != 'found':
            return {
                'restaurant_name': restaurant_name,
                'website': None,
                'status': search_result.get('status', 'error') if search_result else 'error',
                'message': search_result.get('message', '') if search_result else 'No search result'
            }
        
        # 步驟 2: 取得 website
        place_id = search_result.get('place_id')
        website = self.get_place_website(place_id)
        
        return {
            'restaurant_name': restaurant_name,
            'google_place_id': place_id,
            'google_place_name': search_result.get('name'),
            'google_address': search_result.get('formatted_address'),
            'website': website,
            'status': 'found' if website else 'no_website'
        }
    
    def process_excel_file(self, input_file_path, max_restaurants=None):
        """
        處理 Excel 檔案
        
        Args:
            input_file_path (str): 輸入 Excel 檔案路徑
            max_restaurants (int): 限制處理的餐廳數量（可選）
            
        Returns:
            str: 輸出檔案路徑
        """
        # 讀取 Excel 檔案
        logger.info(f"讀取 Excel 檔案: {input_file_path}")
        try:
            df = pd.read_excel(input_file_path)
        except Exception as e:
            logger.error(f"讀取 Excel 檔案失敗: {e}")
            raise
        
        # 檢查必要欄位
        required_columns = ['Name']
        missing_columns = [col for col in required_columns if col not in df.columns]
        if missing_columns:
            raise ValueError(f"Excel 檔案缺少必要欄位: {missing_columns}")
        
        # 準備輸出欄位
        df['Google_Website'] = ''
        df['Google_Place_ID'] = ''
        df['Google_Place_Name'] = ''
        df['Google_Address'] = ''
        df['Search_Status'] = ''
        df['Search_Message'] = ''
        
        # 限制處理數量
        total_restaurants = len(df)
        if max_restaurants and max_restaurants < total_restaurants:
            df = df.head(max_restaurants)
            logger.info(f"限制處理數量: {max_restaurants}/{total_restaurants}")
        
        actual_count = len(df)
        logger.info(f"開始處理 {actual_count} 家餐廳")
        
        # 處理每家餐廳
        success_count = 0
        no_website_count = 0
        error_count = 0
        
        for index, row in df.iterrows():
            restaurant_name = str(row['Name']).strip()
            address = str(row.get('Add', '')).strip() if pd.notna(row.get('Add')) else None
            phone = str(row.get('Phone', '')).strip() if pd.notna(row.get('Phone')) else None
            
            logger.info(f"\n處理進度: {index + 1}/{actual_count}")
            logger.info(f"餐廳名稱: {restaurant_name}")
            
            try:
                result = self.process_restaurant(restaurant_name, address, phone)
                
                # 更新 DataFrame
                df.at[index, 'Google_Website'] = result.get('website', '') or ''
                df.at[index, 'Google_Place_ID'] = result.get('google_place_id', '') or ''
                df.at[index, 'Google_Place_Name'] = result.get('google_place_name', '') or ''
                df.at[index, 'Google_Address'] = result.get('google_address', '') or ''
                df.at[index, 'Search_Status'] = result.get('status', '')
                df.at[index, 'Search_Message'] = result.get('message', '')
                
                # 統計
                if result.get('website'):
                    success_count += 1
                    logger.info(f"✅ 成功取得 website: {result.get('website')}")
                elif result.get('status') == 'found':
                    no_website_count += 1
                    logger.warning(f"⚠️ 找到餐廳但沒有 website")
                else:
                    error_count += 1
                    logger.warning(f"❌ 處理失敗: {result.get('status')}")
            
            except Exception as e:
                logger.error(f"處理餐廳時發生錯誤: {str(e)}")
                df.at[index, 'Search_Status'] = 'error'
                df.at[index, 'Search_Message'] = str(e)[:100]  # 限制訊息長度
                error_count += 1
            
            # 顯示進度
            if (index + 1) % 10 == 0:
                logger.info(f"\n進度摘要: 成功 {success_count}, 無 website {no_website_count}, 錯誤 {error_count}")
        
        # 儲存結果
        input_path = Path(input_file_path)
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_file = input_path.parent / f"{input_path.stem}_with_websites_{timestamp}.xlsx"
        
        try:
            df.to_excel(output_file, index=False)
            logger.info(f"\n結果已儲存至: {output_file}")
        except Exception as e:
            logger.error(f"儲存結果失敗: {e}")
            raise
        
        # 顯示統計結果
        logger.info("\n" + "="*60)
        logger.info("處理完成統計")
        logger.info("="*60)
        logger.info(f"總處理數: {actual_count}")
        logger.info(f"成功取得 website: {success_count}")
        logger.info(f"找到餐廳但無 website: {no_website_count}")
        logger.info(f"處理失敗: {error_count}")
        logger.info(f"API 請求總數: {self.request_count}")
        logger.info(f"成功率: {(success_count/actual_count*100):.1f}%")
        logger.info("="*60)
        
        return str(output_file)


def main():
    """主程式"""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='費城餐廳 Website 搜尋工具（第一階段）',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
範例:
  python philly_email_searcher.py "data/Philly BYOB Restaurant.xlsx"
  python philly_email_searcher.py "data/Philly BYOB Restaurant.xlsx" -n 10
        """
    )
    
    parser.add_argument(
        'input_file',
        help='輸入 Excel 檔案路徑（必須包含 Name 欄位）'
    )
    
    parser.add_argument(
        '-n', '--max-restaurants',
        type=int,
        help='限制處理的餐廳數量（例如: -n 10 表示只處理前10家）'
    )
    
    args = parser.parse_args()
    
    # 檢查檔案是否存在
    input_path = Path(args.input_file)
    if not input_path.exists():
        print(f"❌ 錯誤: 檔案不存在 - {input_path}")
        return 1
    
    # 建立搜尋器並執行
    searcher = PhillyEmailSearcher()
    
    try:
        print("="*60)
        print("費城餐廳 Website 搜尋工具")
        print("="*60)
        print(f"輸入檔案: {input_path}")
        if args.max_restaurants:
            print(f"限制處理數量: {args.max_restaurants}")
        print("="*60)
        print("\n開始處理...")
        print("這可能需要一些時間，請耐心等待...\n")
        
        output_file = searcher.process_excel_file(
            str(input_path),
            max_restaurants=args.max_restaurants
        )
        
        print(f"\n✅ 處理完成！")
        print(f"📁 結果檔案: {output_file}")
        print(f"📊 詳細日誌: philly_email_searcher.log")
        
        return 0
    
    except KeyboardInterrupt:
        print("\n\n⚠️ 程式被使用者中斷")
        return 1
    
    except Exception as e:
        print(f"\n❌ 程式執行失敗: {e}")
        logger.exception("程式執行失敗")
        return 1


if __name__ == "__main__":
    exit(main())

