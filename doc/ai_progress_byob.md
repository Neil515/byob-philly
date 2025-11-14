# BYOB 專案開發進度記錄

## 📅 專案概覽

### **台北 BYOB 專案**（主要專案）
* **專案名稱**：BYOB 台北 - 自帶酒水餐廳推薦平台
* **目前階段**：核心系統完成，推廣與酒商合作階段
* **核心功能**：餐廳推薦表單、重複檢查、審核管理、抽獎系統、Email 通知、多平台推廣
* **技術架構**：WordPress + ACF + Google Apps Script + Python 爬蟲工具

### **費城 BYOB 專案**（新專案）
* **專案名稱**：Philadelphia BYOB Restaurant Guide
* **目前階段**：網站前台英文化完成，準備 FAQ 英文化、後台英文化與 Reddit 回覆工作
* **核心策略**：多平台資料收集 + 雙向表單驗證 + 自動化文章生成 + Reddit 社群互動 + 餐廳聯絡機制
* **定位**：成為 Yelp 的 BYOB 專業補充平台

---

## ✅ 2025年11月14日 — 「離你最近的 BYOB 餐廳」功能完成

### 🎯 今日成就總覽
- **地圖功能實作**：在餐廳列表頁最上方加入 Google Maps 地圖區塊，整合 Google Maps JavaScript API，實作 HTML5 Geolocation 定位功能。定位成功時顯示使用者位置與最近餐廳，失敗時自動縮放顯示全部費城餐廳。
- **最近 5 間餐廳列表**：地圖下方顯示「Closest 5 Restaurants」區塊，使用 Haversine 公式計算距離並排序。桌機版支援標記 hover 互動，行動版支援點擊 InfoWindow。
- **餐廳列表排序優化**：實作多層級排序邏輯（驗證狀態 > 資料完整度 > 餐廳照片 > 距離 > 收藏數 > 名稱），前端 JavaScript 自動排序確保列表依權重正確排列。
- **技術實作**：新增 `wordpress/assets/js/byob-nearby.js`，在 `archive-restaurant.php` 輸出餐廳經緯度與排序權重，在 `wp-config.php` 實作 `.env` 讀取功能優先讀取 API key。
- **修正項目**：修正 API key 讀取問題（最終採用 `.env` 讀取），移除多餘提示文字，關閉 `WP_DEBUG`。

### 後續方向
- 11/15 處理首頁精選餐廳功能與發給餐廳的確認 Email 轉寫。

---

## ✅ 2025年11月13日 — 經緯度批次產出與資料對齊

### 🎯 今日成就總覽
- 啟用新的 Google Places / Geocoding API key，成功批次產出兩份清單的 `Latitude` / `Longitude`。
- 建立 `add_ids.py` 腳本，為 Excel 新增 `ID` 欄位（主清單 19/43 成功、表單 16/20 成功）。
- 規劃將經緯度資料與餐廳排序功能整合。

---

## ✅ 2025年11月12日 — LOGO fallback 與 Nearby 功能準備

### 🎯 今日成就總覽
- 前台 `archive-restaurant.php` 與 `single_restaurant.php` 更新 LOGO fallback 流程，優先採用 ACF `restaurant_logo`，並兼容 `_restaurant_logo` 與舊欄位 `restaurant_photo`。
- ACF 新增 `Latitude` / `Longitude` 欄位，建立 `geocode_restaurant_locations.py` 腳本，可讀取 `.env` 中的 `GOOGLE_PLACES_API_KEY` 與 `GOOGLE_API_KEY`，自動辨識 `Name/Add` 欄位並輸出 `Latitude`、`Longitude`、`Geocode_Status`、`Matched_Address`。
- 腳本初步執行完成資料管線，確立失敗清單輸出與欄位寫入格式，待提升查詢比對邏輯與成功率。
- `Next Task Prompt Byob` 更新 11/13 排程：批次產出座標、寫回 ACF 與實作「Find BYOB Near Me」距離排序功能。

### 後續方向
- 11/13 先調整 geocode 腳本查詢策略（Places/Geocode 比對、重試、距離檢核）並小量驗證，再批次產出餐廳座標與失敗名單。
- 規劃將座標寫入 WordPress ACF（REST API 或 WP-CLI）流程，並設計前台距離排序 + 授權/ZIP code fallback 的互動體驗。

---

## ✅ 2025年11月11日 — BYOB Service 調整與後續規劃

