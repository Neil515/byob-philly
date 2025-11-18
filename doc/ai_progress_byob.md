# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-11-18）

### **台北 BYOB**（核心系統維運）
- 核心功能與自動化流程已完整，持續推廣、酒商合作與 Email 模板優化。

### **費城 BYOB**（主要開發重心）
- 前台英文化完成，持續拓展資料源、餐廳接管、自動寄信與社群推廣。
- 技術堆疊：WordPress + WooCommerce + ACF + WP-CLI + Python 資料腳本 + SendGrid。

---

## ✅ 2025年11月18日 — Token 批次、Email 流程與資料整併

### 🎯 今日成就總覽
- **資料整併 & 爬蟲擴充**
  - `update_1117_restaurants.py` 可針對 11/17、11/18 餐廳，一次查詢官網、Yelp、Latitude/Longitude、Email，並支援 `.env` 讀取 Google API/自訂金鑰。
  - Email 抓取流程優化：加入無效 email 過濾、搜尋 contact/about 頁面，並可寫入多欄（Email_1~Email_n）。
  - 新增 `merge_token_emails.py`（後續刪除）以便把 Excel 的 Email 欄位合併回 takeover token CSV。

- **Token 與批次寄信準備**
  - 在 Cloudways WordPress 建立 `wp-content/mu-plugins/byob-takeover-cli.php`，提供 `wp byob-takeovers batch` 指令，輸入 JSON/CSV 即可批次產生 takeover token、Takeover Link、CSV 輸出，並支援覆寫舊 token、寄送單一 Summary Email。
  - 以 `token_batch_memo.md` 記錄 Cloudways 操作流程（SSH 登入、路徑、指令、清理方式），確保下次可複製流程。
  - `sendgrid_test.py` 建立，讀 `takeover_tokens_20251118_copy.csv` 前兩筆，寄至 wavyclub21 / slow3605 測試；指示使用者將 API Key 放入環境變數，並提示 403 原因（Sender 未驗證或 Key 權限不足）。

- **計畫與文件**
  - `Next Task Prompt Byob.md` 更新：加入 11/19 的 SendGrid 批次發信、餐廳 Logo 補齊、餐廳類型篩選優化三大任務。
  - 說明環境變數與 `.env` 使用方式，避免在腳本內硬編 API Key。

### 🔧 主要修改檔案
- `philly_yelp_crawler/update_1117_restaurants.py`：整合網站/Yelp/LatLng/Email 流程，支援 `.env`、日期參數化。
- `philly_yelp_crawler/sendgrid_test.py`：建立 SendGrid 測試寄信腳本（最新版已刪除待重建）。
- `wp-content/mu-plugins/byob-takeover-cli.php`：WP-CLI 自訂指令，輸出 CSV、寄 Summary Email。
- `philly_yelp_crawler/token_batch_memo.md`：Cloudways/Token 產生備忘錄。
- `doc/Next Task Prompt Byob.md`：新增 2025-11-19 待辦。
- 其他臨時腳本（`merge_token_emails.py` 等）經分析後刪除。

### 📌 備註 / 重要決策
- **SendGrid 403**：使用者提供付費 API Key，若寄信遭 403，優先檢查 Sender Identity 驗證及 Key 權限（Mail Send）。
- **WP-CLI 指令命名**：為避免 `wp byob` 既有命名衝突，改註冊為 `wp byob-takeovers`。
- **資料合併**：Email 欄位以餐廳名稱對應，若 Excel 有多欄 Email，全部合併至 Token CSV，供後續批次寄信使用。

---

## ✅ 2025年11月17日 — 餐廳接管流程與 Email 任務準備

### 🎯 今日成就總覽
- 後台餐廳文章新增「Restaurant Takeover Link」meta box，可生成 30 天有效 token、記錄產生者與到期日。
- 完成接管流程：token 驗證、已註冊/未註冊接管、接管後自動登入並寄通知信到 `byobmap.tw@gmail.com`。
- 接管頁面全面英文化，按鈕/Checkbox 語系調整，避免 `text-transform` 造成大寫。
- 使用者列表新增 `Restaurant` 欄位，可直接連至餐廳文章並排序。
- `Next Task Prompt` 更新 11/18 任務：Email 爬取、Lat/Lng、批次寄信、資料夾整理。

### 🔧 主要修改檔案
- `wordpress/functions.php`：新增一系列 Takeover 相關函式（meta box、token 生成/驗證、通知信）、使用者列表欄位顯示。
- `doc/Next Task Prompt Byob.md`：記錄 11/18 待辦。

---

## ✅ 2025年11月16日 — 前台欄位切換、資料管線與名單更新

### 🎯 今日成就總覽
- 前台全部切換為費城欄位：`single_restaurant.php`、`archive-restaurant.php`、`restaurant-profile.php`、`restaurant-member-functions.php`。
- 修復 Corkage 顯示與完整度計算、確保新餐廳可顯示。
- 針對 11/16 新餐廳補齊官網、Yelp、Lat/Lng，並以 Python 腳本寫回 Excel。

### 🔧 主要修改檔案
- `wordpress/single_restaurant.php`、`archive-restaurant.php`、`woocommerce/myaccount/restaurant-profile.php`、`restaurant-member-functions.php`
- `doc/Next Task Prompt Byob.md`
- `philly_yelp_crawler/update_yelp_links.py`、`philly_yelp_crawler/update_latlng_1116.py`

---

## ✅ 2025年11月15日 — 地圖標記圖標優化與 Attribution 添加

### 🎯 今日成就總覽
- 自訂 SVG 地圖圖標調整（32px/40px），修正錨點與間距。
- 地圖下方新增 Flaticon Attribution、樣式微調。
- 相關修改檔案：`wordpress/assets/js/byob-nearby.js`、`wordpress/archive-restaurant.php`。

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

**下一步重點（11/17）：**
* 📧 餐廳業者 Email 建立與驗證（歡迎/啟用）
* 🧭 檢查餐廳業者後台欄位與權限流程
* 🔄 「先網友推薦、後餐廳加入」資料流巡檢與事件串接
* 🏷️ 列表「餐廳類型」點選篩選（前台 UX + Query）
* ⏳ FAQ/後台英文化、Reddit 回覆節奏、資料衝突處理、社群啟動、上線與招募、榮譽系統

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
* **地圖與定位系統**：Google Maps JavaScript API、客製 SVG 圖標、Attribution、定位排序
* `.env` 檔案 API key 管理：Python 爬蟲 / SendGrid / WordPress 指令

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統、重複檢查系統

---

