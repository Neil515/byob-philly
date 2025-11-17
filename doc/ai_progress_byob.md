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

## ✅ 2025年11月17日 — 餐廳接管流程與 Email 任務準備

### 🎯 今日成就總覽
- **餐廳接管流程上線**  
  - 在後台餐廳文章右側新增「Restaurant Takeover Link」meta box，可生成 30 天有效、可重複使用的 token，並自動記錄產生者與到期時間。  
  - 新增 `byob_generate_restaurant_takeover_token()`、`byob_handle_restaurant_takeover_page()` 等流程，處理 token 驗證、接管頁面顯示、已註冊/未註冊業者接管、取代舊業者等情境。  
  - 完成接管後自動登入並導回 `restaurant-profile`，同時寄送通知信至 `byobmap.tw@gmail.com`，信件含餐廳資訊與後台連結。  
  - 接管頁面全部改為英文介面，並覆寫按鈕 `text-transform`，維持正確大小寫。

- **後台使用者檢視強化**  
  - 在 WordPress 使用者列表新增 `Restaurant` 欄位，可排序並直接連往餐廳文章，方便管理者查看每位餐廳業者與其所屬餐廳。

- **Next Task / 明日任務更新**  
  - 將 11/18 需進行的四大工作寫入 `Next Task Prompt Byob.md`：  
    1. 對 `Philly BYOB Restaurant_with_websites_merged_20251116.xlsx` 中 Date=2025-11-16 的餐廳進行官網/Email 爬取，按舊檔格式 append 至 `Philly BYOB Restaurant_with_websites_20251104_142325_with_emails_20251106_114433.xlsx`。  
    2. 補齊同檔案缺失的 Latitude/Longitude。  
    3. 準備批次發送邀請 Email（含 takeover link 與前台連結）。  
    4. 清理 `philly_yelp_crawler` 資料夾與 README。

- **資料介面與文檔**  
  - `Next Task Prompt Byob.md` 更新至 11/17，加入 11/18 具體任務。  
  - 規劃 Email 發送流程：未來會以 Sheets + Apps Script 合併方式寄信，並提供 takeover link 及餐廳展示連結。

### 🔧 主要修改檔案
- `wordpress/functions.php`
  - `byob_add_restaurant_takeover_meta_box()`、`byob_handle_generate_takeover_token()`、`byob_handle_restaurant_takeover_page()`、`byob_process_restaurant_takeover()`、`byob_send_takeover_notification()` 等一系列新函式。  
  - takeover 頁面 UI/文案調整（標題改為 *Restaurant Access Transfer*、按鈕改為 *Claim Your Restaurant*、checkbox 提示為英文、自訂樣式避免全大寫）。  
  - `byob_get_takeover_notification_email()` 支援自訂通知信箱。  
  - 新增 `manage_users_columns` 與 `manage_users_sortable_columns`，顯示/排序 `Restaurant` 欄位。
- `doc/Next Task Prompt Byob.md`：更新 11/18 工作。

---

## ✅ 2025年11月16日 — 前台欄位切換、資料管線與名單更新

### 🎯 今日成就總覽
- 前台全面切換為費城欄位（移除台北舊鍵回退）
  - 單頁 `wordpress/single_restaurant.php`、列表 `wordpress/archive-restaurant.php`
  - 開瓶費顯示改用 `philly_corkage_fee` + `corkage_fee_amount` + `corkage_fee_note`
  - 修復單頁 PHP/HTML 邊界語法錯誤（造成嚴重錯誤的來源）
- 列表顯示修正
  - 以新欄位輸出「Corkage Fee / Corkage Details」
  - 移除對舊變數 `$is_charged` 的依賴
  - 修正完整度計算來源
- 列表未顯示新餐廳問題排除
  - `restaurant-member-functions.php` 的完整性過濾改用 `philly_corkage_fee`
  - 新餐廳（Ristorante Aroma、Bricco Coal Fired Pizza）可顯示
- 資料名單補強
  - 取 11/16 新增餐廳官網（Google Places Details）→ 與舊檔合併
  - 新增 Google Custom Search 查找 Yelp 連結，覆寫到同檔 `Yelp_URL`
  - 以地址查詢經緯度（TextSearch/Geocode），在 `Yelp_URL` 右側插入 `Latitude` / `Longitude`
- 新增小工具腳本
  - `philly_yelp_crawler/update_yelp_links.py`（針對 11/16，用 CSE 寫回 Yelp_URL）
  - `philly_yelp_crawler/update_latlng_1116.py`（針對 11/16，寫回 Lat/Lng）

### 🔧 主要修改檔案
- `wordpress/single_restaurant.php`（Corkage 採用 philly 欄位；修復語法錯誤）
- `wordpress/archive-restaurant.php`（Corkage 顯示/完整度來源修正）
- `wordpress/woocommerce/myaccount/restaurant-profile.php`（後台改用 `philly_corkage_fee` radio）
- `wordpress/restaurant-member-functions.php`（完整性過濾改用 philly 欄位，儲存流程更新）
- `doc/Next Task Prompt Byob.md`（新增 11/17 目標）
- `philly_yelp_crawler/update_yelp_links.py`、`philly_yelp_crawler/update_latlng_1116.py`

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