### 🎯 今日成就總覽
- **BYOB Service 顯示優化**：`single_restaurant.php` 讀取 `byob_service_level`，支援舊欄位回退並統一顯示標籤文字；`archive-restaurant.php` 同步保留新邏輯（暫時註解待啟用）。
- **欄位來源盤點**：確認 `_restaurant_logo` 為前台與業者後台的主要 LOGO 來源，釐清 ACF `restaurant_photo` 與 meta 的差異。
- **後台選單英文化準備**：檢視 CPT UI 設定，確認只需調整 `Plural/Singular Label` 即可改為英文，避免影響程式碼（slug 維持 `restaurant`）。
- **Placeholder 規劃**：確定示意圖將內嵌文字「Placeholder Image – Awaiting Official Restaurant Logo」，待明日批次套用。

### 後續方向
- 11/12 依 `Next Task Prompt Byob` 執行示意圖 fallback、餐廳排序調整、業者通知 Email 規劃。
- 評估 LOGO 欄位同步機制，必要時在儲存流程將 `restaurant_photo` 寫入 `_restaurant_logo`。

---

## ✅ 2025年11月10日 — 費城餐廳類型與後台欄位調整

### 🎯 今日成就總覽
- **餐廳類型欄位英文化**：將業者後台的餐廳類型改用費城專屬 15 個英文選項，並同步儲存至 `philly_restaurant_type / _other_note`，確保前台顯示與資料一致。  
- **地址驗證規則調整**：移除原本限定台灣地址的檢核邏輯，改為僅要求填寫 Email，支援美國地址格式。  
- **前台顯示同步**：`byob_get_restaurant_type_labels()` 優先讀取費城欄位，避免再出現中文標籤。  
- **工作規劃更新**：重寫 `doc/Next Task Prompt Byob.md`，列出 11/11 要處理的三項任務（單頁 LOGO、照片欄位同步、Rank Math 標題）。

### 影響與後續
- 業者與管理員無論在哪裡上傳、修改類型資料，前台都能顯示正確的英文類型與備註。  
- 海外餐廳可直接儲存地址資訊，不會再被「市/區/路」規則擋下。  
- 明日聚焦 UI/媒體同步與 SEO 標題調整，持續優化費城前台體驗。

---

## ✅ 2025年11月8日 — Google Places + Yelp 整合，前台類型顯示修正

### 🎯 今日成就總覽
- **Google 資料整合腳本**：新增 `philly_yelp_crawler/google_yelp_lookup.py`，讀取 `Name/Add/Phone` Excel，透過 Google Places 驗證地址並萃取餐廳類型，搭配 Programmable Search 找出 Yelp 連結；支援 `.env` 自動載入 `GOOGLE_PLACES_API_KEY`、`GOOGLE_CUSTOM_SEARCH_API_KEY`、`GOOGLE_CUSTOM_SEARCH_CX`。  
  - 產出結果檔：`Philly BYOB Restaurant_with_google_yelp_20251108_153121.xlsx`，含 `Type_1/Type_2/Yelp_URL/Match_Status`。
  - README 新增使用說明、指令參數與環境需求。
- **前台類型顯示修正**：在 `wordpress/functions.php` 新增 `byob_get_restaurant_type_labels()`，統一取得 `restaurant_type` / `philly_restaurant_type`、處理 `Other` 備註與 title case。`archive-restaurant.php`、`single_restaurant.php` 改用新函式，費城餐廳類型得以正常呈現。
- **工作規劃更新**：`doc/Next Task Prompt Byob.md` 更新日期為 2025-11-08，並排定 11/9 的兩項任務：
  1. 放大列表頁分頁按鈕。
  2. Yelp 與電話欄位二擇一顯示。

### 影響與後續
- Excel 匯出現在包含雙渠道類型資料，後續可作為人工驗證或後台批次匯入依據。
- 前台類型顯示與費城資料同步，避免因欄位差異出現空白。
- 明日優先處理列表分頁 UX 與聯絡資訊顯示邏輯。

---

## ✅ 2025年11月6日 — 前台英文化與評論功能移除

### 🎯 今日成就總覽
- 餐廳列表、單頁、註冊表單、Contact Form 全面改為英文；維護註解保留中文。
- 移除未完成的評論與評分相關檔案，保持程式碼庫整潔。

---

## ✅ 2025年11月5日 — 重複檢查與資料追蹤優化

### 🎯 今日成就總覽
- `byob_check_duplicate_restaurant()` 新增專案參數、日誌與地址縮寫處理。
- 建立 `recommendation_count` 欄位、標題加註 `(重複)`、相似度權重調整。

---

## ✅ 2025年11月4日 — Email 搜尋系統

### 🎯 今日成就總覽
- 兩階段腳本 `philly_email_searcher.py` + `philly_email_extractor.py` 完成，支援重試、日誌、Excel 匯出。

