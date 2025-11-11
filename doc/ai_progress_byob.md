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

## 📚 近期工作摘要（精簡版）

### ✅ 2025年11月6日 — 前台英文化與評論功能移除
- 餐廳列表、單頁、註冊表單、Contact Form 全面改為英文；維護註解保留中文。
- 移除未完成的評論與評分相關檔案，保持程式碼庫整潔。

### ✅ 2025年11月5日 — 重複檢查與資料追蹤優化
- `byob_check_duplicate_restaurant()` 新增專案參數、日誌與地址縮寫處理。
- 建立 `recommendation_count` 欄位、標題加註 `(重複)`、相似度權重調整。

### ✅ 2025年11月4日 — Email 搜尋系統
- 兩階段腳本 `philly_email_searcher.py` + `philly_email_extractor.py` 完成，支援重試、日誌、Excel 匯出。

### ✅ 2025年11月3日 — 驗證徽章與 Yelp 整合
- 前台徽章視覺化、後台覆寫邏輯與資料來源標記。
- 表單、Apps Script、後端 API 全程改為使用 Yelp Link。

---


## ✅ 2025年11月5日 — 重複餐廳處理機制實作完成

### 🎯 今日目標
建立完整的重複餐廳檢測與處理機制，優化相似度計算邏輯，確保資料準確性和系統穩定性。

### 已完成項目

* [x] **重複餐廳標題加註功能** ⭐⭐⭐ 核心功能
  * 當系統檢測到重複餐廳時，自動在標題後方加註「(重複)」
  * 例如：「ABC餐廳」→「ABC餐廳(重複)」
  * 即使多次重複也只加一次（避免重複加註）
  * 修改檔案：`wordpress/functions.php`（`byob_create_philly_restaurant_article()` 函數）

* [x] **推薦次數欄位新增** ⭐⭐ 數據追蹤
  * 新增 `recommendation_count` ACF 欄位，記錄餐廳被推薦的次數
  * 新建餐廳時預設為 1
  * 可在 WordPress 後台手動修改
  * 顯示在「Verification Override」欄位群組中
  * 修改檔案：`wordpress/functions.php`（ACF 欄位定義）

* [x] **重複檢查邏輯優化** ⭐⭐⭐ 系統優化
  * 修改 `byob_check_duplicate_restaurant()` 函數，新增 `$project` 參數
  * 費城專案只檢查費城餐廳（透過 `source` 欄位篩選）
  * 避免與台北專案餐廳誤判為重複
  * 支援專案類型篩選：`'philly'`、`'taipei'`、`''`（所有專案）
  * 修改檔案：`wordpress/functions.php`（重複檢查函數）

* [x] **相似度計算優化** ⭐⭐⭐ 核心演算法改進
  * **地址為空時的處理**：
    - 如果兩個地址都為空且名稱相同 → 90% 相似度（判定為重複）
    - 如果兩個地址都為空 → 只依賴名稱相似度
    - 如果只有一個地址為空 → 名稱權重 80%，地址權重 20%
  * **英文標點符號處理**：
    - 新增處理逗號、句號、破折號、連字符等英文標點
    - 統一移除所有標點符號和空格
  * **地址縮寫統一**：
    - Street → St、Avenue → Ave、Boulevard → Blvd
    - Road → Rd、Drive → Dr、Lane → Ln
    - Philadelphia → Philly、Pennsylvania → PA
    - 使用字邊界（`\b`）確保只匹配完整單字
  * 修改檔案：`wordpress/functions.php`（`byob_calculate_simple_similarity()` 函數）

* [x] **除錯日誌新增** ⭐ 開發工具
  * 記錄重複檢查查詢結果
  * 記錄每個比對的餐廳和相似度
  * 方便後續問題診斷
  * 修改檔案：`wordpress/functions.php`（重複檢查函數）

### 技術成果

**重複檢測系統架構：**
- 專案類型篩選機制，避免跨專案誤判
- 智能相似度計算，處理各種邊緣情況
- 標題自動標記，方便管理員識別重複餐廳

**相似度計算優化：**
- 地址為空時的智能處理邏輯
- 英文標點符號和地址縮寫統一化
- 靈活的權重分配機制

**數據追蹤機制：**
- 推薦次數欄位，追蹤餐廳受歡迎程度
- 除錯日誌，方便問題診斷和優化

### 技術細節

