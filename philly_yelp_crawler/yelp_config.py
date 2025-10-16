# Yelp Fusion API 設定檔案
# 請將下面的 YOUR_API_KEY_HERE 替換為你的實際 API Key

# API 憑證
YELP_API_KEY = "L-GT3c1LBZXbtCPo31OpoBLC0e8hEl2DCXFKN8bJZzp0zYODP_kIOr0UVYJC1NG1rXCzn4Fg0tdoGuVaupyEl-Cy3ohqLLmUsNf1KP6QI7jO7SdW6SekeqUrkFPwaHYx"  # 請替換為你的實際 API Key
YELP_CLIENT_ID = "duXHCGAP-WXzIfawKeK1Xw"  # 可選，通常不需要

# API 端點
YELP_BASE_URL = "https://api.yelp.com/v3"
YELP_SEARCH_ENDPOINT = "/businesses/search"

# 搜尋參數
DEFAULT_LOCATION = "Philadelphia, PA"
DEFAULT_RADIUS = 40000  # 40公里半徑，涵蓋整個費城地區
DEFAULT_LIMIT = 50  # 每次請求最多50筆結果

# API 使用限制 (遵守最嚴格限制)
DAILY_REQUEST_LIMIT = 100  # 每日最多100次請求 (Yelp AI API 限制)
MONTHLY_REQUEST_LIMIT = 5000  # 每月最多5000次請求 (Places API 限制)
SAFETY_BUFFER = 10  # 安全緩衝，預留10次請求避免超限

# 搜尋關鍵字列表
SEARCH_TERMS = [
    "BYOB Philadelphia",
    "bring your own wine Philadelphia", 
    "bring your own bottle Philadelphia",
    "corkage fee Philadelphia",
    "BYO Philadelphia",
    "BYOB restaurants Philadelphia",
    "bring wine Philadelphia",
    "corkage Philadelphia"
]

# 輸出檔案設定
OUTPUT_CSV = "philly_byob_restaurants.csv"
OUTPUT_JSON = "philly_byob_restaurants.json"
