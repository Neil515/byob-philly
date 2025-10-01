# 🍷 BYOB 專案工作規劃與進度追蹤

## 📅 當前日期：2025年10月2日

## 🚀 已完成任務

**9月27日完成工作：** ⭐ 餐廳邀約Email優化與AB測試

* [x] **餐廳邀約Email文案優化** ⭐ 行銷策略優化
* [x] **Email行銷策略完善** ⭐ 內容創作

**9月28日完成工作：** ⭐ 雙軌制策略轉向

* [x] **策略檢討**：發現僅依靠餐廳端推動效果有限
* [x] **顧客端收錄方案設計**：提出「顧客爆料名單」流程與誘因設計
* [x] **雙軌制確立**：同時透過「顧客爆料 → 名單累積」與「餐廳邀約 → 後續轉化」雙向推進

**9月30日完成工作：** ⭐ 顧客推薦表單系統建立

* [x] **建立顧客推薦 Google 表單**：包含餐廳資訊、開瓶費條件、推薦者聯絡方式
* [x] **建立 Google Apps Script 處理程式**：基於純淨版結構，支援條件式欄位處理
* [x] **建立「欄位設定表」工作表**：實現動態欄位映射
* [x] **整合 WordPress REST API**：成功建立餐廳草稿文章
* [x] **新增 ACF 欄位**：`customer_recommender_name`、`customer_recommender_email`
* [x] **修正必填欄位問題**：補上 `contact_person`、`email`、`district`、`phone` 預設值

**10月1日完成工作：** ⭐ 顧客推薦表單欄位映射修正完成

* [x] **分析問題根源**：發現 Apps Script 錯誤地將推薦者資料覆寫到餐廳聯絡人欄位
* [x] **修正 functions.php**：
  * 新增 `customer_recommender_name`、`customer_recommender_email`、`source` 到 API 參數映射
  * 在資料轉換中加入推薦者欄位處理
  * 將必填欄位簡化為 3 個核心欄位（restaurant_name, address, is_charged）
  * 修正兩處必填欄位檢查邏輯
  * ACF 欄位更新已包含推薦者欄位
* [x] **修正 Apps Script**：
  * 刪除錯誤的覆寫邏輯
  * 改用空字串作為預設值（不使用誤導性的「待確認」）
  * 正確保留 customer_recommender_name 和 customer_recommender_email 的原始值
* [x] **測試驗證**：成功提交測試表單，推薦者資料正確顯示在專用 ACF 欄位

---

## 🔴 今日工作重點（10月2日）→ 表單優化與推薦通知準備

### 🚨 優先級 1：酒器設備「其他」欄位處理

**問題描述：**
目前酒器設備欄位支援多選，但沒有「其他」選項的說明文字欄位。當顧客選擇「其他」時，無法輸入詳細說明。

**需求描述：**
1. 新增 ACF 欄位：`equipment_other_note`（酒器設備其他說明）
2. 修改 Apps Script 處理邏輯，支援「排除法」識別「其他」內容
3. 修改 functions.php，加入 `equipment_other_note` 欄位處理
4. 測試驗證

**預估時間：** 1-1.5 小時

---

### **階段 1：新增 ACF 欄位（30 分鐘）**

* [ ] 在 WordPress 後台新增 ACF 欄位：`equipment_other_note`
  * 欄位類型：文字（Text）
  * 欄位標籤：酒器設備其他說明
  * 欄位名稱：equipment_other_note
  * 說明：當選擇「其他」時的補充說明
* [ ] 確認欄位顯示在餐廳編輯頁面

---

### **階段 2：修改 Apps Script 處理邏輯（30 分鐘）**

* [ ] 修改 `parseCustomerFormData` 函數
* [ ] 加入酒器設備「排除法」識別邏輯（類似餐廳類型處理）
* [ ] 測試解析功能

---

### **階段 3：修改 functions.php（15 分鐘）**

* [ ] 在 API 參數中新增 `equipment_other_note`
* [ ] 在參數映射中加入 `equipment_other_note`
* [ ] 在 ACF 更新中加入 `equipment_other_note` 處理
* [ ] 測試 API 接收