**重複檢查邏輯：**
- 使用 `source` 欄位篩選費城餐廳，確保只檢查同專案餐廳
- 支援向後相容，不傳入專案參數時檢查所有餐廳
- 查詢條件優化，確保查詢效率

**相似度計算邏輯：**
- 標準化處理：統一大小寫、移除標點、統一縮寫
- 特殊情況處理：地址為空、名稱相似但地址不同等
- 權重分配：根據資料完整性動態調整權重

**標題加註機制：**
- 檢查標題是否已包含「(重複)」，避免重複加註
- 在建立文章前處理標題，確保資料一致性

### 測試狀態

**待測試項目：**
- 重複餐廳標題加註功能
- 推薦次數欄位顯示
- 地址為空時的相似度計算
- 英文標點符號和地址縮寫處理

### 修改的檔案

**程式碼檔案：**
- `wordpress/functions.php`：
  - 重複檢查函數（`byob_check_duplicate_restaurant()`）
  - 相似度計算函數（`byob_calculate_simple_similarity()`）
  - 餐廳建立函數（`byob_create_philly_restaurant_article()`）
  - ACF 欄位定義（推薦次數欄位）

---

## ✅ 2025年11月4日 — 餐廳 Email 搜尋系統建立完成

### 🎯 今日目標
建立兩階段自動化 Email 搜尋系統，從 Google Places API 取得餐廳 website，再從 website 搜尋 email 地址。

### 已完成項目

* [x] **兩階段 Email 搜尋系統建立** ⭐⭐⭐ 完整自動化工具
  * **步驟 1：Website 搜尋工具** (`philly_email_searcher.py`)
    - 輸入：Excel 檔案（Name, Add, Phone 欄位）
    - 處理：使用「餐廳名稱 + Philadelphia」搜尋 Google Places API
    - 輸出：Excel 檔案（新增 Google_Website, Google_Place_ID, Google_Place_Name, Google_Address, Search_Status）
    - 功能：自動重試機制、API 限制處理、進度顯示、完整日誌記錄
  * **步驟 2：Email 提取工具** (`philly_email_extractor.py`)
    - 輸入：步驟 1 的輸出 Excel（包含 Google_Website 欄位）
    - 處理：搜尋多個常見頁面（首頁、聯絡頁面等），提取 email
    - 輸出：Excel 檔案（新增 Email, Email_Status, Email_Message, Email_All_Found）
    - 功能：智能 email 選擇、多個 email 自動展開為多行、email 驗證與過濾

* [x] **檔案清理與組織** ⭐ 專案結構優化
  * 刪除不需要的檔案（Yelp、TripAdvisor 爬蟲等）
  * 保留 Google Places 相關檔案
  * 建立清晰的資料夾結構

* [x] **測試與驗證** ⭐ 系統驗證
  * **檔案 1**：`Philly BYOB Restaurant.xlsx`（42 家餐廳）
    - 步驟 1：成功取得 website 40 家（95.2%）
    - 步驟 2：測試 5 家，成功取得 email 4 家（80%）
  * **檔案 2**：`Philly BYOB Restaurant google form.xlsx`（20 家餐廳）
    - 步驟 1：成功取得 website 18 家（90%）
    - 步驟 2：成功取得 email 9 家（45%），展開後共 35 行（包含多個 email 的餐廳）

### 技術成果

**兩階段設計優勢：**
- 分離取得 website 和搜尋 email，提高靈活性和可維護性
- 可獨立執行和測試每個步驟
- 支援批次處理大量餐廳資料

**Email 搜尋功能：**
- 智能 email 選擇：優先選擇包含餐廳名稱或常見前綴的 email
- 多個 email 自動展開：當找到多個 email 時，自動展開為多行，每行對應一個 email
- Email 驗證與過濾：過濾無效 email（範例 email、系統 email 等）

**API 使用優化：**
- Google Places API：每次請求 0.2-0.5 秒延遲，自動重試機制
- Website 爬蟲：每次請求 1-2 秒延遲，避免被封鎖
- 完整記錄 API 請求次數（目前使用 130 次，遠低於免費額度 100,000 次）

### 測試結果統計

- **處理檔案數**：2 個
- **總餐廳數**：62 家（42 + 20）
- **步驟 1（取得 Website）**：成功 58/62（93.5%）
- **步驟 2（搜尋 Email）**：成功 13 家
- **API 使用量**：130 次請求（0.13% 使用率）

