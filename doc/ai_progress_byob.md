# BYOB 專案開發進度記錄

## 📅 專案概覽

* **專案名稱**：BYOB (Bring Your Own Bottle) 餐廳平台
* **目前階段**：顧客推薦表單系統核心功能已完成，進入功能優化階段
* **核心功能**：餐廳資料管理、BYOB 服務設定、前台展示、SEO、社群與短影音行銷、自動化資料收集、Email/通知系統、顧客推薦表單系統
* **技術架構**：WordPress + ACF + WooCommerce + Google Places API + Google Apps Script + 顧客推薦表單系統

---

## ✅ 2025年10月1日 — 顧客推薦表單欄位映射修正完成

### 🎯 今日目標
修正顧客推薦表單的欄位映射錯誤，確保推薦者資料正確儲存到專用 ACF 欄位。

### 已完成項目

* [x] **問題診斷與根源分析**
  * 發現問題：推薦者資料錯誤地存入餐廳聯絡人欄位
  * 根源分析：Apps Script 錯誤覆寫邏輯 + functions.php 必填欄位限制
  * 確認解決方案：簡化必填欄位 + 修正資料映射

* [x] **修正 functions.php（4 處修改）**
  * 將 5 個欄位改為非必填（contact_person、email、restaurant_type、district、phone）
  * 新增 3 個 API 參數（customer_recommender_name、customer_recommender_email、source）
  * 在參數映射表中加入推薦者欄位映射
  * 在資料轉換中加入推薦者欄位處理
  * 修正 `byob_create_restaurant_article` 必填欄位檢查（只保留 3 個核心欄位）
  * 修正 `byob_create_restaurant_post` 必填參數檢查（只保留 3 個核心欄位）
  * ACF 欄位更新加入 customer_recommender_name 和 customer_recommender_email
  * 優化 source 欄位邏輯（優先使用傳入的 source 值）

* [x] **修正 Apps Script - 顧客推薦版.js（2 處修改）**
  * 刪除錯誤的覆寫邏輯（將推薦者資料複製到 contact_person 和 email）
  * 改用空字串作為預設值（不使用誤導性的「待確認」、「pending@byob.com」）
  * 正確保留 customer_recommender_name 和 customer_recommender_email 的原始值
  * 新增 source 欄位標記為 'customer_recommendation'
  * 更新必填欄位檢查（只檢查 3 個核心欄位）

* [x] **測試與驗證**
  * 提交測試表單（餐廳：牛肉麵，推薦者：NeilHH）
  * 驗證推薦者資料正確顯示在 customer_recommender_name 和 customer_recommender_email
  * 驗證 contact_person 和 email 為空字串（不是預設值）
  * 驗證 source 欄位標記為 'customer_recommendation'
  * 驗證餐廳類型「排除法」處理正常（日式, Buffet, 其他 + 海鮮說明）

### 技術實現細節

**問題根源：**
```
原因：functions.php 要求 contact_person 和 email 必填
      ↓
應急方案：Apps Script 將推薦者資料複製到這些欄位
      ↓
結果：推薦者資料覆寫了餐廳聯絡人欄位
```

**解決方案：**
```
1. functions.php：簡化必填欄位為 3 個核心欄位
2. functions.php：新增推薦者欄位的 API 參數和映射
3. Apps Script：改用空字串預設值，不覆寫推薦者欄位
4. 結果：推薦者資料正確存入專用欄位，餐廳聯絡資料為空
```

**核心必填欄位（最終版）：**
* `restaurant_name`：餐廳名稱
* `address`：餐廳地址
* `is_charged`：開瓶費政策

**推薦者專用欄位（已正確實作）：**
* `customer_recommender_name`：推薦者姓名
* `customer_recommender_email`：推薦者 Email
* `source`：資料來源（'customer_recommendation'）

**測試結果（成功）：**
```json
{
  "restaurant_name": "牛肉麵",
  "address": "台北市中山區中山北路二段100號",
  "is_charged": "其他",
  "customer_recommender_name": "NeilHH",
  "customer_recommender_email": "wavyclub21@gmail.com",
  "source": "customer_recommendation",
  "contact_person": "",
  "email": "",
  "district": ""
}
```

