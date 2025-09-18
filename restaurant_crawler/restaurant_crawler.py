import requests
import json
import time
import pandas as pd
import os
from datetime import datetime
from config import GOOGLE_PLACES_API_KEY, SEARCH_QUERY, MAX_RESULTS, OUTPUT_FILE, USE_GRID_SEARCH, GRID_SIZE, SEARCH_CENTERS, CURRENT_CENTER_INDEX, GRID_RADIUS, SEARCH_LOG_FILE, RESUME_SEARCH

class GooglePlacesCrawler:
    def __init__(self, api_key):
        self.api_key = api_key
        self.base_url = "https://maps.googleapis.com/maps/api/place"
        self.session = requests.Session()
        
    def search_restaurants(self, query, max_results=20, location=None):
        """
        使用Text Search API搜尋餐廳
        """
        url = f"{self.base_url}/textsearch/json"
        params = {
            'query': query,
            'key': self.api_key,
            'language': 'zh-TW',
            'region': 'tw'
        }
        
        # 如果有指定位置，加入location參數
        if location:
            params['location'] = f"{location['lat']},{location['lng']}"
            params['radius'] = 5000  # 5公里半徑
        
        restaurants = []
        next_page_token = None
        
        while len(restaurants) < max_results:
            if next_page_token:
                params['pagetoken'] = next_page_token
                time.sleep(2)  # 等待2秒，避免API限制
            
            try:
                response = self.session.get(url, params=params)
                response.raise_for_status()
                data = response.json()
                
                if data['status'] != 'OK':
                    print(f"API錯誤: {data['status']}")
                    break
                
                restaurants.extend(data['results'])
                
                # 檢查是否有下一頁
                next_page_token = data.get('next_page_token')
                if not next_page_token:
                    break
                    
            except requests.exceptions.RequestException as e:
                print(f"請求錯誤: {e}")
                break
        
        return restaurants[:max_results]
    
    def generate_grid_points(self, center, grid_size, radius):
        """
        生成網格搜尋點
        """
        points = []
        steps = int(radius / grid_size)
        
        for i in range(-steps, steps + 1):
            for j in range(-steps, steps + 1):
                lat = center['lat'] + (i * grid_size)
                lng = center['lng'] + (j * grid_size)
                points.append({'lat': lat, 'lng': lng})
        
        return points
    
    def load_search_log(self):
        """
        載入搜尋記錄，避免重複搜尋
        """
        if os.path.exists(SEARCH_LOG_FILE):
            try:
                with open(SEARCH_LOG_FILE, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except:
                return {}
        return {}
    
    def save_search_log(self, search_log):
        """
        儲存搜尋記錄
        """
        with open(SEARCH_LOG_FILE, 'w', encoding='utf-8') as f:
            json.dump(search_log, f, indent=2, ensure_ascii=False)
    
    def point_to_key(self, point):
        """
        將網格點轉換為字串鍵值，用於記錄
        """
        return f"{point['lat']:.4f},{point['lng']:.4f}"
    
    def search_restaurants_with_grid(self, query, max_results_per_grid=60):
        """
        使用網格搜尋突破60筆限制（支援斷點續傳）
        """
        print(f"開始網格搜尋: {query}")
        print(f"網格大小: {GRID_SIZE}度 (約{GRID_SIZE*111:.1f}公里)")
        print(f"搜尋半徑: {GRID_RADIUS}度 (約{GRID_RADIUS*111:.1f}公里)")
        
        # 載入搜尋記錄
        search_log = self.load_search_log() if RESUME_SEARCH else {}
        query_key = f"{query}_{GRID_SIZE}_{GRID_RADIUS}"
        
        if query_key not in search_log:
            search_log[query_key] = {
                'completed_points': [],
                'last_update': datetime.now().isoformat(),
                'total_restaurants': 0
            }
        
        completed_points = set(search_log[query_key]['completed_points'])
        
        # 獲取當前中心點
        current_center = SEARCH_CENTERS[CURRENT_CENTER_INDEX]
        print(f"當前搜尋中心: {current_center['name']} ({current_center['lat']:.4f}, {current_center['lng']:.4f})")
        
        # 生成網格點
        grid_points = self.generate_grid_points(current_center, GRID_SIZE, GRID_RADIUS)
        
        # 過濾已完成的網格點
        remaining_points = [p for p in grid_points if self.point_to_key(p) not in completed_points]
        
        print(f"總網格點數: {len(grid_points)} 個")
        print(f"已完成: {len(completed_points)} 個")
        print(f"剩餘: {len(remaining_points)} 個")
        
        if len(remaining_points) == 0:
            print("所有網格點都已搜尋完成！")
            return []
        
        all_restaurants = []
        seen_place_ids = set()  # 避免重複餐廳
        
        for i, point in enumerate(remaining_points, 1):
            point_key = self.point_to_key(point)
            print(f"\n=== 網格點 {len(completed_points)+i}/{len(grid_points)}: ({point['lat']:.4f}, {point['lng']:.4f}) ===")
            
            try:
                # 在每個網格點搜尋
                restaurants = self.search_restaurants(query, max_results_per_grid, point)
                print(f"找到 {len(restaurants)} 家餐廳")
                
                # 去重複
                unique_restaurants = []
                for restaurant in restaurants:
                    if restaurant['place_id'] not in seen_place_ids:
                        seen_place_ids.add(restaurant['place_id'])
                        unique_restaurants.append(restaurant)
                
                print(f"新增 {len(unique_restaurants)} 家不重複的餐廳")
                all_restaurants.extend(unique_restaurants)
                
                # 標記此網格點為已完成
                search_log[query_key]['completed_points'].append(point_key)
                search_log[query_key]['last_update'] = datetime.now().isoformat()
                search_log[query_key]['total_restaurants'] = len(all_restaurants)
                
                # 每10個網格點保存一次記錄
                if i % 10 == 0:
                    self.save_search_log(search_log)
                    print(f"💾 已保存搜尋進度 ({i}/{len(remaining_points)})")
                
            except Exception as e:
                print(f"❌ 網格點搜尋失敗: {e}")
                # 即使失敗也要保存進度
                self.save_search_log(search_log)
                
            # 網格間隔，避免API限制
            if i < len(remaining_points):
                print("等待3秒後繼續下一個網格點...")
                time.sleep(3)
        
        # 最終保存搜尋記錄
        self.save_search_log(search_log)
        
        print(f"\n網格搜尋完成！總共收集到 {len(all_restaurants)} 家不重複的餐廳")
        print(f"搜尋記錄已保存到: {SEARCH_LOG_FILE}")
        return all_restaurants
    
    def get_place_details(self, place_id):
        """
        使用Place Details API獲取餐廳詳細資訊
        """
        url = f"{self.base_url}/details/json"
        params = {
            'place_id': place_id,
            'key': self.api_key,
            'language': 'zh-TW',
            'fields': 'name,formatted_address,formatted_phone_number,website,rating,user_ratings_total'
        }
        
        try:
            response = self.session.get(url, params=params)
            response.raise_for_status()
            data = response.json()
            
            if data['status'] == 'OK':
                return data['result']
            else:
                print(f"Place Details API錯誤: {data['status']}")
                return None
                
        except requests.exceptions.RequestException as e:
            print(f"請求錯誤: {e}")
            return None
    
    def crawl_restaurants(self, query, max_results=20):
        """
        主要爬蟲功能
        """
        print(f"開始搜尋: {query}")
        
        # 步驟1: 搜尋餐廳列表
        if USE_GRID_SEARCH:
            print("使用網格搜尋模式突破60筆限制")
            restaurants = self.search_restaurants_with_grid(query, 60)
        else:
            print("使用標準搜尋模式")
            restaurants = self.search_restaurants(query, max_results)
        
        print(f"找到 {len(restaurants)} 家餐廳")
        
        # 步驟2: 獲取每家餐廳的詳細資訊
        detailed_restaurants = []
        
        for i, restaurant in enumerate(restaurants, 1):
            print(f"正在處理第 {i}/{len(restaurants)} 家餐廳: {restaurant['name']}")
            
            place_id = restaurant['place_id']
            details = self.get_place_details(place_id)
            
            if details:
                restaurant_info = {
                    'name': details.get('name', ''),
                    'address': details.get('formatted_address', ''),
                    'phone': details.get('formatted_phone_number', ''),
                    'website': details.get('website', ''),
                    'rating': details.get('rating', ''),
                    'user_ratings_total': details.get('user_ratings_total', ''),
                    'place_id': place_id
                }
                detailed_restaurants.append(restaurant_info)
            
            # 避免API限制
            time.sleep(0.1)
        
        return detailed_restaurants
    
    def generate_unique_filename(self, base_filename, query):
        """
        生成唯一的檔案名稱，避免重複
        """
        # 取得當前時間
        now = datetime.now()
        timestamp = now.strftime("%Y%m%d_%H%M%S")
        
        # 清理查詢字串，移除特殊字元
        clean_query = query.replace(" ", "_").replace("/", "_").replace("\\", "_")
        clean_query = "".join(c for c in clean_query if c.isalnum() or c in "_-")
        
        # 分割檔案名稱和副檔名
        if "." in base_filename:
            name, ext = base_filename.rsplit(".", 1)
        else:
            name = base_filename
            ext = "xlsx"
        
        # 生成新的檔案名稱
        new_filename = f"{name}_{clean_query}_{timestamp}.{ext}"
        
        # 檢查檔案是否存在，如果存在則加入編號
        counter = 1
        original_filename = new_filename
        while os.path.exists(new_filename):
            if "." in original_filename:
                name_part, ext_part = original_filename.rsplit(".", 1)
                new_filename = f"{name_part}_{counter:03d}.{ext_part}"
            else:
                new_filename = f"{original_filename}_{counter:03d}"
            counter += 1
        
        return new_filename

def main():
    # 檢查API金鑰
    if GOOGLE_PLACES_API_KEY == "your_api_key_here":
        print("請先在config.py中設定您的Google Places API金鑰")
        return
    
    # 建立爬蟲實例
    crawler = GooglePlacesCrawler(GOOGLE_PLACES_API_KEY)
    
    print("=== BYOB 餐廳爬蟲程式 ===")
    if USE_GRID_SEARCH:
        print("🔍 網格搜尋模式 - 突破Google API的60筆限制")
        print(f"搜尋關鍵字: {SEARCH_QUERY}")
        print(f"網格大小: {GRID_SIZE}度 (約{GRID_SIZE*111:.1f}公里)")
        print(f"搜尋半徑: {GRID_RADIUS}度 (約{GRID_RADIUS*111:.1f}公里)")
        
        # 顯示所有可用的搜尋中心點
        print(f"\n📍 可用的搜尋中心點:")
        for i, center in enumerate(SEARCH_CENTERS):
            marker = "👉" if i == CURRENT_CENTER_INDEX else "  "
            print(f"{marker} {i}: {center['name']} ({center['lat']:.4f}, {center['lng']:.4f})")
        
        print(f"\n🎯 當前使用: 中心點 {CURRENT_CENTER_INDEX} - {SEARCH_CENTERS[CURRENT_CENTER_INDEX]['name']}")
        
        # 計算預期結果
        grid_points = crawler.generate_grid_points(SEARCH_CENTERS[CURRENT_CENTER_INDEX], GRID_SIZE, GRID_RADIUS)
        print(f"網格點數量: {len(grid_points)} 個")
        print(f"預期結果: 最多 {len(grid_points) * 60} 筆（去重複後會更少）")
        
        print(f"\n💡 提示: 要搜尋不同區域，請修改 config.py 中的 CURRENT_CENTER_INDEX")
        print(f"   例如: CURRENT_CENTER_INDEX = 1  # 搜尋中山區")
    else:
        print("📝 標準搜尋模式")
        print(f"搜尋關鍵字: {SEARCH_QUERY}")
        print(f"最大結果數: {MAX_RESULTS}")
    
    # 開始爬取
    restaurants = crawler.crawl_restaurants(SEARCH_QUERY, MAX_RESULTS)
    
    if restaurants:
        # 顯示結果
        print(f"\n=== 爬取完成 ===")
        print(f"成功獲取 {len(restaurants)} 家餐廳的詳細資訊")
        
        # 顯示前5家餐廳作為預覽
        print(f"\n前5家餐廳預覽:")
        for i, restaurant in enumerate(restaurants[:5], 1):
            print(f"\n{i}. 餐廳名稱: {restaurant['name']}")
            print(f"   地址: {restaurant['address']}")
            print(f"   電話: {restaurant['phone']}")
            print(f"   網站: {restaurant['website']}")
            print(f"   評分: {restaurant['rating']}")
            print("-" * 50)
        
        # 生成唯一檔案名稱並儲存到Excel
        unique_filename = crawler.generate_unique_filename(OUTPUT_FILE, SEARCH_QUERY)
        df = pd.DataFrame(restaurants)
        df.to_excel(unique_filename, index=False, engine='openpyxl')
        print(f"\n結果已儲存到: {unique_filename}")
        print(f"Excel檔案包含 {len(restaurants)} 家餐廳的完整資料")
    else:
        print("沒有找到任何餐廳")

if __name__ == "__main__":
    main()
