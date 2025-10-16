# 主設定檔案 - 費城 BYOB 餐廳爬蟲
# Main Configuration File - Philadelphia BYOB Restaurant Crawler

# 爬蟲平台設定
CRAWLERS = {
    'yelp': True,           # 啟用 Yelp 爬蟲
    'google_places': True,  # 啟用 Google Places 爬蟲（已設定 API Key）
    'tripadvisor': False    # 暫時關閉 TripAdvisor 爬蟲（網頁結構問題）
}

# 輸出格式設定
OUTPUT_FORMATS = ['csv', 'json', 'excel']

# 信心度閾值
CONFIDENCE_THRESHOLD = 0.7

# 搜尋關鍵字（所有平台共用）
SEARCH_TERMS = [
    "BYOB Philadelphia",
    "bring your own wine Philadelphia", 
    "bring your own bottle Philadelphia",
    "corkage fee Philadelphia",
    "BYO Philadelphia",
    "BYOB restaurants Philadelphia",
    "bring wine Philadelphia",
    "corkage Philadelphia",
    "restaurants allow bring wine Philadelphia",
    "BYOB policy Philadelphia",
    "corkage allowed Philadelphia"
]

# 輸出檔案設定
OUTPUT_FILES = {
    'combined_csv': 'data/combined_byob_restaurants.csv',
    'combined_json': 'data/combined_byob_restaurants.json',
    'combined_excel': 'data/combined_byob_restaurants.xlsx',
    'high_confidence_csv': 'data/high_confidence_byob_restaurants.csv',
    'crawl_report': 'data/crawl_report.json'
}

# 日誌設定
LOG_FILES = {
    'main_log': 'logs/main_crawler.log',
    'yelp_log': 'logs/yelp_crawler.log',
    'google_log': 'logs/google_crawler.log',
    'tripadvisor_log': 'logs/tripadvisor_crawler.log'
}

# 爬取設定
CRAWL_SETTINGS = {
    'max_retries': 3,
    'request_delay': 1,
    'timeout': 30,
    'enable_logging': True
}

# 資料處理設定
DATA_PROCESSING = {
    'remove_duplicates': True,
    'confidence_ranking': True,
    'generate_report': True,
    'save_individual_results': True
}
