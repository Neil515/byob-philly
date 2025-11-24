# push_acf_latlng.py 使用說明

這支腳本把 Excel 裡的 `WP_Post_ID + Latitude + Longitude` 批次寫回 WordPress 餐廳文章的 ACF 欄位。

## 邏輯（白話版）
1. 讀入指定 Excel，抓每列的 Post ID & 經緯度欄位。
2. 用 `.env`/環境變數提供的帳號、Application Password 連到 `https://byobmap.com/wp-json/acf/v3/restaurant/<post_id>`.
3. 對每筆有值的列，呼叫 ACF REST API 更新 `latitude`、`longitude` 欄位。
4. 缺 ID 或經緯度會跳過；成功／跳過都會印在終端機。

## 事前準備
```powershell
$Env:WP_API_BASE_URL = "https://byobmap.com/wp-json/acf/v3/restaurant"
$Env:WP_API_USERNAME = "<WP 帳號或 Application Password 的使用者>"
$Env:WP_API_PASSWORD = "<Application Password>"
```
（如果 ACF 欄位名稱不是 `latitude`/`longitude`，另外設 `$Env:WP_LAT_FIELD_NAME`、`$Env:WP_LNG_FIELD_NAME`。）

• WordPress 後台必須已啟用「ACF to REST API」外掛，不然 `/acf/v3/...` 會報 404。  
• 密碼建議使用 Application Password，避免動到登入密碼。

## 指令範例
```powershell
cd C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB
# 預設欄位就叫 WP_Post_ID / Latitude_New / Longitude_New
python philly_yelp_crawler/push_acf_latlng.py "data/Philly BYOB Restaurant_with_websites_merged.xlsx"

# 欄位名稱不同（例如 Latitude / Longitude）就明確寫出
python philly_yelp_crawler/push_acf_latlng.py "data/geocode_full/Philly BYOB Restaurant google form_with_latlng.xlsx" WP_Post_ID Latitude Longitude
```

## 遇到的問題 & 解法
- **API 404 (`rest_no_route`)**  
  原因是 WordPress 沒有 `/acf/v3/...`，後來裝了「ACF to REST API」並確認路徑是 `acf/v3/restaurant` 才成功。
- **密碼錯誤 (`incorrect_password`)**  
  建 Application Password 後，用 `$Env:WP_API_USERNAME` + `$Env:WP_API_PASSWORD` 設定，重新過環境變數即可。
- **只顯示 1 筆就中斷**  
  通常是 API 權限/路由錯誤，修正上面兩點後即可正常跑完整批。

