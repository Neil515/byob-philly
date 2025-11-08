# 費城餐廳 Email 搜尋工具

## 功能說明

這是一個兩階段的 Email 搜尋工具：
- **第一階段**：從 Excel 檔案讀取餐廳資料，使用 Google Places API 搜尋並取得 website
- **第二階段**：從 website 搜尋 email 地址

## 檔案說明

- `philly_email_searcher.py` - 第一階段程式（取得 website）
- `philly_email_extractor.py` - 第二階段程式（從 website 搜尋 email）
- `google_yelp_lookup.py` - 使用 Google Places + Programmable Search 取得餐廳類型與 Yelp 連結
- `google_config.py` - Google Places API 設定檔
- `requirements.txt` - Python 套件依賴
- `data/Philly BYOB Restaurant.xlsx` - 輸入 Excel 檔案

## 新工具：Google Places + Yelp 連結查詢

此腳本會讀取包含 **Name / Add / Phone** 欄位的 Excel，流程如下：
1. 使用 Google Places API 以「餐廳名稱 + Philadelphia」搜尋候選地點。
2. 以 Excel 地址與 Google 回傳的地址比對，確認為同一家餐廳。
3. 取回前兩個餐廳類型（types）。
4. 使用 Google Programmable Search (Custom Search API) 搜尋 `site:yelp.com`，抓取第一個 Yelp 連結。

### 需求
- `.env` 內需設定：
  ```
  GOOGLE_PLACES_API_KEY=...
  GOOGLE_CUSTOM_SEARCH_API_KEY=...
  GOOGLE_CUSTOM_SEARCH_CX=...
  ```
- 安裝 `requirements.txt` 提到的套件（包含 `python-dotenv`、`requests`、`pandas` 等）。

### 執行
```bash
python google_yelp_lookup.py "data/Philly BYOB Restaurant.xlsx"
```
- 可選參數：
  - `--output result.xlsx`：指定輸出檔案。
  - `--limit 10`：僅處理前 10 筆做測試。
  - `--verbose`：顯示除錯資訊。

輸出檔案包含以下欄位：
- `Matched_Name` / `Matched_Address`：Google Places 回傳資訊。
- `Type_1` / `Type_2`：前兩個餐廳類型。
- `Yelp_URL`：第一次搜尋到的 Yelp 連結（若無則留空）。
- `Match_Status` / `Message`：比對及搜尋狀態說明。

## 使用方式

### 步驟 1：取得 Website

#### 基本使用
```bash
python philly_email_searcher.py "data/Philly BYOB Restaurant.xlsx"
```

#### 限制處理數量（測試用）
```bash
python philly_email_searcher.py "data/Philly BYOB Restaurant.xlsx" -n 10
```

### 步驟 2：從 Website 搜尋 Email

#### 基本使用
```bash
python philly_email_extractor.py "data/Philly BYOB Restaurant_with_websites_xxx.xlsx"
```

#### 限制處理數量（測試用）
```bash
python philly_email_extractor.py "data/Philly BYOB Restaurant_with_websites_xxx.xlsx" -n 10
```

## Excel 檔案格式要求

Excel 檔案必須包含以下欄位：
- **Name**：餐廳名稱（必需）
- **Add**：餐廳地址（可選，目前未使用）
- **Phone**：餐廳電話（可選，目前未使用）

## 輸出檔案

### 步驟 1 輸出

程式會產生新的 Excel 檔案，檔名格式為：
`原檔名_with_websites_YYYYMMDD_HHMMSS.xlsx`

新增的欄位：
- **Google_Website**：從 Google Places 取得的 website URL
- **Google_Place_ID**：Google Places ID
- **Google_Place_Name**：Google Places 中的餐廳名稱
- **Google_Address**：Google Places 中的地址
- **Search_Status**：搜尋狀態（found, not_found, no_website, error 等）
- **Search_Message**：錯誤訊息（如果有）

### 步驟 2 輸出

程式會產生新的 Excel 檔案，檔名格式為：
`原檔名_with_websites_xxx_with_emails_YYYYMMDD_HHMMSS.xlsx`

新增的欄位：
- **Email**：找到的 email 地址（優先選擇最相關的）
- **Email_Status**：搜尋狀態（found, not_found, no_website, error 等）
- **Email_Message**：搜尋訊息
- **Email_All_Found**：所有找到的 email（用分號分隔）

## API 限制處理

- 每次請求間有 0.2-0.5 秒的隨機延遲
- 自動重試機制（最多 3 次）
- 處理 API 配額限制
- 記錄所有操作到日誌檔案

## 日誌檔案

- **步驟 1**：所有操作記錄到 `philly_email_searcher.log`
- **步驟 2**：所有操作記錄到 `philly_email_extractor.log`

日誌包含：
- 搜尋進度
- API/HTTP 請求狀態
- 錯誤訊息
- 統計資訊

## 注意事項

### 步驟 1 注意事項
1. 確保 `google_config.py` 中有正確的 Google Places API Key
2. 注意 API 使用限制（每日和每月限制）
3. 程式會自動處理找不到餐廳的情況，繼續處理下一筆
4. 如果有多筆搜尋結果，會選擇第一個結果

### 步驟 2 注意事項
1. 輸入檔案必須是步驟 1 的輸出檔案（包含 `Google_Website` 欄位）
2. 每次請求間有 1-2 秒的隨機延遲，避免被封鎖
3. 程式會搜尋多個常見頁面（首頁、聯絡頁面等）
4. 如果找到多個 email，會優先選擇最相關的（包含餐廳名稱或常見前綴如 info@, contact@）
5. 所有找到的 email 都會記錄在 `Email_All_Found` 欄位中

