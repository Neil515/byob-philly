# TripAdvisor 爬蟲設定檔案
# 注意：此爬蟲使用網頁搜尋，需要遵守 TripAdvisor 的使用條款

# TripAdvisor 設定
TRIPADVISOR_BASE_URL = "https://www.tripadvisor.com"

# 請求設定
REQUEST_DELAY = 3  # 請求間隔（秒），避免被阻擋
MAX_RETRIES = 3    # 最大重試次數
TIMEOUT = 30       # 請求超時時間（秒）

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
OUTPUT_CSV = "data/tripadvisor_results.csv"
OUTPUT_JSON = "data/tripadvisor_results.json"

# 注意事項
"""
使用此爬蟲時請注意：

1. 遵守 TripAdvisor 的使用條款
2. 不要過於頻繁地發送請求
3. 尊重網站的 robots.txt 檔案
4. 僅用於個人學習和研究目的
5. 不要用於商業用途

如果遇到存取限制，請：
- 增加請求間隔時間
- 使用代理伺服器
- 或考慮使用 TripAdvisor 的官方 API
"""