### 修改的檔案

**新建檔案：**
- `philly_yelp_crawler/philly_email_searcher.py`：Website 搜尋工具
- `philly_yelp_crawler/philly_email_extractor.py`：Email 提取工具
- `philly_yelp_crawler/README.md`：更新使用說明

**保留檔案：**
- `philly_yelp_crawler/google_config.py`：API 設定

---

## ✅ 2025年11月3日 — 驗證徽章系統與 Yelp 連結整合完成

### 🎯 今日目標
建立完整的驗證徽章顯示系統，整合 Yelp 連結欄位，優化餐廳業者後台，統一前端顯示格式。

### 已完成項目

* [x] **驗證徽章系統實作** ⭐⭐ 前台顯示與後台管理
  * 前台顯示系統：
    - 餐廳列表頁和單一餐廳頁新增驗證徽章
    - 徽章顯示在餐廳名稱上方一行
    - 兩種狀態設計：
      - `Verified by Restaurant`：藍色背景，🔒 圖示，表示餐廳老闆驗證
      - `Community Recommended`：橙色背景，👥 圖示，表示社群推薦
  * 後台管理機制：
    - 新增 `verification_override` ACF 欄位（管理員可手動覆蓋驗證狀態）
    - 優先順序：`verification_override` > `source` 欄位
    - 修改檔案：`wordpress/functions.php`（新增 ACF 欄位定義和 `byob_display_verification_badge()` 函數）
  * 前端顯示檔案：
    - `wordpress/archive-restaurant.php`：列表頁徽章顯示（small 尺寸）
    - `wordpress/single_restaurant.php`：單一餐廳頁徽章顯示（medium 尺寸）

* [x] **Yelp 連結欄位整合** ⭐⭐ 端到端整合
  * Google 表單更新：
    - 將「Website or Reservation Link」改為「Yelp Link」
    - 更新欄位映射邏輯
  * Apps Script 修改：
    - `wordpress/Apps script - 費城推薦版.js`：更新通知郵件顯示 Yelp Link
    - `wordpress/Apps script - 費城餐廳確認版.js`：更新通知郵件顯示 Yelp Link
  * WordPress 後端：
    - `wordpress/functions.php`：修改 API 端點處理 `yelp_link` 參數
    - 更新 `byob_create_philly_restaurant_post` 和 `byob_create_philly_restaurant_article` 函數
    - 將原本的 `website` 相關程式碼註解保留
  * 前台顯示：
    - 餐廳列表頁：Yelp 欄位已註解（不顯示）
    - 單一餐廳頁：顯示 Yelp 連結
    - 原本的 Website/Social Links 相關程式碼已註解保留

* [x] **餐廳業者後台優化** ⭐ 用戶體驗提升
  * Yelp 連結欄位：
    - 在餐廳業者編輯頁面加入 Yelp Link 欄位
    - 欄位位置：在「Yelp Link / Official Website/Social Media Links」區塊內的第一個位置
    - 修改檔案：`wordpress/woocommerce/myaccount/restaurant-profile.php`
  * 表單提交處理：
    - `wordpress/restaurant-member-functions.php`：新增 `yelp_link` 的保存邏輯

* [x] **前端格式統一** ⭐ 顯示一致性
  * 欄位冒號後空格統一：
    - 檢查並修正餐廳列表頁和單一餐廳頁所有欄位的冒號後空格
    - 統一格式：所有欄位標籤冒號後都加上空格
  * 修改檔案：
    - `wordpress/archive-restaurant.php`：修正 Corkage Fee, Corkage Details, Wine Equipment, Notes, Address, Phone
    - `wordpress/single_restaurant.php`：修正 Cuisine Type, Corkage Fee, Corkage Details, Wine Equipment, Wine Service, Yelp, Notes, Address, Phone

### 技術成果

**驗證系統架構：**
- 視覺化驗證狀態展示，提升用戶信任度
- 管理員可手動覆蓋驗證狀態，保持管理彈性
- 基於 `source` 欄位的自動驗證狀態判斷

**Yelp 整合策略：**
- 聚焦單一外部平台連結（Yelp），簡化用戶選擇
- 保留原有 Website/Social Links 程式碼供未來使用
- 完整的端到端整合：表單 → 後端 → 前台

**格式統一成果：**
- 所有欄位顯示格式一致，提升專業度
- 改善用戶閱讀體驗

