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

## ✅ 2025年11月15日 — 地圖標記圖標優化與 Attribution 添加

### 🎯 今日成就總覽
- **地圖標記圖標調整**：將自定義 SVG 圖標（`placeholder.svg`）尺寸從 64x64 調整為 32x32 像素（默認），高亮圖標從 72x72 調整為 40x40 像素，調整錨點位置確保正確對齊。
- **Attribution 添加**：在地圖下方添加圖標來源 attribution（Flaticon 連結），樣式為小字體、靠右對齊、灰色文字，懸停時變為品牌色。
- **間距調整**：增加地圖與 "Closest 5 Restaurants" 之間的間距（從 24px 調整為 40px）。
- **修改文件**：`wordpress/assets/js/byob-nearby.js`、`wordpress/archive-restaurant.php`。

### 後續方向
- 11/16 處理發給餐廳的 Email 優化與餐廳業者與既有文章建立連結功能。

---

## ✅ 2025年11月14日 — 「離你最近的 BYOB 餐廳」功能完成

### 🎯 今日成就總覽
- **地圖功能實作**：在餐廳列表頁最上方加入 Google Maps 地圖區塊，整合 Google Maps JavaScript API，實作 HTML5 Geolocation 定位功能。定位成功時顯示使用者位置與最近餐廳，失敗時自動縮放顯示全部費城餐廳。
- **最近 5 間餐廳列表**：地圖下方顯示「Closest 5 Restaurants」區塊，使用 Haversine 公式計算距離並排序。桌機版支援標記 hover 互動，行動版支援點擊 InfoWindow。
- **餐廳列表排序優化**：實作多層級排序邏輯（驗證狀態 > 資料完整度 > 餐廳照片 > 距離 > 收藏數 > 名稱），前端 JavaScript 自動排序確保列表依權重正確排列。
- **技術實作**：新增 `wordpress/assets/js/byob-nearby.js`，在 `archive-restaurant.php` 輸出餐廳經緯度與排序權重，在 `wp-config.php` 實作 `.env` 讀取功能優先讀取 API key。
- **修正項目**：修正 API key 讀取問題（最終採用 `.env` 讀取），移除多餘提示文字，關閉 `WP_DEBUG`。

---

## ✅ 2025年11月13日 — 經緯度批次產出與資料對齊

### 🎯 今日成就總覽
- 啟用新的 Google Places / Geocoding API key，成功批次產出兩份清單的 `Latitude` / `Longitude`。
- 建立 `add_ids.py` 腳本，為 Excel 新增 `ID` 欄位，規劃將經緯度資料與餐廳排序功能整合。

---

## ✅ 2025年11月12日 — LOGO fallback 與 Nearby 功能準備

### 🎯 今日成就總覽
- 前台更新 LOGO fallback 流程，優先採用 ACF `restaurant_logo`，並兼容舊欄位。
- ACF 新增 `Latitude` / `Longitude` 欄位，建立 `geocode_restaurant_locations.py` 腳本，自動辨識並輸出座標資料。

---

## ✅ 2025年11月11日 — BYOB Service 調整與後續規劃

### 🎯 今日成就總覽
- BYOB Service 顯示優化，支援舊欄位回退並統一顯示標籤文字。
- 確認 `_restaurant_logo` 為前台與業者後台的主要 LOGO 來源，釐清欄位差異。

---

## ✅ 2025年11月10日 — 費城餐廳類型與後台欄位調整

### 🎯 今日成就總覽
- 餐廳類型欄位英文化，改用費城專屬 15 個英文選項，確保前台顯示與資料一致。
- 地址驗證規則調整，移除台灣地址限制，支援美國地址格式。

---

## ✅ 2025年11月8日 — Google Places + Yelp 整合

### 🎯 今日成就總覽
- 新增 `google_yelp_lookup.py` 腳本，透過 Google Places 驗證地址並萃取餐廳類型，搭配 Programmable Search 找出 Yelp 連結。
- 前台類型顯示修正，新增 `byob_get_restaurant_type_labels()` 統一處理類型顯示。

---

## ✅ 2025年11月1-6日 — 系統優化與功能完善

### 🎯 主要成就
- **11/6**：前台英文化完成，移除未完成的評論功能。
- **11/5**：重複檢查系統優化，新增 `recommendation_count` 欄位與相似度權重調整。
- **11/4**：Email 搜尋系統完成，兩階段自動化工具支援重試與日誌。
- **11/3**：驗證徽章系統與 Yelp 連結整合完成。
- **11/1**：費城餐廳確認表單系統建立，雙表單系統區分資料來源。

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
* ✅ **地圖標記圖標優化**：自定義 SVG 圖標、Attribution 添加（11/15）

**當前狀態：**
* ✅ 兩套完整表單系統運作中
* ✅ 資料來源自動辨識與追蹤
* ✅ 驗證狀態視覺化展示
* ✅ 地圖與定位功能完成
* ✅ 餐廳列表多層級排序邏輯完成

**下一步重點：**
* 🚀 發給餐廳的 Email 優化（11/16）
* 🚀 餐廳業者與既有文章建立連結功能（11/16）
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
  - 自定義 SVG 圖標與 Attribution
  - `.env` 檔案 API key 管理

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統、重複檢查系統

---

