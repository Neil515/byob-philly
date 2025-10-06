# BYOB 專案開發進度記錄

## 📅 專案概覽

* **專案名稱**：BYOB (Bring Your Own Bottle) 餐廳平台
* **目前階段**：抽獎系統完整實作與優化完成，準備進入行銷推廣階段
* **核心功能**：餐廳資料管理、BYOB 服務設定、前台展示、SEO、社群與短影音行銷、自動化資料收集、Email/通知系統、顧客推薦表單系統、推薦成功通知系統、抽獎系統
* **技術架構**：WordPress + ACF + WooCommerce + Google Places API + Google Apps Script + 抽獎系統 + Email 通知系統

---

## ✅ 2025年10月6日 — 抽獎系統測試與優化

### 🎯 今日目標
完成抽獎系統的完整測試，修正發現的問題，並優化用戶體驗。

### 已完成項目

* [x] **抽獎系統測試與修正** ⭐ 核心功能
  * 修正抽獎參與者欄位名稱統一問題（participant_name → customer_recommender_name）
  * 修正推薦者姓名空白問題（byob_auto_send_invitation_on_publish 函數中缺少 $recommender_name 變數）
  * 實作動態月份選擇功能（JavaScript AJAX）
  * 完成抽獎系統完整測試

* [x] **未中獎通知系統** ⭐ 新增功能
  * 實作 `byob_send_non_winner_notifications` 未中獎通知函數
  * 實作 `byob_send_non_winner_notification` 單一通知函數
  * 實作 `byob_generate_non_winner_notification_html` HTML 模板生成
  * 智能去重機制：同 Email 只發送一封未中獎通知
  * 整合到抽獎執行流程中自動觸發

* [x] **獎項配置優化** ⭐ 業務邏輯
  * 修改獎項名稱：一等獎→一獎、二等獎→二獎
  * 移除三等獎配置
  * 更新獎品內容：一獎→進口酒商電子禮券、二獎→高級進口紅白酒杯
  * 移除所有金額標示
  * 更新所有相關 Email 模板

* [x] **額外抽獎機會優化** ⭐ 用戶體驗
  * 修改分享方式：使用短網址 https://reurl.cc/4N01nL
  * 簡化操作步驟：從4步簡化為3步
  * 移除重複的標記和回覆步驟
  * 優化社群帳號標記說明

### 技術實現細節

**抽獎系統架構：**
```
餐廳審核通過 → 自動記錄參與者 → 執行抽獎 → 發送中獎/未中獎通知
```

**獎項配置（最終版）：**
```php
$prizes = [
    ['name' => '一獎', 'count' => 1, 'description' => '進口酒商電子禮券'],
    ['name' => '二獎', 'count' => 2, 'description' => '高級進口紅白酒杯']
];
```

**未中獎通知系統：**
* 智能去重：使用 `$sent_emails` 陣列記錄已發送的 Email
* 自動觸發：在抽獎執行後自動發送
* 個性化內容：包含推薦者姓名和推薦的餐廳名稱
* 公平性說明：包含 Mersenne Twister 演算法說明

**動態月份選擇功能：**
* JavaScript AJAX 實現無刷新更新
* 為統計區塊和參與者清單添加 CSS 類別
* AJAX 處理函數 `byob_get_monthly_participants_ajax`
* 選擇不同月份時自動更新統計資料

**額外抽獎機會優化：**
```
1. 點擊連結 https://reurl.cc/4N01nL 開啟抽獎活動貼文，然後分享到你的社群媒體
2. 分享後回覆此Email並附上你的分享貼文連結
3. 我們確認後會為你增加1次抽獎機會！
```

**測試結果（成功）：**
* 抽獎系統基礎功能測試成功
* 推薦者姓名正確顯示
* 動態月份選擇功能正常
* 未中獎通知系統運作正常
* 獎項配置更新正確
* 額外抽獎機會流程簡化成功

---

## ✅ 2025年10月5日 — 抽獎系統完整實作

### 已完成項目

* [x] **抽獎系統核心功能** ⭐ 核心功能
  * 實作 `byob_record_lottery_participant` 記錄推薦者參與抽獎
  * 實作 `byob_execute_lottery` 執行隨機抽獎
  * 實作 `byob_send_winner_notification` 發送中獎通知
  * 實作 `byob_generate_winner_notification_html` 生成中獎通知 HTML

* [x] **Post Type 註冊** ⭐ 資料結構
  * 註冊 `lottery_participant` Post Type（抽獎參與者）
  * 註冊 `lottery_result` Post Type（抽獎結果）
  * 設定適當的標籤和選單位置

* [x] **後台管理介面** ⭐ 使用者介面
  * 新增抽獎管理頁面（`餐廳 > 抽獎管理`）
  * 參與者統計和清單顯示
  * 社群分享機會管理功能
  * 歷史抽獎結果查看

* [x] **Email 模板整合** ⭐ 行銷整合
  * 修改推薦成功 Email 模板，加入抽獎說明
  * 加入社群分享獎勵說明
  * 統一 CTA 按鈕樣式

* [x] **自動記錄機制** ⭐ 系統整合
  * 餐廳審核通過時自動記錄推薦者
  * 避免重複記錄機制
  * 自動計算基本抽獎機會

