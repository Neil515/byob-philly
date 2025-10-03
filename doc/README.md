# 🍷 BYOB 專案開發文檔

## 📋 專案概述

BYOB (Bring Your Own Bottle) 是一個餐廳資訊平台，讓消費者可以找到支援自帶酒水的餐廳。專案採用「雙軌制」策略，同時從顧客端和餐廳端收集資料。

### 核心目標

* 建立完整的 BYOB 餐廳資料庫
* 提供顧客推薦餐廳機制（降低收集門檻）
* 實現自動化餐廳資料收集（Google Places API 爬蟲）
* 建立餐廳業者會員系統（資料管理與更新）
* 整合 Email 自動化發送系統（邀約、通知、感謝）

---

## 🏗️ 技術架構

### 核心技術棧

* **後端**: WordPress + WooCommerce + ACF Pro
* **SEO**: Rank Math SEO
* **資料收集**: Google Places API + Python 爬蟲 + Google Apps Script
* **Email 發送**: SendGrid API
* **會員系統**: 自定義餐廳業者角色 + 邀請碼機制

### 系統架構圖

```
資料收集層
├── 顧客推薦表單 (Google Form + Apps Script)
├── 餐廳業者表單 (Google Form + Apps Script)
└── 自動爬蟲 (Google Places API + Python)
    ↓
WordPress 後端
├── REST API 端點 (/byob/v1/restaurant)
├── ACF 欄位管理
├── 餐廳文章類型 (Custom Post Type)
├── 推薦成功通知系統
└── 會員系統 (Restaurant Owner Role)
    ↓
前端展示
├── 餐廳列表與篩選
├── 餐廳詳細頁面
└── SEO 優化頁面
```

---

## 🔄 「雙軌制」策略

### 為何需要雙軌制？

* 單靠餐廳端推進：回覆慢、摩擦高、轉化率低
* 顧客端優勢：天然分享動機、社群效應、口碑傳播

### 雙軌運作方式

#### **顧客軌（收集驅動）**
```
顧客填寫推薦表單
  ↓
Google Apps Script 處理
  ↓
WordPress 建立餐廳草稿
  ↓
管理員審核通過
  ↓
自動發送感謝通知給推薦者
```

* **來源標記**: `source=customer_recommendation`
* **推薦者資料**: 儲存在專用欄位 (customer_recommender_name, customer_recommender_email)
* **誘因設計**: 抽獎活動、感謝通知、社群曝光

#### **餐廳軌（轉化驅動）**
```
爬蟲收集餐廳資料
  ↓
先收錄基本資料卡片
  ↓
通知餐廳「已免費上架」
  ↓
引導餐廳補完整資料
  ↓
餐廳業者註冊會員
```

* **來源標記**: `source=google_form` 或 `source=direct`
* **降低摩擦**: 先上架後補資料
* **轉化誘因**: FOMO 效應、免費曝光

---

## 🎯 顧客推薦表單系統（核心功能）

### 系統架構

**1. Google 表單（11 個欄位）**
* 餐廳基本資訊：名稱、類型、地址、電話
* BYOB 政策：開瓶費條件、金額、說明
* 額外資訊：酒器設備、餐廳特色
* 推薦者資訊：姓名、Email（選填）

**2. Google Apps Script 處理**
* 使用「欄位設定表」實現動態映射
* 支援條件式欄位（開瓶費邏輯）
* 「排除法」識別「其他」選項內容
* 自動標記資料來源為 'customer_recommendation'

**3. WordPress REST API 整合**
* API 端點：`/byob/v1/restaurant`
* 建立草稿狀態餐廳文章（待審核）
* 儲存推薦者資料到專用 ACF 欄位

**4. ACF 欄位設計**
* **餐廳聯絡資料**: contact_person, email（可為空）
* **推薦者資料**: customer_recommender_name, customer_recommender_email
* **來源標記**: source = 'customer_recommendation'
* **審核狀態**: review_status = 'pending'

### 技術特點

