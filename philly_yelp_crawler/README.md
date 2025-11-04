# 費城餐廳 Email 搜尋工具

## 功能說明

這是一個兩階段的 Email 搜尋工具：
- **第一階段（本程式）**：從 Excel 檔案讀取餐廳資料，使用 Google Places API 搜尋並取得 website
- **第二階段（待開發）**：從 website 搜尋 email 地址

## 檔案說明

- `philly_email_searcher.py` - 主要程式（第一階段：取得 website）
- `google_config.py` - Google Places API 設定檔
- `requirements.txt` - Python 套件依賴
- `data/Philly BYOB Restaurant.xlsx` - 輸入 Excel 檔案

## 使用方式

### 基本使用

```bash
python philly_email_searcher.py "data/Philly BYOB Restaurant.xlsx"
```

### 限制處理數量（測試用）

```bash
python philly_email_searcher.py "data/Philly BYOB Restaurant.xlsx" -n 10
```

## Excel 檔案格式要求

Excel 檔案必須包含以下欄位：
- **Name**：餐廳名稱（必需）
- **Add**：餐廳地址（可選，目前未使用）
- **Phone**：餐廳電話（可選，目前未使用）

## 輸出檔案

程式會產生新的 Excel 檔案，檔名格式為：
`原檔名_with_websites_YYYYMMDD_HHMMSS.xlsx`

新增的欄位：
- **Google_Website**：從 Google Places 取得的 website URL
- **Google_Place_ID**：Google Places ID
- **Google_Place_Name**：Google Places 中的餐廳名稱
- **Google_Address**：Google Places 中的地址
- **Search_Status**：搜尋狀態（found, not_found, no_website, error 等）
- **Search_Message**：錯誤訊息（如果有）

## API 限制處理

- 每次請求間有 0.2-0.5 秒的隨機延遲
- 自動重試機制（最多 3 次）
- 處理 API 配額限制
- 記錄所有操作到日誌檔案

## 日誌檔案

所有操作會記錄到 `philly_email_searcher.log`，包含：
- 搜尋進度
- API 請求狀態
- 錯誤訊息
- 統計資訊

## 注意事項

1. 確保 `google_config.py` 中有正確的 Google Places API Key
2. 注意 API 使用限制（每日和每月限制）
3. 程式會自動處理找不到餐廳的情況，繼續處理下一筆
4. 如果有多筆搜尋結果，會選擇第一個結果