---

## ✅ 2025年10月4日 — 重複檢查系統完整實作

### 已完成項目

* [x] **重複檢查核心功能** ⭐ 核心功能
  * 實作 `byob_check_duplicate_restaurant` 主檢查函數
  * 實作 `byob_calculate_name_similarity` 名稱相似度計算
  * 實作 `byob_calculate_address_similarity` 地址相似度計算
  * 實作 `byob_extract_road_name` 和 `byob_extract_house_number` 輔助函數
  * 相似度閾值設定為 80%

* [x] **自動觸發機制** ⭐ 系統整合
  * 在 `byob_create_restaurant_article` 中自動檢查重複
  * 發現重複時設為 `pending` 狀態，標記為 `pending_duplicate_review`
  * 未重複時設為 `draft` 狀態，標記為 `pending_general_review`
  * 儲存重複檢查資訊到 post meta

* [x] **後台管理介面** ⭐ 使用者介面
  * 新增審核管理頁面（`餐廳 > 審核管理`）
  * 重複檢查標籤頁，視覺化顯示相似餐廳資訊
  * 顯示相似度百分比和相似餐廳詳細資訊
  * 一鍵操作按鈕（確認重複/不重複）

* [x] **審核處理機制** ⭐ 業務邏輯
  * 實作 `byob_handle_review_confirmation` AJAX 處理函數
  * 確認重複：直接拒絕，移到垃圾桶
  * 確認不重複：立即發布並觸發通知
  * 審核通過：立即發布並觸發通知

---

## ✅ 2025年10月3日 — 推薦成功通知功能完整實作

### 已完成項目

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

## 📊 專案整體進度（截至 2025-10-06）

### 已完成里程碑

* ✅ **顧客推薦表單系統**（核心功能完整）
  * Google 表單建立與 Apps Script 處理
  * WordPress REST API 整合
  * 推薦者資料正確儲存
  * 欄位映射問題完全修正

* ✅ **推薦成功通知系統**（完整實作）
  * 自動觸發機制
  * 判別邏輯完善
  * HTML Email 模板
  * 防重複機制
  * 測試驗證成功

* ✅ **重複檢查系統**（完整實作）
  * 相似度計算演算法
  * 自動觸發機制
  * 後台管理介面
  * 審核處理機制

* ✅ **抽獎系統**（完整實作）
  * 抽獎參與者記錄
  * 隨機抽獎執行
  * 中獎/未中獎通知
  * 後台管理介面
  * 動態月份選擇

* ✅ **餐廳業者表單系統**（純淨版）
  * 動態欄位映射機制
  * 餐廳類型「其他」欄位處理
  * 開瓶費條件式邏輯

* ✅ **WordPress 架構優化**
  * 必填欄位簡化為 3 個核心欄位
  * 支援多種資料來源標記
  * ACF 欄位動態映射

### 進行中模組

* 🔄 抽獎活動文章製作（明日進行）

### 待開發模組

* ⏳ 自動回覆系統實作
* ⏳ 社群推廣素材製作
* ⏳ 抽獎活動推廣素材
* ⏳ KPI 追蹤儀表板
* ⏳ 行銷活動管理系統

---

## 📝 技術筆記

### 抽獎系統架構（已完成）

**功能架構：**
* 自動記錄：餐廳審核通過時自動記錄推薦者
* 隨機抽獎：使用 Mersenne Twister 演算法確保公平性
* 通知系統：中獎者和未中獎者都會收到通知
* 防重複機制：同 Email 只發送一封未中獎通知

**核心函數：**
* `byob_record_lottery_participant`：記錄推薦者參與抽獎
* `byob_execute_lottery`：執行隨機抽獎
* `byob_send_winner_notification`：發送中獎通知
* `byob_send_non_winner_notifications`：發送未中獎通知
* `byob_generate_winner_notification_html`：生成中獎通知 HTML
* `byob_generate_non_winner_notification_html`：生成未中獎通知 HTML

**獎項配置：**
* 一獎：進口酒商電子禮券（1名）
* 二獎：高級進口紅白酒杯（2名）

### 重複檢查系統架構（已完成）

**功能架構：**
* 自動觸發：在 `byob_create_restaurant_article` 中檢查重複
* 相似度計算：名稱相似度 + 地址相似度 ÷ 2
* 閾值設定：≥ 80% 視為可能重複
* 狀態管理：重複設為 `pending`，不重複設為 `draft`

**核心函數：**
* `byob_check_duplicate_restaurant`：主檢查函數
* `byob_calculate_name_similarity`：名稱相似度計算
* `byob_calculate_address_similarity`：地址相似度計算
* `byob_extract_road_name`：路名提取
* `byob_extract_house_number`：門牌號碼提取

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

### 顧客推薦表單系統架構（已完成）

**表單欄位設計：**
1. 餐廳名稱（必填）
2. 餐廳類型（選填，多選）
3. 餐廳地址（必填）
4. 餐廳電話（選填）
5. 開瓶費條件（必填，條件式顯示）
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

*最後更新：2025年10月6日*
*版本：v3.0*