**動態欄位映射機制：**
使用「欄位設定表」工作表，實現表單欄位與 WordPress ACF 欄位的動態對應，支援欄位名稱變更而不需修改程式碼。

**「排除法」識別機制：**
* 餐廳類型：已知類型列表比對，未知類型自動歸類為「其他」並記錄說明
* 酒器設備：已知設備列表比對，未知設備自動歸類為「其他」並記錄說明
* 已實作：餐廳類型 (restaurant_type_other_note)、酒器設備 (equipment_other_note)

**必填欄位策略：**
* WordPress 端只檢查 3 個核心欄位：restaurant_name, address, is_charged
* Google 表單端控制實際的必填欄位
* 後端保持彈性，支援不同來源的資料

---

## 📅 最新進度（2025年10月3日）

### 完成項目

✅ **推薦成功通知功能完整實作**
* 修改 `byob_auto_send_invitation_on_publish` 函數
* 新增 `byob_send_recommender_notification` 函數
* 新增輔助函數群組（資料取得、格式化、HTML 生成）
* 內嵌 HTML Email 模板
* 測試驗證：推薦者成功收到通知郵件

✅ **酒器設備「其他」欄位處理**
* 新增 `equipment_other_note` ACF 欄位
* 修改 Apps Script 支援「排除法」識別邏輯
* 更新 functions.php 完成欄位處理
* 測試驗證成功

✅ **Email 模板優化**
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

## 🔜 下一步計畫

### 近期任務（10月第一週）

**優先級 1：顧客推薦表單自動回覆系統**
* 設計自動回覆 Email 模板
* 修改 Apps Script 加入自動回覆功能
* 設定抽獎活動說明
* 測試自動回覆流程

**優先級 2：重複餐廳檢查機制**
* 設計重複檢查邏輯
* 實作餐廳名稱比對功能
* 建立友善的錯誤提示
* 測試重複檢查機制

### 中期目標（10月）

* 社群推廣素材製作（IG Reels、貼文、Story）
* 抽獎活動系統建立
* KPI 追蹤儀表板
* 首週目標：收集 ≥50 筆顧客推薦

---

## 📁 專案檔案結構

```
BYOB/
├── wordpress/                          # WordPress 相關檔案
│   ├── functions.php                   # REST API 端點、ACF 欄位處理、推薦通知系統
│   ├── Apps script - 顧客推薦版.js      # 顧客推薦表單處理
│   ├── Apps script - 純淨版.js          # 餐廳業者表單處理
│   ├── restaurant-member-functions.php # 餐廳業者會員系統
│   └── invitation-handler.php          # 邀請碼處理
│
├── restaurant_crawler/                 # 餐廳爬蟲系統
│   ├── restaurant_crawler.py           # 主要爬蟲程式
│   ├── restaurant_crawler_gui_chinese.py # GUI 版本
│   ├── restaurant_email_search.py      # Email 搜尋程式
│   └── list/                           # 爬取結果（Excel 檔案）
│
├── doc/                                # 文檔資料夾
│   ├── README.md                       # 專案概覽（本檔案）
│   ├── ai_progress_byob.md            # 詳細開發進度記錄
│   ├── Next Task Prompt Byob.md       # 工作規劃與任務追蹤
│   ├── message_and_form/              # Email 模板
│   └── articles/                       # 參考文章
│
└── Mid/                                # 媒體資源（圖片、影片）
```

---

## 🔑 核心技術說明

### 顧客推薦表單資料流

```
Google 表單提交
  ↓
Apps Script 觸發 (onCustomerFormSubmit)
  ↓
解析表單資料 (parseCustomerFormData)
  - 讀取「欄位設定表」工作表
  - 動態映射表單欄位到 WordPress 欄位
  - 處理條件式欄位（開瓶費、餐廳類型、酒器設備）
  - 保留推薦者資料到專用欄位
  ↓
發送到 WordPress API (sendToCustomerWordPress)
  - POST /byob/v1/restaurant
  - 包含推薦者欄位：customer_recommender_name, customer_recommender_email
  - 標記來源：source = 'customer_recommendation'
  ↓
WordPress 處理 (byob_create_restaurant_post)
  - 參數映射和資料轉換
  - 只檢查 3 個核心必填欄位
  - 調用共用函數建立餐廳文章
  ↓
建立餐廳草稿 (byob_create_restaurant_article)
  - 更新所有 ACF 欄位
  - 包含推薦者資料到專用欄位
  - 設定 review_status = 'pending'
  ↓
管理員審核通過
  ↓
自動發送推薦成功通知
```

