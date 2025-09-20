# Google Places API 設定
# 請將 your_api_key_here 替換為您的實際API金鑰
GOOGLE_PLACES_API_KEY = "AIzaSyCzcNzweMQjo_vbspHtQcHGmWqhE2GDwjg"

# 搜尋設定
SEARCH_QUERY = "台北 懷石料理 無菜單料理 私廚"
MAX_RESULTS = 250

# 網格搜尋設定 - 突破60筆限制
USE_GRID_SEARCH = True  # 啟用網格搜尋
GRID_SIZE = 0.01  # 網格大小（度），約1公里

# 多個搜尋中心點 - 避免重複搜尋
SEARCH_CENTERS = [
    {'name': '信義區', 'lat': 25.0330, 'lng': 121.5654},  # 台北101
    {'name': '中山區', 'lat': 25.0520, 'lng': 121.5200},  # 中山站
    {'name': '大安區', 'lat': 25.0260, 'lng': 121.5440},  # 大安站
    {'name': '松山區', 'lat': 25.0500, 'lng': 121.5780},  # 松山機場
    {'name': '中正區', 'lat': 25.0320, 'lng': 121.5200},  # 中正紀念堂
    {'name': '大同區', 'lat': 25.0630, 'lng': 121.5120},  # 大同區
    {'name': '萬華區', 'lat': 25.0360, 'lng': 121.5000},  # 西門町
    {'name': '內湖區', 'lat': 25.0670, 'lng': 121.5940},  # 內湖
    {'name': '文山區', 'lat': 24.9900, 'lng': 121.5700},  # 文山
    {'name': '北投區', 'lat': 25.1320, 'lng': 121.4980},  # 北投
    {'name': '士林區', 'lat': 25.0880, 'lng': 121.5250},  # 士林
    {'name': '南港區', 'lat': 25.0550, 'lng': 121.6060}   # 南港
]

#熱炒 0,1 義式餐廳 4 法式餐廳 2 居酒 日本料理 1 鐵板燒 3 懷石料理 無菜單料理 私廚 4
# 當前使用的中心點索引（手動調整）
CURRENT_CENTER_INDEX = 3  # 0-11，對應上面的12個區域

GRID_RADIUS = 0.05  # 搜尋半徑（度），約5公里

# 輸出設定
OUTPUT_FILE = "restaurant_data.xlsx"

# 搜尋記錄設定 - 避免重複搜尋
SEARCH_LOG_FILE = "search_log.json"  # 記錄已搜尋的網格點
RESUME_SEARCH = True  # 是否從上次中斷的地方繼續
