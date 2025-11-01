# BYOB 專案開發進度記錄

## 📅 專案概覽

### **台北 BYOB 專案**（主要專案）
* **專案名稱**：BYOB 台北 - 自帶酒水餐廳推薦平台
* **目前階段**：核心系統完成，推廣與酒商合作階段
* **核心功能**：餐廳推薦表單、重複檢查、審核管理、抽獎系統、Email 通知、多平台推廣
* **技術架構**：WordPress + ACF + Google Apps Script + Python 爬蟲工具

### **費城 BYOB 專案**（新專案）
* **專案名稱**：Philadelphia BYOB Restaurant Guide
* **目前階段**：雙表單系統完成，準備實作多層次驗證策略
* **核心策略**：多平台資料收集 + 雙向表單驗證 + 自動化文章生成 + Reddit 社群互動
* **定位**：成為 Yelp 的 BYOB 專業補充平台

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

* [x] **表單提交後確認訊息設計** ⭐ 用戶體驗
  * 網友推薦版：強調貢獻價值，提醒檢查網站而非等待 email
  * 老闆確認版：強調精確驗證，提供聯絡管道並承諾 email 確認

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

## ✅ 2025年10月30日 — 餐廳列表頁顯示邏輯與統一性修復

### 已完成項目

* [x] **移除 district 必填限制** ⭐ 顯示邏輯優化
  * 問題：費城餐廳沒有 `district` 欄位，導致列表頁被隱藏
  * 修復：移除 `district` 的必填檢查，改為檢查 5 個必填欄位
  * 修改檔案：`wordpress/restaurant-member-functions.php`（2576-2591 行）

* [x] **修復 other 類型顯示邏輯** ⭐ 顯示一致性
  * 問題：列表頁只檢查中文「其他」，未檢查英文 'other'
  * 修復：增加英文 'other' 支援，顯示 `Other: [description]`
  * 修改檔案：`wordpress/archive-restaurant.php`（餐廳類型：298, 313, 316 行；設備：455, 468, 470 行）

* [x] **Notes 欄位命名統一** ⭐ 命名一致性
  * 修改：將 "Dining Experience" 改為 "Notes"
  * 修改檔案：`wordpress/functions.php`（1111 行）、`wordpress/Apps script - 費城推薦版.js`（377 行）

* [x] **表單 ↔ ACF 對齊修復** ⭐ 資料一致性
  * 單選題 label→key 安全映射：就地 if/elseif 處理
  * Other 選項與備註自動產生機制
  * 前台顯示一致性處理

### 技術成果

- 餐廳列表頁正確顯示費城餐廳
- other 類型中英文正確處理與顯示
- 表單到 ACF 的映射穩定可靠

---

## ✅ 2025年10月29日 — Google 表單新欄位整合與 ACF 系統優化

### 已完成項目

* [x] **Google 表單新增欄位處理** ⭐ 系統整合完成
  * 新增「Reddit 用戶名顯示偏好」選擇題
  * 完成端到端整合：表單 → Sheets → Apps Script → API → ACF

* [x] **ACF 欄位群組顯示問題修復** ⭐ 後台管理優化
  * 費城餐廳只顯示專用欄位群組
  * 透過位置規則精確控制

* [x] **ACF 欄位空值處理優化** ⭐ 資料品質提升
  * 選填選擇題正確處理空值
  * 避免自動選擇第一個選項

### 修改的檔案
- `wordpress/functions.php`：4處修改
- `wordpress/Apps script - 費城推薦版.js`：Email 更新

---

## ✅ 2025年10月22日 — WordPress 程式碼英文化完成

### 已完成項目

* [x] **WordPress 前台英文化完成** ⭐ 介面英文化
  * 餐廳列表頁、單一餐廳頁、資料編輯頁全英文化
  * 保留中文註解，用戶可見文字改為英文

* [x] **Apps Script 評估** ⭐ 技術決策
  * 確認不需要英文化
  * 保持中文註解和 Logger 訊息

