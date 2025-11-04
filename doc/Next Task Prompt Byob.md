# 🍷 BYOB 專案工作規劃與進度追蹤

## 📅 當前日期：2025年11月4日

---

## ✅ 今日（2025-11-04）完成工作總結

### 1) 餐廳 Email 搜尋系統建立完成 ⭐⭐⭐
- **兩階段 Email 搜尋系統**：
  - **步驟 1**：`philly_email_searcher.py` - 使用 Google Places API 搜尋餐廳並取得 website
    - 輸入：Excel 檔案（Name, Add, Phone 欄位）
    - 處理：使用「餐廳名稱 + Philadelphia」搜尋 Google Places
    - 輸出：Excel 檔案（新增 Google_Website, Google_Place_ID, Google_Place_Name, Google_Address, Search_Status 欄位）
    - 功能：自動重試機制、API 限制處理、進度顯示、完整日誌記錄
  - **步驟 2**：`philly_email_extractor.py` - 從 website 搜尋 email 地址
    - 輸入：步驟 1 的輸出 Excel（包含 Google_Website 欄位）
    - 處理：搜尋多個常見頁面（首頁、聯絡頁面等），提取 email
    - 輸出：Excel 檔案（新增 Email, Email_Status, Email_Message, Email_All_Found 欄位）
    - 功能：智能 email 選擇、多個 email 自動展開為多行、email 驗證與過濾
- **檔案清理**：
  - 刪除不需要的檔案（Yelp、TripAdvisor 爬蟲等）
  - 保留 Google Places 相關檔案
  - 建立清晰的資料夾結構
- **測試結果**：
  - **檔案 1**：`Philly BYOB Restaurant.xlsx`（42 家餐廳）
    - 步驟 1：成功取得 website 40 家（95.2%）
    - 步驟 2：成功取得 email 4 家（測試 5 家，80% 成功率）
  - **檔案 2**：`Philly BYOB Restaurant google form.xlsx`（20 家餐廳）
    - 步驟 1：成功取得 website 18 家（90%）
    - 步驟 2：成功取得 email 9 家（45%），展開後共 35 行（包含多個 email 的餐廳）
- **修改檔案**：
  - `philly_yelp_crawler/philly_email_searcher.py`：新建
  - `philly_yelp_crawler/philly_email_extractor.py`：新建
  - `philly_yelp_crawler/README.md`：更新使用說明
  - `philly_yelp_crawler/google_config.py`：保留 API 設定

---

## ✅ 昨日（2025-11-03）完成工作總結

### 1) 驗證徽章系統實作 ⭐⭐
- **前台顯示系統**：
  - 餐廳列表頁和單一餐廳頁新增驗證徽章顯示
  - 徽章顯示在餐廳名稱上方一行
  - 兩種狀態：`Verified by Restaurant`（藍色，🔒圖示）、`Community Recommended`（橙色，👥圖示）
- **後台管理機制**：
  - 新增 `verification_override` ACF 欄位，允許管理員手動覆蓋驗證狀態
  - 優先順序：`verification_override` > `source` 欄位
  - 修改檔案：`wordpress/functions.php`（新增 ACF 欄位定義和徽章顯示函數）
- **前端顯示檔案**：
  - `wordpress/archive-restaurant.php`：列表頁徽章顯示
  - `wordpress/single_restaurant.php`：單一餐廳頁徽章顯示

### 2) Yelp 連結欄位整合 ⭐⭐
- **Google 表單更新**：
  - 將「Website or Reservation Link」改為「Yelp Link」
  - 更新欄位映射邏輯
- **Apps Script 修改**：
  - `wordpress/Apps script - 費城推薦版.js`：更新通知郵件顯示 Yelp Link
  - `wordpress/Apps script - 費城餐廳確認版.js`：更新通知郵件顯示 Yelp Link
- **WordPress 後端**：
  - `wordpress/functions.php`：修改 API 端點處理 `yelp_link` 參數
  - 更新 `byob_create_philly_restaurant_post` 和 `byob_create_philly_restaurant_article` 函數
- **前台顯示**：
  - 餐廳列表頁：Yelp 欄位已註解（不顯示）
  - 單一餐廳頁：顯示 Yelp 連結
  - 原本的 Website/Social Links 相關程式碼已註解保留

### 3) 餐廳業者後台優化 ⭐
- **Yelp 連結欄位**：
  - 在餐廳業者編輯頁面加入 Yelp Link 欄位
  - 欄位位置：在「Yelp Link / Official Website/Social Media Links」區塊內的第一個位置
  - 修改檔案：`wordpress/woocommerce/myaccount/restaurant-profile.php`
- **表單提交處理**：
  - `wordpress/restaurant-member-functions.php`：新增 `yelp_link` 的保存邏輯