---

### **階段 4：測試與驗證（15 分鐘）**

* [ ] 提交包含「其他」酒器設備的測試表單
* [ ] 檢查 WordPress 後台，確認 `equipment_other_note` 欄位有資料
* [ ] 驗證顯示邏輯正確

---

### 🚨 優先級 2：推薦成功通知功能準備

**目標：**
當顧客推薦的餐廳通過審核並上架後，自動發送感謝通知給推薦者。

**需求描述：**
1. 設計推薦成功通知 Email 模板
2. 規劃觸發邏輯（餐廳發布時）
3. 準備實作方案

**預估時間：** 1.5-2 小時

---

### **階段 1：Email 模板設計（45 分鐘）**

* [ ] 設計推薦成功通知 Email 內容：
  * 感謝推薦者的貢獻
  * 告知餐廳已成功上架
  * 提供餐廳連結
  * 說明抽獎資格（如有）
  * 邀請繼續推薦其他餐廳
* [ ] 準備 HTML 模板（參考現有 Email 模板風格）
* [ ] 建立 Markdown 文檔記錄模板

---

### **階段 2：觸發邏輯規劃（30 分鐘）**

* [ ] 分析現有的餐廳發布流程
* [ ] 確認推薦者資料來源（`customer_recommender_email`）
* [ ] 規劃檢查機制：
  * 確認是顧客推薦來源（source = 'customer_recommendation'）
  * 確認有推薦者 Email
  * 避免重複發送
* [ ] 設計 post meta 標記系統（`_byob_recommender_notified`）

---

### **階段 3：實作方案準備（15 分鐘）**

* [ ] 決定實作方式：
  * 使用現有的 `byob_auto_send_invitation_on_publish` 機制
  * 或建立新的 hook 函數
* [ ] 列出需要修改的檔案清單
* [ ] 準備測試計畫

---

### **階段 4：文檔整理（15 分鐘）**

* [ ] 建立 `byob_recommender_notification.md` 文檔
* [ ] 記錄 Email 模板
* [ ] 記錄實作規劃
* [ ] 記錄測試案例

---

## 🎯 今日成功標準（10月2日）

* [ ] ✅ 新增 `equipment_other_note` ACF 欄位
* [ ] ✅ Apps Script 支援酒器設備「其他」選項處理
* [ ] ✅ functions.php 完成 `equipment_other_note` 欄位處理
* [ ] ✅ 測試表單提交成功，「其他」說明正確顯示
* [ ] ✅ 推薦成功通知 Email 模板設計完成
* [ ] ✅ 推薦成功通知觸發邏輯規劃完成
* [ ] ✅ 推薦成功通知實作方案文檔準備完成

---

## 🟡 後續工作重點（10月3日）— 推薦通知功能實作與表單推廣

### A. 推薦成功通知功能實作

* [ ] 實作 `byob_send_recommender_notification` 函數
* [ ] 整合到餐廳發布流程
* [ ] 測試通知發送
* [ ] 確認 Email 內容和格式
* [ ] 建立錯誤處理機制

### B. 顧客推薦表單優化

* [ ] 優化表單使用者體驗（問題順序、說明文字）
* [ ] 設定自動回覆（感謝信 + 抽獎說明）
* [ ] 建立資料驗證機制（重複餐廳檢查）
* [ ] 優化通知郵件格式和內容

### C. 社群推廣準備

* [ ] 準備社群素材：IG Reels（含推薦 CTA）、貼文、Story 投票
* [ ] 設計抽獎活動規則（每月：餐酒券/酒杯/禮券）
* [ ] 建立 KPI 追蹤：首日 ≥10 筆提交、CTR ≥2%、完成率 ≥60%
* [ ] 準備推廣時間表和內容日曆

---

## ✅ 後續成功標準（10月3日）

* [ ] 推薦成功通知功能完整實作
* [ ] 通知發送測試成功
* [ ] 顧客推薦表單優化完成
* [ ] 自動回覆系統設定完成
* [ ] 社群推廣素材準備完成

