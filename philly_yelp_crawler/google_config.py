# Google Places API 設定檔案
# 請將下面的 YOUR_GOOGLE_API_KEY_HERE 替換為你的實際 Google Places API Key

# API 憑證（請透過環境變數 GOOGLE_API_KEY 或設定檔注入）
import os

GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY")
if not GOOGLE_API_KEY:
    raise RuntimeError("未設定環境變數 GOOGLE_API_KEY，請在執行前提供有效的 Google Places API Key")

# API 端點
GOOGLE_PLACES_BASE_URL = "https://maps.googleapis.com/maps/api/place"
GOOGLE_PLACES_SEARCH_ENDPOINT = "/textsearch/json"

# 搜尋參數
DEFAULT_LOCATION = "Philadelphia, PA"
DEFAULT_RADIUS = 40000  # 40公里半徑，涵蓋整個費城地區
DEFAULT_LIMIT = 20  # Google Places 每次請求最多20筆結果

# API 使用限制 (嚴格控制在免費額度內)
# Google Places API 免費額度：每月 100,000 次請求
DAILY_REQUEST_LIMIT = 3000  # 每日限制（100,000/30天 ≈ 3,333，設定為3000）
MONTHLY_REQUEST_LIMIT = 90000  # 每月限制（預留10,000次給台北專案）
SAFETY_BUFFER = 100  # 安全緩衝，預留100次請求避免超限

# 搜尋關鍵字列表
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
OUTPUT_CSV = "data/google_places_results.csv"
OUTPUT_JSON = "data/google_places_results.json"