---

## ✅ 2025年11月3日 — 驗證徽章系統與 Yelp 連結整合

### 🎯 今日成就總覽
- **驗證徽章系統**：前台顯示驗證狀態徽章（Verified by Restaurant / Community Recommended），後台新增 `verification_override` 欄位供管理員手動覆蓋。
- **Yelp 連結整合**：表單、Apps Script、WordPress 後端、前台顯示完整整合，聚焦單一外部平台連結。
- **前端格式統一**：統一所有欄位標籤冒號後空格格式。

---

## ✅ 2025年11月1日 — 費城餐廳確認表單系統建立

### 🎯 今日成就總覽
- **雙表單系統**：建立專為餐廳老闆設計的確認表單，與網友推薦表單區分資料來源。
- **專屬 Apps Script**：獨立處理流程，Email 通知區分來源和格式。
- **API 修復**：修復 `source` 欄位硬編碼問題，後台可正確辨識資料來源。

---

## 📊 專案整體進度

### 🍷 台北 BYOB 專案

**已完成核心模組：**
* ✅ 餐廳表單系統、WordPress 整合、推薦通知系統
* ✅ 重複檢查系統、審核管理系統、抽獎系統
* ✅ 多平台推廣、酒商名單收集

**進行中：**
* 🔄 酒商合作邀約 Email 擬定
* 🔄 Facebook 品酒社團規則確認和推廣

**待開發：**
* ⏳ 自動回覆系統、KPI 追蹤儀表板

---

### 🍷 費城 BYOB 專案

**已完成：**
* ✅ **專案規劃**：市場分析、AD 方案、榮譽系統設計
* ✅ **資料收集**：269 家候選餐廳（Yelp + Google Places）
* ✅ **爬蟲系統**：多平台整合、智能去重、信心度評估
* ✅ **雙表單系統**：網友推薦表單 + 老闆確認表單
* ✅ **自動化整合**：雙 Apps Script + WordPress API
* ✅ **Reddit 準備**：帳號建立、貼文策略、追蹤系統
* ✅ **系統優化**：英文化、ACF 修復、顯示邏輯優化
* ✅ **驗證徽章系統**：前台顯示與後台管理機制
* ✅ **Yelp 連結整合**：表單、後端、前台完整整合
* ✅ **餐廳 Email 搜尋系統**：兩階段自動化工具（11/4）
* ✅ **重複餐廳處理機制**：智能去重與標記（11/5）
* ✅ **網站前台英文化**：列表、單頁、表單全面英文化（11/6）
* ✅ **經緯度資料產出**：批次產出餐廳座標資料（11/13）
* ✅ **離你最近的 BYOB 餐廳功能**：地圖、定位、距離排序（11/14）

**當前狀態：**
* ✅ 兩套完整表單系統運作中
* ✅ 資料來源自動辨識與追蹤
* ✅ 驗證狀態視覺化展示
* ✅ 地圖與定位功能完成
* ✅ 餐廳列表多層級排序邏輯完成

**下一步重點：**
* 🚀 首頁精選餐廳功能（11/15）
* 🚀 發給餐廳的確認 Email 轉寫（11/15）
* ⏳ FAQ 英文化、後台英文化、Reddit 回覆工作、多名網友資料衝突處理邏輯、Reddit 社群互動啟動、網站上線、用戶招募、榮譽系統

---

## 📝 技術工具與資源

### **費城專案工具**
* **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
* **智能去重系統**：保留來源資訊的資料整合
* **信心度評估**：High/Medium/Low 三級評分系統
* **Reddit 互動追蹤系統**：完整的管理和分析工具
* **Google 表單系統**：雙表單（推薦 + 確認）
* **Apps Script 自動化**：推薦版 + 確認版獨立處理
* **WordPress API 整合**：動態 source 處理、自動生成草稿
* **自動化文章生成系統**：英文內容模板和 SEO 優化
* **雙重通知系統**：成功和錯誤通知機制
* **驗證徽章系統**：前台顯示與後台管理
* **Yelp 連結整合**：表單、後端、前台完整整合
* **餐廳 Email 搜尋系統**：兩階段自動化搜尋工具
  - Google Places API 搜尋（取得 website）
  - Website email 提取（搜尋 email）
  - 支援多個 email 自動展開
* **地圖與定位系統**：Google Maps JavaScript API 整合
  - HTML5 Geolocation 定位功能
  - Haversine 公式距離計算
  - 地圖標記互動（hover/click）
  - 前端多層級排序邏輯
  - `.env` 檔案 API key 管理

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統、重複檢查系統

---

