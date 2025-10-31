# BYOB 專案開發進度記錄

## 📅 專案概覽

### **台北 BYOB 專案**（主要專案）
* **專案名稱**：BYOB 台北 - 自帶酒水餐廳推薦平台
* **目前階段**：核心系統完成，推廣與酒商合作階段
* **核心功能**：餐廳推薦表單、重複檢查、審核管理、抽獎系統、Email 通知、多平台推廣
* **技術架構**：WordPress + ACF + Google Apps Script + Python 爬蟲工具

### **費城 BYOB 專案**（新專案）
* **專案名稱**：Philadelphia BYOB Restaurant Guide
* **目前階段**：自動化整合完成，進入網站英文化階段
* **核心策略**：多平台資料收集 + Google 表單驗證 + 自動化文章生成 + Reddit 社群互動
* **定位**：成為 Yelp 的 BYOB 專業補充平台

---

## ✅ 2025年10月30日 — 餐廳列表頁顯示邏輯與統一性修復完成

### 🎯 今日目標
修復餐廳列表頁顯示問題，統一 Google 表單 ↔ ACF 對齊，確保費城餐廳能正確顯示，以及 other 類型的正確顯示邏輯。

### 已完成項目

* [x] **移除 district 必填限制** ⭐ 顯示邏輯優化
  * 問題：費城餐廳沒有 `district` 欄位，導致在 `archive-restaurant.php` 列表頁被隱藏
  * 根因：`byob_is_restaurant_complete` 函數檢查 6 個必填欄位，包含 `district`
  * 修復：移除 `district` 的必填檢查，改為檢查 5 個必填欄位
  * 修改檔案：`wordpress/restaurant-member-functions.php`（2576-2591 行）
  * 影響：費城餐廳現在能正常顯示在列表頁

* [x] **修復 other 類型顯示邏輯** ⭐ 顯示一致性
  * 問題：餐廳類型為 `other` 時，列表頁只顯示 `other` 而非 `Other: [description]`
  * 根因：`archive-restaurant.php` 只檢查中文「其他」，未檢查英文 'other'
  * 修復：增加英文 'other' 支援（`strtolower($type) === 'other'` 和 `stripos($types, 'other')`）
  * 修改檔案：`wordpress/archive-restaurant.php`（298, 313, 316 行處理餐廳類型；455, 468, 470 行處理設備）
  * 影響：當餐廳類型或設備為 `other` 且有 `other_note` 時，正確顯示為 `Other: [description]`

* [x] **Notes 欄位命名統一** ⭐ 命名一致性
  * 修改：將 "Dining Experience" 改為 "Notes"
  * 修改檔案：
    - `wordpress/functions.php`：餐廳文章內容顯示（1111 行）
    - `wordpress/Apps script - 費城推薦版.js`：Email 通知（377 行）
  * 建議說明文字："Anything you'd like other BYOB diners to know"
  * 需要手動完成：Google 表單欄位標題、Google Sheets 欄位設定表、ACF 欄位標籤

* [x] **表單 ↔ ACF 對齊修復** ⭐ 資料一致性
  * 單選題 placeholder 回退問題修復
    - 問題：ACF 新增「: -- 請選擇 --」後，WP 端直接用顯示文字寫入，ACF 對不上值鍵導致一律回到 placeholder
    - 修復：在 `wordpress/functions.php` 實作安全的 label→key 映射（就地 if/elseif，無函式宣告），空值一律寫 ''
      - `philly_corkage_fee`：Free→free、Corkage Fee→corkage_fee、Other→other
      - `byob_service_level`：對應 full_service/basic_service/self_service/no_service
      - `show_reddit_username`：以 Yes/No 前綴判斷，規一撇號與空白
  * Other 與備註（equipment/type）一致化
    - 若填寫其他說明，確保陣列包含 'other' 並寫入對應 other_note
    - 前台 `single_restaurant.php` 顯示將 'other' 轉為 `Other: [note]`
    - 修正設備其他說明鍵名相容：以 `equipment_other_note` 為主；Apps Script 跳過直寫，交由解析邏輯產生
  * 前台/通知顯示一致
    - 前台已正確顯示：`Cuisine Type: ... / Other: xxxx`、`Wine Equipment: ... | Other: yyyy`
    - 通知 Email 讀取對應鍵，避免顯示鍵名
  * 嚴重錯誤排除
    - 一度因重複宣告小函式造成致命錯誤，已改為就地 if/elseif 版本，消除風險