---

## 📝 技術筆記更新

### 顧客推薦表單系統架構（已完成修正）

**表單欄位設計：**
1. 餐廳名稱（必填）
2. 餐廳類型（選填，多選）
3. 餐廳地址（必填）
4. 餐廳電話（選填）
5. 開瓶費條件（必填，條件式顯示）
6. 開瓶費金額（條件顯示）
7. 開瓶費說明（條件顯示）
8. 酒器設備（選填，多選）
9. 餐廳特色（選填）
10. 推薦者姓名（選填）
11. 推薦者 Email（選填）

**技術實現：**
* Google Apps Script 基於純淨版結構
* 使用「欄位設定表」工作表實現動態映射
* 支援條件式欄位處理（開瓶費邏輯）
* 餐廳類型「排除法」識別「其他」內容
* WordPress REST API 整合，建立草稿文章
* **核心必填欄位簡化為 3 個**：restaurant_name, address, is_charged

**ACF 欄位對應（已完成）：**
* ✅ `customer_recommender_name`：推薦者姓名
* ✅ `customer_recommender_email`：推薦者 Email
* ✅ `source`：標記為 'customer_recommendation'
* ✅ `contact_person`：空字串（不使用預設值）
* ✅ `email`：空字串（不使用預設值）
* ⏳ `equipment_other_note`：酒器設備其他說明（待新增）

**10月1日修正總結：**
1. ✅ 修正欄位映射錯誤：推薦者資料現在正確存入專用 ACF 欄位
2. ✅ 簡化必填欄位：functions.php 兩處必填檢查都已改為只檢查 3 個核心欄位
3. ✅ 移除誤導性預設值：contact_person 和 email 改用空字串
4. ✅ 新增 source 欄位：標記為 'customer_recommendation'
5. ✅ 完整測試驗證：表單提交成功，資料正確顯示

### 待實作功能

**酒器設備「其他」欄位：**
* 需要新增 `equipment_other_note` ACF 欄位
* Apps Script 需要加入「排除法」識別邏輯（類似餐廳類型）
* functions.php 需要加入欄位映射和處理

**推薦成功通知系統：**
* Email 模板設計
* 觸發機制（餐廳發布時檢查 source）
* 推薦者資料驗證
* 防止重複發送機制
* 錯誤處理和日誌記錄

### 社群推廣策略

* IG Story 投票：「你知道哪些餐廳可以自帶酒嗎？」
* IG Reels Hook：「台北居然有這麼多餐廳可以自帶酒？！🍷」
* IG 貼文 CTA：「留言或填表單推薦餐廳 → 抽餐酒券」
* 每月抽獎（餐酒券 / 酒杯 / 禮券）

---

## 🔍 參考文檔

* `wordpress/Apps script - 顧客推薦版.js`：顧客推薦表單處理程式
* `wordpress/Apps script - 純淨版.js`：餐廳業者表單處理程式
* `wordpress/functions.php`：WordPress REST API 端點和 ACF 欄位處理
* `restaurant_crawler/restaurant_crawler.py`：Google Places API爬蟲程式
* `doc/ai_progress_byob.md`：詳細的開發進度記錄
* `doc/message_and_form/byob_invitation_friendly.txt`：餐廳邀約Email模板

---

## 📊 專案進度概覽

### 已完成模組
- ✅ 餐廳業者表單系統
- ✅ 顧客推薦表單系統（核心功能）
- ✅ WordPress REST API 整合
- ✅ ACF 欄位動態映射
- ✅ 餐廳類型「其他」欄位處理
- ✅ 開瓶費條件式邏輯
- ✅ 推薦者欄位正確儲存

### 進行中模組
- 🔄 酒器設備「其他」欄位處理（明日完成）
- 🔄 推薦成功通知系統（規劃中）

### 待開發模組
- ⏳ 表單自動回覆系統
- ⏳ 重複餐廳檢查機制
- ⏳ 社群推廣素材製作
- ⏳ 抽獎活動系統
- ⏳ KPI 追蹤儀表板
