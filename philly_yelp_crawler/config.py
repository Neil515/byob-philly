# 費城 Yelp BYOB 餐廳爬蟲配置檔案
# Philadelphia Yelp BYOB Restaurant Crawler Configuration

import random
from fake_useragent import UserAgent

# 搜尋關鍵字配置
SEARCH_KEYWORDS = [
    "BYOB Philadelphia",
    "bring your own wine Philadelphia", 
    "bring your own bottle Philadelphia",
    "corkage fee Philadelphia",
    "BYO Philadelphia",
    "BYOB restaurants Philadelphia",
    "bring wine Philadelphia",
    "corkage Philadelphia"
]

# 費城主要區域（用於更精確的搜尋）
PHILLY_NEIGHBORHOODS = [
    "Center City",
    "Rittenhouse Square",
    "Old City",
    "Society Hill", 
    "Queen Village",
    "South Street",
    "Fishtown",
    "Northern Liberties",
    "University City",
    "Manayunk",
    "East Passyunk",
    "Fairmount"
]

# 請求設定
REQUEST_DELAY = (3, 5)  # 隨機延遲 3-5 秒
MAX_RETRIES = 3
TIMEOUT = 30

# User-Agent 設定
def get_random_user_agent():
    """獲取隨機 User-Agent"""
    ua = UserAgent()
    return ua.random

# 輸出檔案設定
OUTPUT_CSV = "philly_byob_restaurants.csv"
OUTPUT_JSON = "philly_byob_restaurants.json"
LOG_FILE = "crawler_log.txt"

# 資料欄位定義
RESTAURANT_FIELDS = [
    "restaurant_name",
    "address", 
    "phone",
    "website",
    "cuisine_type",
    "yelp_url",
    "search_keyword",
    "confidence_level",
    "neighborhood",
    "crawl_date",
    "notes"
]

# 信心度評估標準
CONFIDENCE_CRITERIA = {
    "high": ["BYOB", "bring your own", "corkage"],
    "medium": ["wine", "bottle", "alcohol"],
    "low": ["restaurant", "dining", "food"]
}