---

## ✅ 2025年9月30日 — 顧客推薦表單系統建立完成

### 已完成項目

* [x] **建立顧客推薦 Google 表單**：11 個欄位，支援條件式顯示
* [x] **建立 Google Apps Script 處理程式**：基於純淨版結構，動態欄位映射
* [x] **整合 WordPress REST API**：成功建立餐廳草稿文章
* [x] **新增 ACF 欄位**：customer_recommender_name、customer_recommender_email

### 技術實現

* Apps Script 基於純淨版結構，使用「欄位設定表」工作表
* 餐廳類型「排除法」識別「其他」內容
* 開瓶費條件式邏輯處理
* 全形轉半形函數確保標題匹配

---

## 🔄 策略調整 — 「顧客/餐廳雙軌制」

**決議日期**：2025-09-28

### 雙軌描述

* **顧客軌（收集驅動）**：
  * 「爆料 BYOB 餐廳」表單 + 抽獎誘因 + 社群排行榜
  * 來源標記：`source=customer_recommendation`
  * 每週公布新增名單，建立參與回饋

* **餐廳軌（低摩擦轉化）**：
  * 先收錄精簡卡片 → 通知店家「已免費上架」→ 引導一鍵補資料
  * 來源標記：`source=google_form | direct`
  * 追蹤：補資料轉化率、回覆時長

### 成功指標（首週）

* 顧客端：提交 ≥ 50 筆、表單完成率 ≥ 60%、社群 CTR ≥ 2%
* 餐廳端：先收錄 100 家覆核完成、通知開啟率 ≥ 25%、補資料轉化 ≥ 15%

---

## 📊 專案整體進度（截至 2025-10-01）

### 已完成里程碑

* ✅ **顧客推薦表單系統**（核心功能完整）
  * Google 表單建立與 Apps Script 處理
  * WordPress REST API 整合
  * 推薦者資料正確儲存
  * 欄位映射問題完全修正

* ✅ **餐廳業者表單系統**（純淨版）
  * 動態欄位映射機制
  * 餐廳類型「其他」欄位處理
  * 開瓶費條件式邏輯

* ✅ **WordPress 架構優化**
  * 必填欄位簡化為 3 個核心欄位
  * 支援多種資料來源標記
  * ACF 欄位動態映射

### 進行中

* 🔄 酒器設備「其他」欄位處理（待實作）
* 🔄 推薦成功通知系統（規劃中）
* 🔄 表單自動回覆系統（規劃中）

### 待開發

* ⏳ 重複餐廳檢查機制
* ⏳ 社群推廣素材製作
* ⏳ 抽獎活動系統
* ⏳ KPI 追蹤儀表板

---

## 📝 技術筆記

### 顧客推薦表單系統架構（已完成）

**表單欄位設計：**
1. 餐廳名稱（必填）
2. 餐廳類型（選填，多選）
3. 餐廳地址（必填）
4. 餐廳電話（選填）
5. 開瓶費（必填，條件式顯示）
6. 開瓶費金額（條件顯示）
7. 開瓶費說明（條件顯示）
8. 酒器設備（選填，多選）
9. 餐廳特色（選填）
10. 推薦者姓名（選填）
11. 推薦者 Email（選填）

**欄位映射機制：**
```
Google 表單欄位 → 欄位設定表 → Apps Script 解析 → WordPress API → ACF 欄位
```

**核心必填欄位（Final）：**
* `restaurant_name`：餐廳名稱
* `address`：餐廳地址
* `is_charged`：開瓶費政策

**推薦者專用欄位：**
* `customer_recommender_name`：推薦者姓名
* `customer_recommender_email`：推薦者 Email
* `source`：資料來源標記（'customer_recommendation'）