### 4) 前端格式統一 ⭐
- **欄位冒號後空格統一**：
  - 檢查並修正餐廳列表頁和單一餐廳頁所有欄位的冒號後空格
  - 統一格式：所有欄位標籤冒號後都加上空格
  - 修改檔案：
    - `wordpress/archive-restaurant.php`（Corkage Fee, Corkage Details, Wine Equipment, Notes, Address, Phone）
    - `wordpress/single_restaurant.php`（Cuisine Type, Corkage Fee, Corkage Details, Wine Equipment, Wine Service, Yelp, Notes, Address, Phone）

---

## 🗓️ 明日（2025-11-05）工作規劃

### 🎯 核心任務：餐廳聯絡與資料驗證機制

### 1) 寄給餐廳的 Email 模板 ⭐⭐
- **目標**：設計專業、友善的 email 模板，用於聯絡餐廳老闆驗證資料
- **實作內容**：
  - **Email 主題設計**：吸引人且專業的標題
    - 範例：「[Philadelphia BYOB] Verify Your Restaurant Information」或「Your Restaurant is Featured on Philadelphia BYOB Guide」
  - **Email 內容結構**：
    - **開頭**：自我介紹與平台說明
      - 簡述平台定位（成為 Yelp 的 BYOB 專業補充）
      - 說明平台價值（幫助餐廳接觸 BYOB 愛好者）
    - **主體**：餐廳資料概述
      - 顯示當前平台上顯示的餐廳資訊
      - 列出關鍵欄位（開瓶費、設備、服務等級等）
      - 提供餐廳頁面連結
    - **請求**：驗證請求
      - 請老闆確認資料是否正確
      - 如有錯誤，請提供正確資訊
      - 提供簡便的回覆方式（回覆 email 或使用確認表單連結）
    - **價值說明**：社群價值
      - 強調對餐廳的價值（增加曝光、吸引目標客群）
      - 強調對消費者的價值（提供準確資訊）
      - 說明驗證徽章的好處
    - **結尾**：聯絡方式與回覆管道
      - 提供回覆 email
      - 提供確認表單連結
      - 提供平台網站連結
  - **多語言支援**：英文版本（費城專案主要使用英文）
  - **個性化內容**：
    - 根據餐廳類型調整內容
    - 根據資料完整性調整（完整資料 vs 部分資料）
    - 根據資料來源調整（社群推薦 vs 已驗證）
  - **模板變體**：
    - 初次聯絡版本（完整介紹）
    - 提醒版本（簡短提醒）
    - 感謝版本（已驗證後的感謝信）
- **技術實作**：
  - 建立 email 模板檔案（Markdown 或文字格式）
  - 考慮整合到 WordPress（使用 WordPress email 功能）
  - 或整合到 Apps Script（批次發送）
  - 支援變數替換（餐廳名稱、資料等）
- **預期成果**：
  - 完成 email 模板檔案（至少 1-2 個版本）
  - 建立發送機制或腳本（可選）
  - 測試發送（可選）

### 2) 建立多名網友對某家餐廳資訊不一的處理邏輯 ⭐⭐⭐
- **問題背景**：
  - 不同網友可能對同一家餐廳提供不同資訊（如開瓶費、設備、服務等）
  - 需要建立衝突解決機制，確保資料準確性
- **實作內容**：
  - **衝突檢測機制**：
    - 識別同一餐廳的多筆推薦資料
    - 比較關鍵欄位（開瓶費、設備、服務等級等）
    - 標記衝突或差異的欄位
  - **衝突解決策略**：
    - **優先順序設計**：
      - 餐廳老闆直接驗證 > 多數網友一致意見 > 單一網友意見
      - 較新的資料 > 較舊的資料（但需考慮可信度）
      - 有更多詳細資訊的資料 > 資訊較少的資料
    - **自動合併邏輯**：
      - 非衝突欄位自動合併
      - 衝突欄位保留所有版本，標記待人工審核
    - **人工審核流程**：
      - 後台顯示衝突餐廳列表
      - 管理員可選擇採用哪一版本或手動編輯
  - **資料版本管理**：
    - 記錄每筆資料的提交時間、來源、提交者資訊
    - 保留歷史版本以供參考
    - 建立變更記錄
- **技術實作需求**：
  - 新增 ACF 欄位或用自訂資料表記錄多筆提交
  - 建立衝突檢測函數
  - 建立後台衝突管理介面
  - 建立自動合併與標記邏輯
- **預期成果**：
  - 完成衝突檢測與處理機制
  - 建立後台管理介面
  - 完成資料版本管理系統

---

## 📊 專案進度概覽