### 技術成果

**餐廳列表頁顯示邏輯：**
- 顯示標準：必須滿足 5 個必填欄位（名稱、電話、地址、餐廳類型、開瓶費）
- 不顯示條件：缺少任一必填欄位、單一餐廳頁面以相同標準過濾
- 後台不受影響：管理員仍可查看所有餐廳

**other 類型顯示邏輯：**
- 支援中英文：`其他` 和 `other` 都正確處理
- 顯示格式：有 `other_note` 時顯示 `Other: [note]`，否則顯示原始值
- 一致處理：餐廳類型和設備使用相同邏輯

**命名統一：**
- 前後台一致：程式碼、表單、ACF 使用相同名稱
- 用戶友好：簡潔明確的欄位名稱
- 文化適應：符合美國用戶習慣

**表單 ↔ ACF 對齊：**
- 安全映射：label→key 轉換避免值鍵不匹配
- 空值策略：一致的空白處理邏輯
- 資料品質：確保前台顯示與後台儲存一致

### 修改的檔案

**程式碼檔案：**
- `wordpress/restaurant-member-functions.php`：移除 district 必填檢查
- `wordpress/archive-restaurant.php`：增加 other 類型英文支援（餐廳類型和設備）
- `wordpress/functions.php`：Notes 顯示標題、label→key 映射
- `wordpress/single_restaurant.php`：將 'other' 顯示轉為 `Other: note`（支援字串/陣列情境）
- `wordpress/Apps script - 費城推薦版.js`：Notes Email 標籤、設備/餐廳類型 other 與 note 自動產生

**需要手動完成：**
- Google 表單：欄位標題改為 "Notes"
- Google Sheets：欄位設定表映射更新
- ACF 欄位：標籤改為 "Notes"

---

## ✅ 2025年10月29日 — Google 表單新欄位整合與 ACF 系統優化

### 🎯 今日目標
在費城 BYOB Google 表單中新增「Reddit 用戶名顯示偏好」問題，並完成完整的系統整合與 ACF 顯示問題修復。

### 已完成項目

* [x] **Google 表單新增欄位處理** ⭐ 系統整合完成
  * 在費城 BYOB 表單中新增「Reddit 用戶名顯示偏好」選擇題（Yes/No）
  * 完成 Google Sheets「欄位設定表」映射設定：`show_reddit_username`
  * 修改 Google Apps Script 處理新欄位資料，更新通知 Email 內容
  * 修改 WordPress functions.php API 端點接收新欄位參數（4處修改）
  * 修改 WordPress 資料保存邏輯，正確處理新欄位到 ACF

* [x] **ACF 欄位群組顯示問題修復** ⭐ 後台管理優化
  * 調整「Philly BYOB restaurant」欄位群組的 ACF 位置規則
  * 調整「餐廳欄位」欄位群組的 ACF 位置規則（排除費城分類）
  * 解決費城餐廳同時顯示兩個欄位群組的問題
  * 確保費城餐廳只顯示「Philly BYOB restaurant」欄位群組

* [x] **ACF 欄位空值處理優化** ⭐ 資料品質提升
  * 為三個選填選擇題 ACF 欄位新增空白選項作為第一個選項
    - `philly_corkage_fee`（Corkage Fee）
    - `byob_service_level`（BYOB Service Level）
    - `show_reddit_username`（Reddit 用戶名顯示偏好）
  * 修改程式碼正確處理表單空值，使用 `!empty(trim())` 判斷
  * 避免自動選擇第一個實際選項，確保跳過的選填問題正確對應空白選項