### 推薦成功通知流程

```
餐廳文章狀態變更為 'publish'
  ↓
transition_post_status hook 觸發
  ↓
byob_auto_send_invitation_on_publish 函數執行
  ↓
檢查資料來源 (source 欄位)
  ↓
判別邏輯：
  - source === 'customer_recommendation' → 發送推薦者通知
  - contact_person 不為空 → 發送業者邀請
  ↓
byob_send_recommender_notification 函數
  ↓
生成 HTML Email 內容
  ↓
發送通知郵件
  ↓
設定防重複標記 (_byob_recommender_notified)
```

### 必填欄位設計哲學

**前端（Google 表單）：** 完整的必填欄位控制
* 餐廳名稱、地址、開瓶費政策等

**後端（WordPress API）：** 最小化必填限制
* 只檢查 3 個絕對核心欄位：restaurant_name, address, is_charged
* 其他欄位都可選，支援不同來源的資料結構

**優點：**
* 前端保持嚴謹的資料品質
* 後端保持彈性，支援多種資料來源
* 降低系統耦合度

### 欄位映射機制

使用 Google Sheets「欄位設定表」工作表：

```
WordPress 欄位           | 表單欄位名稱
------------------------+---------------------------
restaurant_type         | 餐廳類型 (選填)
address                 | 餐廳地址(必填)
phone                   | 餐廳電話(選填)
is_charged              | 開瓶費(必填)
equipment               | 酒器設備(選填)
notes                   | 餐廳特色(選填)
customer_recommender_name  | 您的姓名或暱稱
customer_recommender_email | 聯絡Email
```

**優點：**
* 表單欄位名稱改變時，只需更新試算表
* 不需修改程式碼
* 容易維護和擴充

---

## 🎯 已實作功能

### ✅ 顧客推薦表單系統（完整）

**核心功能：**
* 11 個欄位的 Google 表單
* 動態欄位映射機制（欄位設定表）
* 條件式欄位顯示（開瓶費邏輯）
* 「排除法」識別「其他」選項內容
* 推薦者資料正確儲存到專用 ACF 欄位
* 自動標記資料來源

**技術特點：**
* 基於純淨版 Apps Script 結構
* 全形轉半形處理確保欄位匹配
* 完整的錯誤處理和日誌記錄
* 管理員 Email 通知

**ACF 欄位設計：**
* `customer_recommender_name`: 推薦者姓名
* `customer_recommender_email`: 推薦者 Email
* `source`: 資料來源標記 ('customer_recommendation')
* `review_status`: 審核狀態 ('pending')

### ✅ 推薦成功通知系統（完整）

**核心功能：**
* 自動觸發機制（餐廳發布時）
* 判別邏輯完善（區分推薦者與業者）
* HTML Email 模板
* 防重複機制
* 測試驗證成功

**技術特點：**
* 使用 `transition_post_status` hook
* 內嵌 HTML 模板，響應式設計
* 完整的錯誤處理和日誌記錄
* 支援動態內容替換

### ✅ 餐廳業者表單系統（完整）

* 動態欄位映射機制
* 餐廳類型「其他」欄位處理
* 開瓶費條件式邏輯
* 邀請碼系統整合

### ✅ WordPress REST API（完整）

* `/byob/v1/restaurant` 端點
* API 金鑰驗證機制
* 參數映射和資料轉換
* 必填欄位簡化為 3 個核心欄位
* 支援多種資料來源

### ✅ 自動化爬蟲系統（完整）

