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
├── 抽獎系統（參與者管理、抽獎執行、通知發送）
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
  ↓
記錄推薦者參與抽獎
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

## 🎲 抽獎系統（完整功能）

### 系統架構

**1. 抽獎參與者管理**
* 自動記錄推薦者為抽獎參與者
* 統一的 ACF 欄位命名（customer_recommender_name, customer_recommender_email）
* 月份分類管理

**2. 抽獎執行系統**
* 高品質隨機數生成（mt_rand()）
* 防重複中獎機制
* 加權抽獎邏輯（額外分享機會）
* 獎項配置：一獎1名（進口酒商電子禮券）、二獎2名（高級進口紅白酒杯）

**3. 通知系統**
* 中獎者通知（包含獎項資訊）
* 未中獎者通知（包含公平性說明）
* 防重複發送機制（同 Email 只發送一次）
* HTML Email 模板

**4. 管理介面**
* 抽獎管理頁面（WordPress 後台）
* 月份選擇下拉選單
* AJAX 動態更新統計數據
* 參與者列表展示

### 技術實現

**核心函數群組：**
* `byob_record_lottery_participant`：記錄抽獎參與者
* `byob_execute_lottery`：執行抽獎
* `byob_send_winner_notifications`：發送中獎通知
* `byob_send_non_winner_notifications`：發送未中獎通知
* `byob_lottery_management_page`：抽獎管理頁面

**公平性保證：**
* 使用 PHP 的 mt_rand() 高品質隨機數生成器
* 防重複中獎機制確保每人最多中獎一次
* 加權抽獎支援額外分享機會
* 完整的抽獎記錄和統計

---

## 📅 最新進度（2025年10月6日）

### 🎯 今日完成項目

✅ **抽獎系統完整測試與優化**
* 統一 ACF 欄位命名（customer_recommender_name, customer_recommender_email）
* 修正推薦者姓名空白問題
* 實現動態月份選擇功能（AJAX）
* 測試抽獎執行流程

✅ **未中獎者通知系統**
* 實作未中獎者 Email 通知功能
* 防重複發送機制（同 Email 只發送一次）
* 包含抽獎公平性說明
* 測試驗證成功

✅ **獎項配置優化**
* 更新獎項名稱：一獎、二獎
* 更新獎品描述：進口酒商電子禮券、高級進口紅白酒杯
* 移除金額資訊，聚焦獎品本身

✅ **額外抽獎機會優化**
* 簡化分享流程為 3 步驟
* 提供直接分享連結（https://reurl.cc/4N01nL）
* 移除重複的標記步驟
* 優化使用者體驗

### 🔧 技術修正

**ACF 欄位統一：**
* 將所有抽獎相關的 ACF 欄位統一為 customer_recommender_name 和 customer_recommender_email
* 修正推薦者姓名空白問題
* 確保資料一致性

**動態月份選擇：**
* 實作 AJAX 功能處理月份選擇
* 動態更新統計數據和參與者列表
* 提升管理介面使用者體驗

**通知系統完善：**
* 新增未中獎者通知功能
* 實作防重複發送機制
* 優化 Email 模板內容

---

## 🔜 下一步計畫

### 明日任務（10月7日）

**優先級 1：抽獎活動文章製作**
* 撰寫抽獎活動宣傳文章
* 設計社群媒體分享素材
* 準備活動說明頁面

**優先級 2：推薦成功通知重新測試**
* 重新測試推薦成功通知流程
* 驗證 Email 模板內容
* 確認額外抽獎機會說明

**優先級 3：Email 模板完整驗證**
* 測試所有 Email 類型（推薦成功、中獎、未中獎）
* 驗證 Email 內容和格式
* 確認連結和按鈕功能

**優先級 4：系統整合測試**
* 完整測試從推薦到抽獎的流程
* 驗證資料一致性
* 確認所有功能正常運作

---

## 📁 專案檔案結構

```
BYOB/
├── wordpress/                          # WordPress 相關檔案
│   ├── functions.php                   # REST API 端點、ACF 欄位處理、推薦通知系統、抽獎系統
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

### ✅ 推薦成功通知系統（完整）

**核心功能：**
* 自動觸發機制（餐廳發布時）
* 判別邏輯完善（區分推薦者與業者）
* HTML Email 模板
* 防重複機制
* 額外抽獎機會說明

**技術特點：**
* 使用 `transition_post_status` hook
* 內嵌 HTML 模板，響應式設計
* 完整的錯誤處理和日誌記錄
* 支援動態內容替換

### ✅ 抽獎系統（完整）

**核心功能：**
* 自動記錄推薦者為抽獎參與者
* 抽獎執行（高品質隨機數生成）
* 防重複中獎機制
* 中獎者和未中獎者通知
* 管理介面（月份選擇、統計展示）

**技術特點：**
* 統一的 ACF 欄位命名
* AJAX 動態更新功能
* 防重複發送機制
* 完整的抽獎記錄

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

### 近期開發（10月第二週）

* [ ] **抽獎活動文章製作**
  * 撰寫抽獎活動宣傳文章
  * 設計社群媒體分享素材
  * 準備活動說明頁面

* [ ] **重複餐廳檢查機制**
  * 設計重複檢查邏輯
  * 實作餐廳名稱比對功能
  * 建立友善的錯誤提示
  * 測試重複檢查機制

### 中期開發（10月）

* [ ] 社群推廣素材製作
* [ ] KPI 追蹤儀表板
* [ ] 餐廳業者會員系統完善

---

## 📝 重要技術筆記

### 10月6日完成：抽獎系統測試與優化

**ACF 欄位統一：**
* 統一所有抽獎相關欄位為 customer_recommender_name 和 customer_recommender_email
* 修正推薦者姓名空白問題
* 確保資料一致性

**動態月份選擇：**
* 實作 AJAX 功能處理月份選擇
* 動態更新統計數據和參與者列表
* 提升管理介面使用者體驗

**未中獎者通知：**
* 實作未中獎者 Email 通知功能
* 防重複發送機制（同 Email 只發送一次）
* 包含抽獎公平性說明

**獎項配置優化：**
* 更新獎項名稱：一獎、二獎
* 更新獎品描述：進口酒商電子禮券、高級進口紅白酒杯
* 移除金額資訊，聚焦獎品本身

**額外抽獎機會優化：**
* 簡化分享流程為 3 步驟
* 提供直接分享連結（https://reurl.cc/4N01nL）
* 移除重複的標記步驟
* 優化使用者體驗

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

### 抽獎系統欄位

**抽獎參與者：**
* customer_recommender_name（推薦者姓名）
* customer_recommender_email（推薦者 Email）
* lottery_month（參與月份）
* additional_chances（額外抽獎機會次數）

**抽獎結果：**
* lottery_result（中獎結果）
* prize_name（獎項名稱）
* prize_description（獎品描述）

---

*最後更新：2025-10-06*  
*專案階段：抽獎系統完整測試完成，進入活動文章製作階段*  
*下一步：抽獎活動文章製作 + 推薦成功通知重新測試*