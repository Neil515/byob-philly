# BYOB 專案開發進度記錄

## 📅 專案概覽

* **專案名稱**：BYOB (Bring Your Own Bottle) 餐廳平台
* **目前階段**：推薦成功通知功能已完成，進入自動回覆系統開發階段
* **核心功能**：餐廳資料管理、BYOB 服務設定、前台展示、SEO、社群與短影音行銷、自動化資料收集、Email/通知系統、顧客推薦表單系統、推薦成功通知系統
* **技術架構**：WordPress + ACF + WooCommerce + Google Places API + Google Apps Script + 顧客推薦表單系統 + 推薦成功通知系統

---

## ✅ 2025年10月3日 — 推薦成功通知功能完整實作

### 🎯 今日目標
實作推薦成功通知功能，當顧客推薦的餐廳通過審核並上架後，自動發送感謝通知給推薦者。

### 已完成項目

* [x] **酒器設備「其他」欄位處理**
  * 新增 `equipment_other_note` ACF 欄位
  * 修改 Apps Script 支援「排除法」識別邏輯
  * 更新 functions.php 完成欄位處理
  * 測試驗證成功

* [x] **推薦成功通知功能實作**
  * 修改 `byob_auto_send_invitation_on_publish` 函數
  * 新增 `byob_send_recommender_notification` 函數
  * 新增輔助函數群組（資料取得、格式化、HTML 生成）
  * 內嵌 HTML Email 模板
  * 測試驗證：推薦者成功收到通知郵件

* [x] **Email 模板優化**
  * 移除餐廳資訊區塊
  * 調整按鈕樣式（字體顏色、大小）
  * 移除追蹤我們區塊
  * 更新推薦表單連結為實際連結

### 技術實現細節

**推薦成功通知系統架構：**
```
餐廳發布 → transition_post_status hook → 判別資料來源 → 發送對應通知
```

**判別邏輯（最終版）：**
```php
if ($source === 'customer_recommendation' && !empty($recommender_email)) {
    // 發送推薦者通知
    byob_send_recommender_notification($restaurant_id);
} elseif (!empty($contact_person)) {
    // 發送業者邀請通知
    byob_send_approval_notification($restaurant_id);
}
```

**核心函數群組：**
* `byob_auto_send_invitation_on_publish`：主要觸發函數
* `byob_send_recommender_notification`：發送推薦者通知
* `byob_get_restaurant_display_data`：取得餐廳資料
* `byob_generate_recommender_notification_html`：生成 HTML 內容
* `byob_format_corkage_fee`：格式化開瓶費
* `byob_format_equipment`：格式化酒器設備
* `byob_format_contact_info`：格式化聯絡資訊

**Email 模板特色：**
* 移除餐廳資訊區塊，版面更簡潔
* 按鈕樣式：`rgba(139, 38, 53, 0.7)` 背景，`#f8f9fa` 字體
* 按鈕大小：`padding: 16px 32px`，`font-size: 16px`
* 移除追蹤我們區塊，聚焦主要行動
* 推薦表單連結：`https://forms.gle/jAnvmwh2BKyVXq5M8`

**防重複機制：**
* 推薦者通知：`_byob_recommender_notified` post meta
* 業者邀請：`_byob_invitation_sent` post meta

**測試結果（成功）：**
* 推薦者成功收到通知郵件
* Email 內容和格式正確
* 按鈕樣式和連結功能正常
* 防重複機制運作正常

---

## ✅ 2025年10月2日 — 酒器設備「其他」欄位處理完成

### 已完成項目

* [x] **新增 ACF 欄位**
  * 欄位名稱：`equipment_other_note`
  * 欄位類型：文字（Text）
  * 欄位標籤：酒器設備其他說明

* [x] **修改 Apps Script 處理邏輯**
  * 更新 `knownEquipment` 陣列：`['無提供', '酒杯', '開瓶器', '冰桶', '醒酒器', '酒架', '保溫袋']`
  * 實作「排除法」識別邏輯
  * 測試解析功能正常

