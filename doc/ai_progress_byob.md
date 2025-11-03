# BYOB 專案開發進度記錄

## 📅 專案概覽

### **台北 BYOB 專案**（主要專案）
* **專案名稱**：BYOB 台北 - 自帶酒水餐廳推薦平台
* **目前階段**：核心系統完成，推廣與酒商合作階段
* **核心功能**：餐廳推薦表單、重複檢查、審核管理、抽獎系統、Email 通知、多平台推廣
* **技術架構**：WordPress + ACF + Google Apps Script + Python 爬蟲工具

### **費城 BYOB 專案**（新專案）
* **專案名稱**：Philadelphia BYOB Restaurant Guide
* **目前階段**：驗證徽章系統完成，準備實作餐廳聯絡機制
* **核心策略**：多平台資料收集 + 雙向表單驗證 + 自動化文章生成 + Reddit 社群互動
* **定位**：成為 Yelp 的 BYOB 專業補充平台

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

**當前狀態：**
* ✅ 兩套完整表單系統運作中
* ✅ 資料來源自動辨識與追蹤
* ✅ 驗證狀態視覺化展示
* 🔄 準備實作餐廳聯絡機制（11/4）

**下一步重點：**
* 🚀 餐廳 Email 搜尋系統
* 🚀 寄給餐廳的 Email 模板設計
* 🚀 多名網友資料衝突處理邏輯
* ⏳ Reddit 社群互動啟動、手動內容英文化、網站上線、用戶招募、榮譽系統

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
* `wordpress/woocommerce/myaccount/restaurant-profile.php`：餐廳業者後台編輯頁面

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

**驗證徽章系統設計：**
1. 視覺化狀態展示：提升用戶信任度
2. 管理員覆蓋機制：保持管理彈性
3. 優先順序邏輯：覆蓋欄位 > 來源欄位
4. 一致的顯示格式：列表頁與單一頁統一

**資料對齊與一致性：**
1. label→key 映射：避免顯示文字與值鍵不匹配
2. 空值策略：一致的空值處理邏輯
3. 跨語言支援：中英文欄位都正確處理
4. 前後台對齊：確保顯示與儲存一致
5. 格式統一：所有欄位標籤格式一致

**API 參數硬編碼教訓：**
1. 避免硬編碼關鍵識別欄位
2. 從請求中動態讀取所有參數
3. 提供合理的預設值作為後備
4. 完整的參數定義和驗證

**ACF URL 欄位行為：**
1. ACF URL 欄位會自動添加協議前綴
2. 預設添加 `http://` 而非 `https://`，可能導致連結錯誤
3. 建議在表單說明中提醒用戶輸入完整 URL
4. 可考慮加入 URL 處理過濾器強制使用 https

---

## 🚨 當前挑戰與風險

### **餐廳聯絡與驗證** ⚠️
- **風險**：餐廳 email 難以取得、餐廳老闆回覆率可能較低
- **影響**：資料驗證困難、大量資料停滯待驗證
- **緩解策略**：建立友善的 email 模板、設計多階段提醒機制、建立社群驗證備案

### **資料衝突處理** ⚠️
- **風險**：多名網友提供不同資訊，難以判斷正確性
- **影響**：資料準確性下降、用戶信任度降低
- **緩解策略**：建立衝突檢測與解決機制、優先採用餐廳老闆驗證、保留多版本供人工審核

### **Reddit 社群接受度**
- **風險**：新帳號可能被視為推廣或 spam
- **緩解**：先建立信譽，提供有價值的建議
- **備案**：準備多個社群平台互動

### **資料品質控制**
- **風險**：回覆可能包含錯誤資訊
- **緩解**：交叉驗證、記錄來源
- **備案**：保留原始爬蟲資料作為備份

---

*最後更新：2025年11月3日*
*版本：v18.0*
*明日重點：餐廳聯絡機制與資料驗證系統實作*