**「排除法」識別機制（已實作）：**
* 餐廳類型：已知類型列表比對，未知類型自動歸類為「其他」並記錄說明
* 開瓶費：支援「不收費」、「酌收」、「其他」三種選項
* 酒器設備：待實作（明日任務）

---

## 🧭 明日（10/2）行動清單

### 優先級 1：酒器設備「其他」欄位處理

**實作步驟：**
1. 新增 ACF 欄位：`equipment_other_note`
2. 修改 Apps Script：加入「排除法」識別邏輯
3. 修改 functions.php：API 參數和映射處理
4. 測試驗證

### 優先級 2：推薦成功通知準備

**規劃步驟：**
1. 設計 Email 模板（感謝推薦 + 餐廳連結 + 繼續推薦 CTA）
2. 規劃觸發邏輯（餐廳發布時檢查 source）
3. 準備實作方案文檔
4. 設計防重複發送機制

---

## 📁 核心檔案

### WordPress 檔案
* `wordpress/functions.php`：REST API 端點和 ACF 欄位處理
* `wordpress/Apps script - 顧客推薦版.js`：顧客推薦表單處理程式
* `wordpress/Apps script - 純淨版.js`：餐廳業者表單處理程式

### 文檔檔案
* `doc/Next Task Prompt Byob.md`：工作規劃與任務追蹤
* `doc/ai_progress_byob.md`：開發進度詳細記錄
* `doc/message_and_form/`：Email 模板資料夾

### 爬蟲檔案
* `restaurant_crawler/restaurant_crawler.py`：Google Places API 爬蟲程式
* `restaurant_crawler/restaurant_crawler_gui_chinese.py`：GUI 版本爬蟲

---

## 📌 10月1日技術問題解決記錄

### 問題 1：推薦者資料出現在錯誤的 ACF 欄位

**現象：**
* 推薦者姓名出現在 `contact_person` 欄位
* 推薦者 Email 出現在 `email` 欄位
* `customer_recommender_name` 和 `customer_recommender_email` 欄位空白

**根本原因：**
* functions.php 要求 contact_person 和 email 必填
* Apps Script 為了通過驗證，將推薦者資料複製到這些欄位
* 導致推薦者資料覆寫了餐廳聯絡人欄位

**解決方案：**
1. functions.php 簡化必填欄位為 3 個核心欄位（restaurant_name, address, is_charged）
2. functions.php 新增推薦者欄位的 API 參數映射和處理
3. Apps Script 改用空字串預設值，不覆寫推薦者欄位
4. 所有非核心欄位加上 `?? ''` 保護

### 問題 2：functions.php 兩處必填檢查不一致

**現象：**
* 修改了 `byob_create_restaurant_article` 的必填檢查
* 但 `byob_create_restaurant_post` 仍檢查 8 個必填參數
* 導致 API 請求被拒絕（400 錯誤）

**解決方案：**
* 修正 `byob_create_restaurant_post` 函數（第 370-382 行）
* 統一兩處必填檢查邏輯，都只檢查 3 個核心欄位

### 修改檔案清單

1. **wordpress/functions.php**（6 處修改）
   * 第 18-33 行：API 參數必填設定（5 個欄位改為 false）
   * 第 82-93 行：新增推薦者專用 API 參數
   * 第 157-169 行：修正共用函數必填欄位檢查
   * 第 252-272 行：ACF 更新加入推薦者欄位
   * 第 354-356 行：參數映射加入推薦者欄位
   * 第 370-383 行：修正 API 函數必填參數檢查
   * 第 408-410 行：資料轉換加入推薦者欄位

2. **wordpress/Apps script - 顧客推薦版.js**（2 處修改）
   * 第 190-197 行：修正預設值邏輯，不覆寫推薦者欄位
   * 第 201-217 行：更新必填欄位檢查

### 測試驗證結果

**測試資料：**
* 餐廳名稱：牛肉麵
* 餐廳類型：日式, Buffet, 海鮮（自動識別為「其他」）
* 推薦者：NeilHH (wavyclub21@gmail.com)