### 技術成果

**新欄位整合流程：**
- Google 表單新增問題 → Google Sheets 欄位設定表 → Apps Script 解析 → WordPress API 接收 → ACF 欄位保存
- 完整的端到端資料流處理，確保新欄位正確傳遞和儲存

**ACF 欄位管理優化：**
- 透過位置規則精確控制欄位群組顯示，使用分類條件判斷
- 費城餐廳分類（`restaurant_category:philly-byob-restaurants`）作為條件
- 提升後台編輯體驗，避免欄位群組重複顯示

**空值處理機制：**
- 選填選擇題在 ACF 中預設顯示「-- 請選擇 --」空白選項
- 程式碼層面優化空值判斷邏輯，確保空值正確處理
- 避免系統自動選擇第一個選項，保持資料準確性

### 修改的檔案

**程式碼檔案：**
- `wordpress/functions.php`：新增 API 參數定義、資料處理邏輯、ACF 欄位更新（4處修改）
- `wordpress/Apps script - 費城推薦版.js`：更新通知 Email 內容

**WordPress 後台設定：**
- ACF 欄位群組位置規則調整（2個群組）
- 新增 ACF 欄位：`show_reddit_username`（單選按鈕，含空白選項）

**Google 設定：**
- Google Sheets「欄位設定表」工作表新增映射
- Google 表單新增問題和選項

---

## ✅ 2025年10月22日 — WordPress 程式碼英文化完成

### 🎯 今日目標
將 WordPress 網站的所有 PHP 檔案前台顯示文字改為英文，為費城 BYOB 專案建立專業的英文網站介面。

### 已完成項目

* [x] **WordPress 程式碼英文化完成** ⭐ 前台介面英文化完成
  * 修改 `archive-restaurant.php` - 餐廳列表頁面英文化
  * 修改 `single_restaurant.php` - 單一餐廳頁面英文化  
  * 修改 `restaurant-profile.php` - 餐廳資料編輯頁面英文化
  * 修改 `restaurant-member-functions.php` - 後台管理功能英文化
  * 修改 `functions.php` - 核心功能檔案英文化

* [x] **保留中文註解策略** ⭐ 開發者友善設計
  * 所有程式碼註解保持中文，僅修改用戶可見的介面文字
  * 確保中文開發者能輕鬆維護程式碼
  * 前台顯示文字完全英文化，提升用戶體驗

* [x] **Apps Script 英文化評估** ⭐ 技術決策完成
  * 確認 `Apps script - 費城推薦版.js` 不需要英文化
  * 該檔案為後端處理腳本，主要供開發者維護使用
  * 保持中文註解和 Logger 訊息，避免破壞與 Google Sheets 的欄位對應

### 技術成果

**WordPress 前台英文化功能：**
- 餐廳列表頁面：所有欄位標籤、按鈕文字、狀態訊息英文化
- 單一餐廳頁面：詳細資訊顯示、聯絡方式、照片區塊英文化
- 餐廳資料編輯：表單欄位、選項、驗證訊息英文化
- 後台管理功能：角色名稱、儀表板內容、通知訊息英文化
- 核心功能檔案：API 設定、系統狀態檢查、統計資訊英文化

**英文化內容範例：**
- 頁面標題：`所有餐廳列表` → `All BYOB Restaurants`
- 欄位標籤：`地址：` → `Address:`, `餐廳聯絡電話：` → `Phone:`
- 按鈕文字：`更多詳情 >>` → `More Details >>`
- 狀態訊息：`目前沒有餐廳資料。` → `No restaurants found.`
- 角色名稱：`餐廳業者` → `Restaurant Owner`

**開發者友善設計：**
- 保留所有中文註解，方便中文開發者維護
- 僅修改用戶可見的前台顯示文字
- 維持程式碼結構和邏輯不變
- 確保後續維護和擴展的便利性

---

## 📊 專案整體進度

### 🍷 台北 BYOB 專案