* [x] **修改 functions.php**
  * 在 API 參數中新增 `equipment_other_note`
  * 在參數映射中加入 `equipment_other_note`
  * 在 ACF 更新中加入 `equipment_other_note` 處理
  * 測試 API 接收正常

* [x] **測試與驗證**
  * 提交包含「其他」酒器設備的測試表單
  * 檢查 WordPress 後台，確認 `equipment_other_note` 欄位有資料
  * 驗證顯示邏輯正確

---

## ✅ 2025年10月1日 — 顧客推薦表單欄位映射修正完成

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

## 📊 專案整體進度（截至 2025-10-03）

### 已完成里程碑

* ✅ **顧客推薦表單系統**（核心功能完整）
  * Google 表單建立與 Apps Script 處理
  * WordPress REST API 整合
  * 推薦者資料正確儲存
  * 欄位映射問題完全修正
  * 酒器設備「其他」欄位處理完成

* ✅ **推薦成功通知系統**（完整實作）
  * 自動觸發機制
  * 判別邏輯完善
  * HTML Email 模板
  * 防重複機制
  * 測試驗證成功

* ✅ **餐廳業者表單系統**（純淨版）
  * 動態欄位映射機制
  * 餐廳類型「其他」欄位處理
  * 開瓶費條件式邏輯

* ✅ **WordPress 架構優化**
  * 必填欄位簡化為 3 個核心欄位
  * 支援多種資料來源標記
  * ACF 欄位動態映射

### 進行中

* 🔄 自動回覆系統（明日實作）
* 🔄 重複餐廳檢查機制（明日實作）

### 待開發

* ⏳ 社群推廣素材製作
* ⏳ 抽獎活動系統
* ⏳ KPI 追蹤儀表板

---

## 📝 技術筆記

### 推薦成功通知系統架構（已完成）

**功能架構：**
* 觸發機制：`transition_post_status` hook
* 判別邏輯：`source === 'customer_recommendation'` + `contact_person` 檢查
* 防重複機制：`_byob_recommender_notified` post meta
* Email 模板：內嵌 HTML，響應式設計

**核心函數：**
* `byob_auto_send_invitation_on_publish`：主要觸發函數
* `byob_send_recommender_notification`：發送推薦者通知
* `byob_get_restaurant_display_data`：取得餐廳資料
* `byob_generate_recommender_notification_html`：生成 HTML 內容
* `byob_format_corkage_fee`：格式化開瓶費
* `byob_format_equipment`：格式化酒器設備
* `byob_format_contact_info`：格式化聯絡資訊

**Email 模板特色：**
* 移除餐廳資訊區塊，版面更簡潔
* 按鈕樣式：`rgba(139, 38, 53, 0.7)` 背景，`#f8f9fa` 字體
* 按鈕大小：`padding: 16px 32px`，`font-size: 16px`
* 移除追蹤我們區塊，聚焦主要行動
* 推薦表單連結：`https://forms.gle/jAnvmwh2BKyVXq5M8`

### 顧客推薦表單系統架構（已完成）

**表單欄位設計：**
1. 餐廳名稱（必填）
2. 餐廳類型（選填，多選）
3. 餐廳地址（必填）
4. 餐廳電話（選填）
5. 開瓶費（必填，條件式顯示）
6. 開瓶費金額（條件顯示）
7. 開瓶費說明（條件顯示）
8. 酒器設備（選填，多選，支援「其他」）
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
* 酒器設備：已知設備列表比對，未知設備自動歸類為「其他」並記錄說明

---

## 🧭 明日（10/4）行動清單

### 優先級 1：顧客推薦表單自動回覆系統

**目標：** 當顧客提交推薦表單後，立即發送感謝信和抽獎說明

**實作步驟：**
1. 設計自動回覆 Email 模板
2. 修改 Apps Script 加入自動回覆功能
3. 設定抽獎活動說明
4. 測試自動回覆流程

### 優先級 2：重複餐廳檢查機制

**目標：** 避免同一餐廳被重複推薦，提升資料品質

**實作步驟：**
1. 設計重複檢查邏輯
2. 實作餐廳名稱比對功能
3. 建立友善的錯誤提示
4. 測試重複檢查機制

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