* Google Places API 整合
* 網格搜尋突破 60 筆限制
* 多中心點搜尋（台北 12 個區域）
* 斷點續傳功能
* Excel 資料輸出
* Email 搜尋程式

---

## 🔜 待開發功能

### 近期開發（10月第一週）

* [ ] **顧客推薦表單自動回覆系統**
  * 設計自動回覆 Email 模板
  * 修改 Apps Script 加入自動回覆功能
  * 設定抽獎活動說明
  * 測試自動回覆流程

* [ ] **重複餐廳檢查機制**
  * 設計重複檢查邏輯
  * 實作餐廳名稱比對功能
  * 建立友善的錯誤提示
  * 測試重複檢查機制

### 中期開發（10月）

* [ ] 抽獎活動系統
* [ ] 社群推廣素材製作
* [ ] KPI 追蹤儀表板

---

## 📝 重要技術筆記

### 10月3日完成：推薦成功通知系統

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

### 10月3日完成：酒器設備「其他」欄位處理

**實作內容：**
* 新增 `equipment_other_note` ACF 欄位
* 修改 Apps Script 支援「排除法」識別邏輯
* 更新 functions.php 完成欄位處理
* 測試驗證成功

**已知酒器設備清單：**
```javascript
['無提供', '酒杯', '開瓶器', '冰桶', '醒酒器', '酒架', '保溫袋']
```

### 10月1日修正：必填欄位簡化

**問題：** 顧客推薦表單無法提供餐廳聯絡人資料，但 WordPress API 要求必填

**解決方案：**
* WordPress API 只檢查 3 個核心欄位：restaurant_name, address, is_charged
* 其他欄位（contact_person, email, district, phone 等）都改為非必填
* Google 表單端仍保持完整的必填控制

**影響：**
* 系統更靈活，支援多種資料來源
* 顧客推薦不需要提供餐廳聯絡資料
* 餐廳業者表單仍保持完整的資料收集

### 10月1日修正：推薦者資料正確儲存

**問題：** 推薦者資料被錯誤存入餐廳聯絡人欄位

**根源：**
```javascript
// 錯誤的覆寫邏輯（已刪除）
parsedData['contact_person'] = parsedData['customer_recommender_name'] || '顧客推薦';
parsedData['email'] = parsedData['customer_recommender_email'] || 'customer@byob.com';
```

**正確做法：**
```javascript
// 使用空字串預設值，不覆寫推薦者欄位
parsedData['contact_person'] = parsedData['contact_person'] || '';
parsedData['email'] = parsedData['email'] || '';
parsedData['source'] = 'customer_recommendation';
// customer_recommender_name 和 customer_recommender_email 保持原本解析的值
```

**結果：**
* 推薦者資料正確存入 customer_recommender_name 和 customer_recommender_email
* 餐廳聯絡資料（contact_person, email）為空白
* 可透過 source 欄位區分資料來源

---

## 🔍 快速參考

### 核心檔案

* **表單處理**: `wordpress/Apps script - 顧客推薦版.js`
* **API 端點**: `wordpress/functions.php`
* **工作規劃**: `doc/Next Task Prompt Byob.md`
* **進度記錄**: `doc/ai_progress_byob.md`

### 資料來源標記

* `customer_recommendation`: 顧客推薦表單
* `google_form`: 餐廳業者表單
* `direct`: 直接加入（網站表單）
* `auto_listed`: 爬蟲先收錄（待實作）

### ACF 欄位對照

**餐廳基本資料：**
* restaurant_name, restaurant_type, district, address, phone

**BYOB 政策：**
* is_charged, corkage_fee_amount, corkage_fee_note
* equipment, open_bottle_service

**推薦者資料（僅顧客推薦）：**
* customer_recommender_name
* customer_recommender_email

**系統資料：**
* source, review_status, submitted_date

---

*最後更新：2025-10-03*  
*專案階段：推薦成功通知功能已完成，進入自動回覆系統開發階段*  
*下一步：自動回覆系統 + 重複餐廳檢查機制*