### 🍷 費城 BYOB 專案（進行中）
- ✅ **資料收集完成**：269 家候選餐廳（Yelp + Google Places）
- ✅ **Reddit 帳號建立**：u/findingBYOB 準備就緒
- ✅ **互動追蹤系統**：完整的管理工具建立
- ✅ **Google 表單系統完成**：推薦表單 + 老闆確認表單
- ✅ **Apps Script 整合完成**：兩套獨立處理流程
- ✅ **自動化整合完成**：Google Apps Script + WordPress API 整合
- ✅ **WordPress 程式碼英文化完成**：所有 PHP 檔案前台顯示文字已改為英文
- ✅ **驗證徽章系統完成**：前台顯示與後台管理機制
- ✅ **Yelp 連結欄位整合完成**：表單、後端、前台顯示
- ✅ **餐廳業者後台優化完成**：Yelp 欄位加入
- ✅ **前端格式統一完成**：欄位冒號後空格統一
- ✅ **餐廳 Email 搜尋系統完成**：兩階段系統（取得 website + 搜尋 email）
  - 步驟 1：Google Places API 搜尋（philly_email_searcher.py）
  - 步驟 2：Website email 提取（philly_email_extractor.py）
  - 測試結果：處理 62 家餐廳（42 + 20），成功取得 email 13 家
- 🔄 **餐廳 Email 模板設計**：11/5 開始執行
- 🔄 **資料衝突處理機制**：11/5 開始執行
- ⏳ **待執行**：Reddit 社群互動階段、手動內容英文化、英文網站上線、用戶招募、榮譽系統實作

### 🍷 台北 BYOB 專案（既有專案）
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

---

## 🧭 重要技術學習與踩雷紀錄

### 1) 單選題加上 placeholder 後 ACF 回退問題
- **根因**：WP 端把表單「顯示文字」直接寫入 ACF；ACF 期望的是「值鍵」（key）
- **解法**：在 WP `functions.php` 寫入安全映射（就地 if/elseif），將顯示文字 → 值鍵
  - `philly_corkage_fee`：Free → `free`、Corkage Fee → `corkage_fee`、Other → `other`、未選擇 → ''
  - `byob_service_level`：四個長句對應 `full_service`/`basic_service`/`self_service`/`no_service`、未選擇 → ''
  - `show_reddit_username`：以 Yes/No 前綴判斷，並規一撇號與空白；Yes → `yes`、No → `no`、未選擇 → ''

### 2) 餐廳類型/酒器設備的 other 與備註
- **根因**：Apps Script/ACF 欄位鍵不一致，以及將中文「其他」存入導致條件顯示不觸發
- **解法**：
  - ACF 勾選鍵一律使用英文 `'other'`
  - 若有說明文字，確保陣列包含 `'other'`，並寫入對應 other_note
  - 前台顯示：把字串中的 `'other'` 替換為 `Other: [note]`（`archive-restaurant.php` 和 `single_restaurant.php` 已處理）

### 3) API 參數硬編碼問題
- **現象**：`source` 欄位在 `functions.php` 被硬編碼為 `'philly_community_recommendation'`
- **根因**：未從 API 請求中讀取 `source` 參數
- **解法**：
  - 在 API 端點定義中新增 `source` 參數（190-193 行）
  - 從 `$request->get_param('source')` 讀取（761 行）
  - 移除所有硬編碼，改用動態值（784 行、980 行）
- **影響**：兩個表單現在可正確區分資料來源

### 4) ACF URL 欄位自動添加 http:// 前綴
- **現象**：當在 Google 表單輸入不含 `https://` 的 Yelp URL 時，ACF 後台自動添加 `http://` 前綴
- **根因**：ACF URL 欄位類型的內建行為，會自動驗證並添加協議前綴
- **注意事項**：ACF 預設添加 `http://` 而非 `https://`，可能導致連結錯誤
- **建議處理**：在 Google 表單說明中提醒用戶輸入完整 URL（包含 `https://`），或考慮未來加入 URL 處理過濾器

### 5) Email 搜尋系統設計與實作
- **兩階段設計**：
  - 分離取得 website 和搜尋 email 兩個步驟，提高靈活性和可維護性
  - 步驟 1 可以使用 Google Places API，步驟 2 可以使用網站爬蟲
- **多個 email 展開邏輯**：
  - 當 Email_All_Found 包含多個 email（用分號分隔）時，自動展開為多行
  - 每行對應一個 email，Email 和 Email_All_Found 欄位都更新為單一 email
- **Email 驗證與過濾**：
  - 過濾無效 email（範例 email、系統 email 等）
  - 優先選擇最相關的 email（包含餐廳名稱或常見前綴）
- **API 限制處理**：
  - Google Places API：每次請求 0.2-0.5 秒延遲，自動重試機制
  - Website 爬蟲：每次請求 1-2 秒延遲，避免被封鎖
- **統計資訊**：
  - 完整記錄 API 請求次數（目前使用 130 次，遠低於免費額度 100,000 次）

---

## 🔍 參考文檔