### 技術成果
- 專業英文網站介面
- 符合美國用戶習慣
- 開發者友善維護

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

**當前狀態：**
* ✅ 兩套完整表單系統運作中
* ✅ 資料來源自動辨識與追蹤
* 🔄 準備實作多層次驗證策略（11/2）

**下一步重點：**
* 🚀 多層次驗證回覆率處理策略（見 Next Task Prompt）
* 🚀 Reddit 社群互動啟動
* ⏳ 手動內容英文化、網站上線、用戶招募、榮譽系統

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

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統、重複檢查系統

---

## 📁 核心文檔

### **費城專案文檔**
* `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
* `doc/Next Task Prompt Byob.md`：工作規劃與任務追蹤
* `philly_yelp_crawler/data/combined_byob_restaurants.csv`：269 家候選餐廳資料
* `philly_yelp_crawler/data/high_confidence_byob_restaurants.csv`：10 家高信心度餐廳
* `wordpress/Apps script - 費城推薦版.js`：網友推薦表單處理
* `wordpress/Apps script - 費城餐廳確認版.js`：老闆確認表單處理

### **台北專案文檔**
* `doc/ai_progress_byob.md`：開發進度記錄（本檔案）
* `doc/lottery_activity_planning.md`：抽獎活動規劃
* `doc/message_and_form/`：Email 通知模板

### **WordPress 核心檔案**
* `wordpress/functions.php`：REST API 端點和 ACF 欄位處理
* `wordpress/archive-restaurant.php`：餐廳列表頁模板
* `wordpress/single_restaurant.php`：單一餐廳頁模板
* `wordpress/restaurant-member-functions.php`：餐廳會員相關功能

---

## 💡 關鍵學習與洞察

### **從台北到費城的策略演進**

**台北模式：**
- 抽獎激勵、物質獎勵驅動、一次性參與

**費城模式升級：**
- 榮譽系統、專業認同驅動、長期持續參與
- 創始成員身份、更低成本、更可持續

**調整原因：**
1. 海外專案初期成本考量
2. 美國用戶重視專業認同和社群地位
3. 榮譽系統可擴展性高
4. 建立專家社群而非獎品獵人

### **技術架構學習**

**多平台爬蟲優勢：**
1. 資料完整性：不同平台提供互補資訊
2. 交叉驗證：多個來源提高可信度
3. 風險分散：單一平台問題不影響整體
4. 來源追蹤：清楚記錄每筆資料來源

**雙表單系統設計：**
1. 清晰的目的區分：社群推薦 vs 老闆驗證
2. 資料來源自動標記：後台管理便利
3. 獨立但共享：避免重複開發
4. 信任度分層：老闆驗證 > 社群推薦

**資料對齊與一致性：**
1. label→key 映射：避免顯示文字與值鍵不匹配
2. 空值策略：一致的空值處理邏輯
3. 跨語言支援：中英文欄位都正確處理
4. 前後台對齊：確保顯示與儲存一致

**API 參數硬編碼教訓：**
1. 避免硬編碼關鍵識別欄位
2. 從請求中動態讀取所有參數
3. 提供合理的預設值作為後備
4. 完整的參數定義和驗證

---

## 🚨 當前挑戰與風險

### **驗證回覆率過低風險** ⚠️
- **風險**：餐廳老闆回覆率可能僅 20-30%
- **影響**：資料品質失衡、信心度不足、用戶信任下降
- **緩解策略**：多層次處理系統（見 Next Task Prompt 詳述）

### **Reddit 社群接受度**
- **風險**：新帳號可能被視為推廣或 spam
- **緩解**：先建立信譽，提供有價值的建議
- **備案**：準備多個社群平台互動

### **資料品質控制**
- **風險**：回覆可能包含錯誤資訊
- **緩解**：交叉驗證、記錄來源
- **備案**：保留原始爬蟲資料作為備份

---

*最後更新：2025年11月1日*
*版本：v17.0*
*明日重點：多層次驗證回覆率處理策略實作*