**已完成核心模組：**
* ✅ **餐廳表單系統**：業者和顧客推薦表單
* ✅ **WordPress 整合**：REST API 和 ACF 欄位映射
* ✅ **推薦通知系統**：自動發送推薦成功通知
* ✅ **重複檢查系統**：智能檢測重複餐廳
* ✅ **審核管理系統**：後台審核流程
* ✅ **抽獎系統**：完整的中獎/未中獎通知
* ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
* ✅ **酒商名單收集**：爬蟲工具和 Email 提取器

**進行中：**
* 🔄 酒商合作邀約 Email 擬定
* 🔄 Facebook 品酒社團規則確認和推廣

**待開發：**
* ⏳ 自動回覆系統
* ⏳ KPI 追蹤儀表板

---

### 🍷 費城 BYOB 專案

**已完成：**
* ✅ **專案規劃**：市場分析、AD 方案、榮譽系統設計
* ✅ **文檔建立**：完整專案計畫文檔
* ✅ **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
* ✅ **資料收集完成**：269 家候選餐廳資料
* ✅ **信心度評估系統**：High/Medium/Low 三級評分
* ✅ **Reddit 帳號建立**：u/findingBYOB 準備就緒
* ✅ **互動追蹤系統**：完整的管理和分析工具
* ✅ **Google 表單建立**：費城 BYOB 餐廳驗證表單完成
* ✅ **自動化整合完成**：Google Apps Script + WordPress API 整合
* ✅ **Reddit 貼文策略準備**：社群互動內容和追蹤系統完成
* ✅ **WordPress 程式碼英文化完成**：所有 PHP 檔案前台顯示文字已改為英文
* ✅ **ACF 系統優化完成**：欄位群組顯示問題修復，空值處理優化
* ✅ **列表頁顯示邏輯修復**：district 限制移除，other 類型顯示修復
* ✅ **表單 ↔ ACF 對齊完成**：label→key 映射、空值策略、資料一致性

**當前狀態：**
* ✅ **資料收集完成**：269 家餐廳（191 Yelp + 78 Google Places）
* ✅ **Google 表單系統**：完整的英文驗證表單，包含 Reddit 用戶名顯示偏好欄位
* ✅ **自動化整合完成**：表單提交自動生成文章草稿，包含新欄位處理
* ✅ **Reddit 社群準備**：貼文策略和追蹤系統完成，準備發布第一則貼文
* 🔄 **餐廳列表頁顯示**：邏輯修復完成，費城餐廳可正常顯示
* 🔄 **Reddit 社群互動階段**：準備發布費城 BYOB 餐廳詢問貼文

**近期重點（10月31日）：**
* 🚀 設計「餐廳資料確認」Google 表單（費城版）
* 🚀 建立欄位映射與 Apps Script 解析邏輯
* 🚀 實作端到端寫入流程

**後續階段：**
* ⏳ 手動內容英文化（首頁、About Us、餐廳加入頁面）
* ⏳ Reddit 社群互動持續優化
* ⏳ 英文網站上線
* ⏳ 榮譽系統和遊戲化功能實作

---

## 📝 技術工具與資源

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統
* WordPress 重複檢查系統

### **費城專案工具**
* **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
* **智能去重系統**：保留來源資訊的資料整合
* **信心度評估**：High/Medium/Low 三級評分系統
* **Reddit 互動追蹤系統**：完整的管理和分析工具
* **Google 表單系統**：費城 BYOB 餐廳驗證表單
* **費城專用 Google Apps Script**：處理費城表單提交
* **費城專用 WordPress API**：自動生成文章草稿
* **自動化文章生成系統**：英文內容模板和 SEO 優化
* **費城專用通知系統**：成功和錯誤通知機制

---

## 📁 核心文檔