### **費城專案文檔**
* `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
* `philly_yelp_crawler/data/combined_byob_restaurants.csv`：269 家候選餐廳資料
* `philly_yelp_crawler/data/high_confidence_byob_restaurants.csv`：10 家高信心度餐廳
* `philly_yelp_crawler/data/crawl_report.json`：詳細爬取統計報告
* `philly_yelp_crawler/philly_email_searcher.py`：Email 搜尋步驟 1（取得 website）
* `philly_yelp_crawler/philly_email_extractor.py`：Email 搜尋步驟 2（搜尋 email）
* `philly_yelp_crawler/README.md`：Email 搜尋工具使用說明
* `wordpress/Apps script - 費城推薦版.js`：網友推薦表單處理
* `wordpress/Apps script - 費城餐廳確認版.js`：老闆確認表單處理

### **台北專案文檔**
* `doc/ai_progress_byob.md`：台北專案開發進度記錄
* `doc/lottery_activity_planning.md`：抽獎活動規劃
* `doc/message_and_form/`：Email 通知模板

---

## 🚨 當前挑戰與風險

### **餐廳聯絡與驗證** ⚠️（明日重點）
- **風險**：餐廳 email 難以取得、餐廳老闆回覆率可能較低
- **影響**：資料驗證困難、大量資料停滯待驗證
- **緩解策略**：建立友善的 email 模板、設計多階段提醒機制、建立社群驗證備案

### **資料衝突處理** ⚠️（明日重點）
- **風險**：多名網友提供不同資訊，難以判斷正確性
- **影響**：資料準確性下降、用戶信任度降低
- **緩解策略**：建立衝突檢測與解決機制、優先採用餐廳老闆驗證、保留多版本供人工審核

### **Reddit 社群接受度**
- **風險**：新帳號可能被視為推廣或 spam
- **緩解**：先建立信譽，提供有價值的建議
- **備案**：準備多個社群平台互動

### **資料品質控制**
- **風險**：Reddit 回覆可能包含錯誤資訊
- **緩解**：交叉驗證多個回覆，記錄資訊來源
- **備案**：保留原始爬蟲資料作為備份

---

## 📝 技術工具與資源

### **費城專案工具（已完成）**
- **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
- **智能去重系統**：保留來源資訊的資料整合
- **信心度評估**：High/Medium/Low 三級評分系統
- **Reddit 互動追蹤系統**：完整的管理和分析工具
- **Google 表單系統**：推薦表單 + 老闆確認表單（兩套完整流程）
- **Apps Script 自動化**：推薦版 + 確認版獨立處理
- **WordPress API 整合**：自動生成文章草稿、動態 source 處理
- **自動化文章生成系統**：英文內容模板和 SEO 優化
- **雙重通知系統**：成功和錯誤通知機制
- **驗證徽章系統**：前台顯示與後台管理
- **Yelp 連結整合**：表單、後端、前台完整整合
- **餐廳 Email 搜尋系統**：兩階段自動化搜尋工具
  - Google Places API 搜尋（取得 website）
  - Website email 提取（搜尋 email）
  - 支援多個 email 自動展開

### **台北專案工具（已完成）**
- **葡萄酒展參展商爬蟲**：酒商名單收集
- **Email 提取器**：聯絡資訊收集
- **抽獎系統**：推薦者激勵機制
- **重複檢查系統**：自動檢測重複餐廳

---

## 📈 今日（2025-11-04）成果統計

### Email 搜尋系統測試結果
- **處理檔案數**：2 個
- **總餐廳數**：62 家（42 + 20）
- **步驟 1（取得 Website）**：
  - 檔案 1：成功 40/42（95.2%）
  - 檔案 2：成功 18/20（90%）
  - 合計：成功 58/62（93.5%）
- **步驟 2（搜尋 Email）**：
  - 檔案 1：測試 5 家，成功 4 家（80%）
  - 檔案 2：處理 20 家，成功 9 家（45%）
  - 合計：成功 13 家
- **API 使用量**：
  - Google Places API：130 次請求
  - 免費額度：100,000 次/月
  - 使用率：0.13%（遠低於免費額度）

### 產出檔案
- `philly_yelp_crawler/philly_email_searcher.py`：Website 搜尋工具
- `philly_yelp_crawler/philly_email_extractor.py`：Email 提取工具
- `philly_yelp_crawler/README.md`：使用說明文件
- `philly_yelp_crawler/data/Philly BYOB Restaurant_with_websites_xxx.xlsx`：Website 搜尋結果
- `philly_yelp_crawler/data/Philly BYOB Restaurant_with_websites_xxx_with_emails_xxx.xlsx`：最終 email 搜尋結果

---

*最後更新：2025年11月4日*
*版本：v18.0*
*明日重點：Email 模板設計與資料衝突處理機制實作*
