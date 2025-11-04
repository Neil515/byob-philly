#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
費城餐廳 Email 搜尋工具 - 第二階段：從 Website 搜尋 Email
Philadelphia Restaurant Email Extractor - Phase 2: Extract Email from Website

從 Excel 檔案讀取餐廳 website，搜尋並提取 email 地址
"""

import pandas as pd
import requests
import re
import time
import random
import logging
from datetime import datetime
from pathlib import Path
from urllib.parse import urljoin, urlparse
import argparse

# 設定日誌
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('philly_email_extractor.log', encoding='utf-8'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)


class PhillyEmailExtractor:
    """費城餐廳 Email 提取器"""
    
    def __init__(self):
        """初始化提取器"""
        # Email 正則表達式
        self.email_pattern = re.compile(
            r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
        )
        
        # 常見的無效 email 域名
        self.invalid_domains = {
            'example.com', 'test.com', 'sample.com', 'demo.com',
            'yoursite.com', 'website.com', 'domain.com'
        }
        
        # 常見的無效 email 前綴
        self.invalid_prefixes = {
            'noreply', 'no-reply', 'donotreply', 'do-not-reply',
            'mailer-daemon', 'postmaster', 'admin@example'
        }
        
        # 請求設定
        self.base_delay = 1.0  # 基礎延遲（秒）
        self.max_delay = 2.0   # 最大延遲（秒）
        self.timeout = 10      # 請求超時（秒）
        self.max_retries = 2   # 最大重試次數
        
        # HTTP headers
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.9',
            'Accept-Encoding': 'gzip, deflate',
            'Connection': 'keep-alive',
        }
    
    def random_delay(self):
        """隨機延遲，避免被封鎖"""
        delay = random.uniform(self.base_delay, self.max_delay)
        time.sleep(delay)
    
    def is_valid_email(self, email):
        """驗證 email 是否有效"""
        if not email or len(email) < 5:
            return False
        
        email_lower = email.lower().strip()
        
        # 檢查格式
        if not self.email_pattern.match(email_lower):
            return False
        
        # 檢查無效前綴
        for prefix in self.invalid_prefixes:
            if email_lower.startswith(prefix):
                return False
        
        # 檢查無效域名
        if '@' in email_lower:
            domain = email_lower.split('@')[1]
            if domain in self.invalid_domains:
                return False
        
        # 過濾常見的範例 email
        invalid_patterns = [
            r'example\.',
            r'test\.',
            r'sample\.',
            r'demo\.',
            r'your.*@',
            r'email@',
            r'info@info',
            r'contact@contact'
        ]
        
        for pattern in invalid_patterns:
            if re.search(pattern, email_lower):
                return False
        
        return True
    
    def extract_emails_from_text(self, text):
        """從文字中提取 email"""
        if not text:
            return []
        
        emails = self.email_pattern.findall(text)
        valid_emails = []
        
        for email in emails:
            email = email.lower().strip()
            if self.is_valid_email(email):
                valid_emails.append(email)
        
        return list(set(valid_emails))  # 去重
    
    def search_website_pages(self, base_url):
        """
        搜尋網站的各個頁面尋找 email
        
        Args:
            base_url (str): 網站基礎 URL
            
        Returns:
            list: 找到的 email 列表
        """
        if not base_url or pd.isna(base_url):
            return []
        
        # 清理 URL
        base_url = str(base_url).strip()
        if not base_url.startswith(('http://', 'https://')):
            base_url = 'https://' + base_url
        
        # 要搜尋的頁面路徑（英文和常見路徑）
        search_paths = [
            '',                    # 首頁
            '/contact',
            '/contact-us',
            '/contact.html',
            '/contact.php',
            '/about',
            '/about-us',
            '/about.html',
            '/contactinfo',
            '/contact-info',
            '/get-in-touch',
            '/reach-us',
            '/reachout',
            '/info',
            '/information',
        ]
        
        all_emails = []
        found_emails = False
        
        for path in search_paths:
            try:
                url = urljoin(base_url, path)
                
                # 如果是首頁且已經找到 email，跳過（避免重複搜尋）
                if path == '' and all_emails:
                    continue
                
                logger.info(f"搜尋頁面: {url}")
                
                # 嘗試請求
                for attempt in range(self.max_retries):
                    try:
                        response = requests.get(
                            url,
                            headers=self.headers,
                            timeout=self.timeout,
                            allow_redirects=True
                        )
                        
                        if response.status_code == 200:
                            # 提取 email
                            found_emails = self.extract_emails_from_text(response.text)
                            
                            if found_emails:
                                all_emails.extend(found_emails)
                                logger.info(f"在 {url} 找到 {len(found_emails)} 個 email")
                                
                                # 如果找到 email，優先返回（但有時聯絡頁面有更多）
                                if path in ['/contact', '/contact-us', '/contact.html']:
                                    return list(set(all_emails))  # 返回找到的所有 email
                                
                                found_emails = True  # 標記已找到
                                break  # 找到 email 就停止嘗試重試
                        
                        elif response.status_code == 404:
                            # 404 錯誤，跳過這個路徑
                            break
                        
                        else:
                            # 其他錯誤，嘗試重試
                            if attempt < self.max_retries - 1:
                                time.sleep(1)
                                continue
                    
                    except requests.exceptions.Timeout:
                        logger.warning(f"請求超時: {url}")
                        if attempt < self.max_retries - 1:
                            time.sleep(1)
                            continue
                    
                    except requests.exceptions.RequestException as e:
                        logger.warning(f"請求錯誤 {url}: {str(e)[:50]}")
                        if attempt < self.max_retries - 1:
                            time.sleep(1)
                            continue
                    
                    except Exception as e:
                        logger.warning(f"處理頁面時發生錯誤 {url}: {str(e)[:50]}")
                        break
                
                # 如果找到 email，可以選擇停止搜尋其他頁面（但我們繼續搜尋以找到更多）
                if all_emails and path == '':
                    # 首頁已找到，繼續搜尋聯絡頁面
                    continue
                
                # 隨機延遲
                self.random_delay()
            
            except Exception as e:
                logger.warning(f"處理路徑 {path} 時發生錯誤: {str(e)[:50]}")
                continue
        
        return list(set(all_emails))  # 去重並返回
    
    def extract_restaurant_email(self, website_url, restaurant_name=None):
        """
        從餐廳 website 提取 email
        
        Args:
            website_url (str): 餐廳 website URL
            restaurant_name (str): 餐廳名稱（可選，用於日誌）
            
        Returns:
            dict: 包含 email 和狀態的字典
        """
        if not website_url or pd.isna(website_url) or str(website_url).strip() == '':
            return {
                'email': None,
                'status': 'no_website',
                'message': '沒有 website URL'
            }
        
        try:
            website_url = str(website_url).strip()
            logger.info(f"處理餐廳: {restaurant_name or 'Unknown'} | Website: {website_url}")
            
            # 搜尋 website
            emails = self.search_website_pages(website_url)
            
            if emails:
                # 優先選擇看起來最相關的 email
                # 優先順序：1. 包含餐廳名稱的 email, 2. info@, 3. contact@, 4. 第一個
                best_email = None
                
                if restaurant_name:
                    restaurant_name_lower = restaurant_name.lower().replace(' ', '').replace("'", "")
                    for email in emails:
                        email_name = email.split('@')[0].lower()
                        if restaurant_name_lower in email_name or email_name in restaurant_name_lower:
                            best_email = email
                            break
                
                if not best_email:
                    # 尋找常見的聯絡 email
                    for prefix in ['info', 'contact', 'hello', 'hello@']:
                        for email in emails:
                            if email.startswith(prefix + '@'):
                                best_email = email
                                break
                        if best_email:
                            break
                
                # 如果還沒找到，使用第一個
                if not best_email:
                    best_email = emails[0]
                
                logger.info(f"✅ 找到 email: {best_email}")
                return {
                    'email': best_email,
                    'status': 'found',
                    'message': f'找到 {len(emails)} 個 email，選擇: {best_email}',
                    'all_emails': emails  # 保留所有找到的 email 供參考
                }
            else:
                logger.warning(f"⚠️ 未找到 email")
                return {
                    'email': None,
                    'status': 'not_found',
                    'message': '在網站中未找到 email'
                }
        
        except Exception as e:
            logger.error(f"提取 email 時發生錯誤: {str(e)}")
            return {
                'email': None,
                'status': 'error',
                'message': str(e)[:100]
            }
    
    def expand_multiple_emails(self, df):
        """
        展開多個 email 的餐廳資料，每個 email 一行
        
        Args:
            df (pd.DataFrame): 原始 DataFrame
            
        Returns:
            pd.DataFrame: 展開後的 DataFrame
        """
        expanded_rows = []
        
        for index, row in df.iterrows():
            email_all_found = str(row.get('Email_All_Found', '')).strip()
            
            # 檢查是否有多個 email（用分號分隔）
            if email_all_found and ';' in email_all_found:
                emails = [email.strip() for email in email_all_found.split(';') if email.strip()]
                
                if len(emails) > 1:
                    # 為每個 email 創建一行
                    for email in emails:
                        new_row = row.copy()
                        new_row['Email'] = email  # 更新為當前 email
                        new_row['Email_All_Found'] = email  # Email_All_Found 也更新為單一 email
                        expanded_rows.append(new_row)
                    logger.info(f"展開餐廳 '{row.get('Name', 'Unknown')}': {len(emails)} 個 email")
                else:
                    # 只有一個 email，保持原樣
                    expanded_rows.append(row)
            else:
                # 沒有多個 email，保持原樣
                expanded_rows.append(row)
        
        # 建立新的 DataFrame
        df_expanded = pd.DataFrame(expanded_rows)
        
        return df_expanded
    
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
        required_columns = ['Google_Website']
        missing_columns = [col for col in required_columns if col not in df.columns]
        if missing_columns:
            raise ValueError(f"Excel 檔案缺少必要欄位: {missing_columns}。請確保這是步驟 1 的輸出檔案。")
        
        # 準備輸出欄位
        if 'Email' not in df.columns:
            df['Email'] = ''
        if 'Email_Status' not in df.columns:
            df['Email_Status'] = ''
        if 'Email_Message' not in df.columns:
            df['Email_Message'] = ''
        if 'Email_All_Found' not in df.columns:
            df['Email_All_Found'] = ''  # 儲存所有找到的 email（用分號分隔）
        
        # 限制處理數量
        total_restaurants = len(df)
        if max_restaurants and max_restaurants < total_restaurants:
            df = df.head(max_restaurants)
            logger.info(f"限制處理數量: {max_restaurants}/{total_restaurants}")
        
        actual_count = len(df)
        logger.info(f"開始處理 {actual_count} 家餐廳")
        
        # 處理每家餐廳
        success_count = 0
        no_email_count = 0
        no_website_count = 0
        error_count = 0
        
        for index, row in df.iterrows():
            restaurant_name = str(row.get('Name', 'Unknown')).strip() if pd.notna(row.get('Name')) else 'Unknown'
            website_url = row.get('Google_Website')
            
            logger.info(f"\n處理進度: {index + 1}/{actual_count}")
            logger.info(f"餐廳名稱: {restaurant_name}")
            
            try:
                result = self.extract_restaurant_email(website_url, restaurant_name)
                
                # 更新 DataFrame
                df.at[index, 'Email'] = result.get('email', '') or ''
                df.at[index, 'Email_Status'] = result.get('status', '')
                df.at[index, 'Email_Message'] = result.get('message', '')
                
                # 儲存所有找到的 email
                all_emails = result.get('all_emails', [])
                if all_emails:
                    df.at[index, 'Email_All_Found'] = '; '.join(all_emails)
                
                # 統計
                if result.get('email'):
                    success_count += 1
                elif result.get('status') == 'no_website':
                    no_website_count += 1
                elif result.get('status') == 'not_found':
                    no_email_count += 1
                else:
                    error_count += 1
            
            except Exception as e:
                logger.error(f"處理餐廳時發生錯誤: {str(e)}")
                df.at[index, 'Email'] = ''
                df.at[index, 'Email_Status'] = 'error'
                df.at[index, 'Email_Message'] = str(e)[:100]
                error_count += 1
            
            # 顯示進度
            if (index + 1) % 10 == 0:
                logger.info(f"\n進度摘要: 成功 {success_count}, 無 email {no_email_count}, 無 website {no_website_count}, 錯誤 {error_count}")
        
        # 展開多個 email 的餐廳資料
        logger.info("\n展開多個 email 的餐廳資料...")
        df_expanded = self.expand_multiple_emails(df)
        expanded_count = len(df_expanded) - len(df)
        if expanded_count > 0:
            logger.info(f"新增 {expanded_count} 行（多個 email 的餐廳）")
        
        # 儲存結果
        input_path = Path(input_file_path)
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_file = input_path.parent / f"{input_path.stem}_with_emails_{timestamp}.xlsx"
        
        try:
            df_expanded.to_excel(output_file, index=False)
            logger.info(f"\n結果已儲存至: {output_file}")
        except Exception as e:
            logger.error(f"儲存結果失敗: {e}")
            raise
        
        # 顯示統計結果
        logger.info("\n" + "="*60)
        logger.info("處理完成統計")
        logger.info("="*60)
        logger.info(f"總處理數: {actual_count}")
        logger.info(f"成功取得 email: {success_count}")
        logger.info(f"有 website 但無 email: {no_email_count}")
        logger.info(f"無 website: {no_website_count}")
        logger.info(f"處理失敗: {error_count}")
        if actual_count > 0:
            logger.info(f"成功率: {(success_count/actual_count*100):.1f}%")
        logger.info("="*60)
        
        return str(output_file)


def main():
    """主程式"""
    parser = argparse.ArgumentParser(
        description='費城餐廳 Email 提取工具（第二階段）',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
範例:
  python philly_email_extractor.py "data/Philly BYOB Restaurant_with_websites_xxx.xlsx"
  python philly_email_extractor.py "data/Philly BYOB Restaurant_with_websites_xxx.xlsx" -n 5
        """
    )
    
    parser.add_argument(
        'input_file',
        help='輸入 Excel 檔案路徑（必須包含 Google_Website 欄位）'
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
    
    # 建立提取器並執行
    extractor = PhillyEmailExtractor()
    
    try:
        print("="*60)
        print("費城餐廳 Email 提取工具（第二階段）")
        print("="*60)
        print(f"輸入檔案: {input_path}")
        if args.max_restaurants:
            print(f"限制處理數量: {args.max_restaurants}")
        print("="*60)
        print("\n開始處理...")
        print("這可能需要一些時間，請耐心等待...\n")
        
        output_file = extractor.process_excel_file(
            str(input_path),
            max_restaurants=args.max_restaurants
        )
        
        print(f"\n✅ 處理完成！")
        print(f"📁 結果檔案: {output_file}")
        print(f"📊 詳細日誌: philly_email_extractor.log")
        
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