### **費城專案文檔**
* `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
* `philly_yelp_crawler/data/combined_byob_restaurants.csv`：269 家候選餐廳資料
* `philly_yelp_crawler/data/high_confidence_byob_restaurants.csv`：10 家高信心度餐廳
* `philly_yelp_crawler/data/crawl_report.json`：詳細爬取統計報告
* `doc/Next Task Prompt Byob.md`：工作規劃與任務追蹤
* `doc/reddit_interaction_tracker.md`：Reddit 貼文記錄
* `reddit_tracker/Reddit_Interaction_Tracker.xlsx`：Reddit 互動追蹤 Excel 檔案

### **台北專案文檔**
* `doc/ai_progress_byob.md`：開發進度記錄（本檔案）
* `doc/lottery_activity_planning.md`：抽獎活動規劃
* `doc/message_and_form/`：Email 通知模板

### **WordPress 檔案**
* `wordpress/functions.php`：REST API 端點和 ACF 欄位處理
* `wordpress/archive-restaurant.php`：餐廳列表頁模板
* `wordpress/single_restaurant.php`：單一餐廳頁模板
* `wordpress/restaurant-member-functions.php`：餐廳會員相關功能
* `wordpress/Apps script - 費城推薦版.js`：費城表單處理
* `wordpress/Apps script - 顧客推薦版.js`：顧客推薦表單處理
* `wordpress/Apps script - 純淨版.js`：餐廳業者表單處理

---

## 💡 關鍵學習與洞察

### **從台北到費城的策略演進**

**台北模式：**
- 抽獎激勵用戶推薦餐廳
- 物質獎勵驅動（酒商禮券、酒杯）
- 一次性參與為主

**費城模式升級：**
- 榮譽系統取代物質抽獎
- 專業認同和社群歸屬驅動
- 長期持續參與機制
- 創始成員特殊身份
- 更低成本、更可持續

**為什麼這樣調整？**
1. **成本考量**：海外專案初期無法負擔持續的物質獎勵
2. **文化差異**：美國用戶更重視專業認同和社群地位
3. **可擴展性**：榮譽系統可以無限擴展，物質獎勵不行
4. **長期價值**：建立真正的專家社群而非獎品獵人

### **技術架構學習**

**多平台爬蟲優勢：**
1. **資料完整性**：不同平台提供互補的餐廳資訊
2. **交叉驗證**：多個來源確認提高資料可信度
3. **風險分散**：單一平台問題不影響整體資料收集
4. **來源追蹤**：清楚記錄每筆資料的來源平台

**Google 表單系統設計：**
1. **用戶體驗優先**：清晰的問題分類和說明文字
2. **文化適應**：英文介面符合美國用戶習慣
3. **結構化收集**：系統化的資料收集流程
4. **自動化準備**：為後續自動化處理做好準備

**資料對齊與一致性：**
1. **label→key 映射**：避免顯示文字與實際值鍵不匹配
2. **空值策略**：一致的空值處理邏輯
3. **跨語言支援**：中英文欄位都正確處理
4. **前後台對齊**：確保顯示與儲存一致

**社群互動管理：**
1. **追蹤系統**：結構化記錄和分析所有互動
2. **信譽建立**：先參與後推廣的策略
3. **長期規劃**：為自動化和 AI 整合做準備

---

## 🚨 當前挑戰與解決方案

### **Reddit 社群互動挑戰**

**挑戰：**
- 新帳號可能被視為推廣或 spam
- 需要建立信譽和專業形象
- 避免過度推銷，保持社群性質

**解決方案：**
- 先建立信譽，提供有價值的建議
- 使用追蹤系統記錄所有互動
- 真誠回應每個貢獻者

### **內容創作挑戰**

**挑戰：**
- 缺乏實際用餐經驗
- 需要基於爬蟲資料和社群回饋
- 確保內容品質和準確性

**解決方案：**
- 標明資料來源，邀請用戶補充
- 建立內容品質檢查機制
- 鼓勵社群驗證和更新

### **跨地區資料差異**

**挑戰：**
- 台北和費城餐廳資料結構不同（如 district 欄位）
- 需要靈活的顯示邏輯

**解決方案：**
- 移除不適用的必填欄位檢查
- 動態判斷資料完整性
- 保持後台可見性的同時優化前台顯示

---

*最後更新：2025年10月30日*
*版本：v16.0*