**驗證結果：**
* ✅ 推薦者姓名正確顯示在 `customer_recommender_name` 欄位
* ✅ 推薦者 Email 正確顯示在 `customer_recommender_email` 欄位
* ✅ `contact_person` 欄位為空白（不是「待確認」）
* ✅ `email` 欄位為空白（不是「pending@byob.com」）
* ✅ `source` 欄位顯示 'customer_recommendation'
* ✅ 餐廳類型「排除法」正常運作（海鮮 → 其他說明）

---

## 🧭 明日（10/2）行動清單

### 優先級 1：酒器設備「其他」欄位處理

**目標：** 支援顧客在選擇「其他」酒器設備時輸入詳細說明

**實作步驟：**
1. 新增 ACF 欄位：`equipment_other_note`（酒器設備其他說明）
2. 修改 Apps Script 加入「排除法」識別邏輯（類似餐廳類型處理）
3. 修改 functions.php 加入 `equipment_other_note` 參數映射和 ACF 更新
4. 測試驗證

**已知酒器設備清單：**
```javascript
['酒杯', '開瓶器', '冰桶', '醒酒器', '酒架', '保溫袋']
```

### 優先級 2：推薦成功通知準備

**目標：** 當顧客推薦的餐廳通過審核並上架後，自動發送感謝通知給推薦者

**規劃步驟：**
1. 設計 Email 模板
   * 感謝推薦者的貢獻
   * 告知餐廳已成功上架
   * 提供餐廳頁面連結
   * 邀請繼續推薦其他餐廳
   * 說明抽獎資格（如有）

2. 規劃觸發邏輯
   * Hook：`transition_post_status`（餐廳發布時）
   * 檢查：source = 'customer_recommendation'
   * 檢查：customer_recommender_email 不為空
   * 防重複：post meta `_byob_recommender_notified`

3. 準備實作方案
   * 建立 `byob_send_recommender_notification` 函數
   * 參考現有的 `byob_send_approval_notification` 結構
   * 建立錯誤處理和日誌記錄

---

## 📌 待解決問題清單

### 技術問題

* ⏳ **酒器設備「其他」欄位**：需要 ACF 欄位和識別邏輯
* ⏳ **推薦成功通知**：需要 Email 模板和觸發機制
* ⏳ **重複餐廳檢查**：避免同一餐廳被重複推薦
* ⏳ **表單自動回覆**：推薦者提交後立即收到感謝信

### 行銷問題

* ⏳ **社群推廣素材**：IG Reels、貼文、Story 投票
* ⏳ **抽獎活動規則**：獎品、頻率、公告方式
* ⏳ **KPI 追蹤系統**：提交數、完成率、CTR 監控

---

## 🎯 短期目標（10月第一週）

### 技術開發
- [ ] 完成酒器設備「其他」欄位處理
- [ ] 實作推薦成功通知功能
- [ ] 建立表單自動回覆系統
- [ ] 設計重複餐廳檢查機制

### 內容準備
- [ ] 設計推薦成功通知 Email 模板
- [ ] 準備表單自動回覆內容
- [ ] 準備社群推廣素材（IG Reels、貼文）
- [ ] 設計抽獎活動規則

### 測試驗證
- [ ] 完整流程測試（表單 → 審核 → 通知）
- [ ] Email 發送測試
- [ ] 資料完整性驗證

---

## 📖 參考文檔

### 技術文檔
* `wordpress/Apps script - 顧客推薦版.js`：顧客推薦表單處理程式
* `wordpress/Apps script - 純淨版.js`：餐廳業者表單處理程式
* `wordpress/functions.php`：WordPress REST API 端點和 ACF 欄位處理

### 規劃文檔
* `doc/Next Task Prompt Byob.md`：工作規劃與任務追蹤
* `doc/ai_progress_byob.md`：開發進度詳細記錄

### Email 模板
* `doc/message_and_form/byob_invitation_friendly.txt`：餐廳邀約 Email 模板
* `doc/message_and_form/byob_invitation_formal.txt`：餐廳邀約正式版模板