### 修改的檔案

**程式碼檔案：**
- `wordpress/functions.php`：驗證徽章系統、Yelp 欄位處理
- `wordpress/archive-restaurant.php`：徽章顯示、格式統一
- `wordpress/single_restaurant.php`：徽章顯示、格式統一
- `wordpress/woocommerce/myaccount/restaurant-profile.php`：Yelp 欄位加入
- `wordpress/restaurant-member-functions.php`：Yelp 保存邏輯
- `wordpress/Apps script - 費城推薦版.js`：Yelp 通知更新
- `wordpress/Apps script - 費城餐廳確認版.js`：Yelp 通知更新

---

## ✅ 2025年11月1日 — 費城餐廳確認表單系統建立完成

### 🎯 今日目標
建立專為費城餐廳老闆設計的資料確認表單，並完成完整的技術整合與資料來源辨識機制。

### 已完成項目

* [x] **費城餐廳確認表單完整建置** ⭐⭐ 雙表單系統完成
  * 建立新 Google 表單「Philly BYOB Restaurant (for owners)」
  * 與網友推薦表單的差異：
    - 移除「Show Reddit Username」問題
    - 「Reddit Username」→「Contact name」（聯絡人姓名）
    - 新增 `source = 'philly_owner_verification'` 來源標記
  * Spreadsheet ID：`11kIfdMNJ-6Pa-331AUViYpotnMRfM2sNoAuW7U0lLkA`

* [x] **建立專屬 Apps Script** ⭐ 獨立處理流程
  * 新建 `Apps script - 費城餐廳確認版.js`
  * 函式命名獨立化：`onPhillyOwnerFormSubmit`, `parsePhillyOwnerFormData`, `sendPhillyOwnerNotificationEmail`
  * Email 通知主旨：「Owner Verification」vs「Recommendation」
  * 資料來源標記：`philly_owner_verification` vs `philly_community_recommendation`

* [x] **修復 WordPress API source 欄位硬編碼問題** ⭐ 關鍵修復
  * 問題：`functions.php` 中 `source` 被硬編碼為 `'philly_community_recommendation'`，導致無法區分資料來源
  * 修復步驟：
    - 新增 `source` 參數到 API 端點定義（190-193 行）
    - 從 `$request->get_param('source')` 讀取（761 行）
    - 移除兩處硬編碼，改用動態值（784 行、980 行）
  * 影響：後台可正確辨識網友推薦或老闆驗證的資料來源

* [x] **ACF source 欄位處理決策** ⭐ 技術決策
  * 決定：不強制新增 ACF 欄位（選配）
  * 理由：程式碼已自動寫入 source，後台可手動新增作為視覺辨識
  * 狀態：系統功能完整，保持彈性

### 技術成果

**雙表單系統架構：**
- 推薦表單：社群驅動，Reddit 用戶回饋為主
- 確認表單：老闆驗證，直接授權資料
- 資料來源自動區分與追蹤
- 獨立但共享 API 端點和處理邏輯

**系統辨識機制：**
- Email 通知區分來源和格式
- 後台標籤顯示資料可信度
- 後續追蹤系統的基礎架構

### 修改的檔案

**程式碼檔案：**
- `wordpress/Apps script - 費城餐廳確認版.js`：新建完整處理邏輯
- `wordpress/functions.php`：4處修改（API 參數、動態 source）
- Google Sheets 欄位映射表：新建並測試驗證

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

**當前狀態：**
* ✅ 兩套完整表單系統運作中
* ✅ 資料來源自動辨識與追蹤
* ✅ 驗證狀態視覺化展示
* ✅ 餐廳 Email 搜尋系統完成（11/4）
* ✅ 重複餐廳處理機制完成（11/5）
* ✅ 網站前台英文化完成（11/6）
* ✅ 評論功能移除完成（11/6）
* 🔄 準備 FAQ 英文化、後台英文化、Reddit 回覆工作（11/7）

**下一步重點：**
* 🚀 FAQ 頁面英文化（11/7）
* 🚀 WordPress 後台英文化（11/7）
* 🚀 Reddit 回覆工作（11/7）
* ⏳ 寄給餐廳的 Email 模板設計、多名網友資料衝突處理邏輯、Reddit 社群互動啟動、網站上線、用戶招募、榮譽系統

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

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統、重複檢查系統

---

