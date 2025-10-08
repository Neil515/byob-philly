# 🍷 BYOB 專案開發文檔

## 📋 專案概述

BYOB (Bring Your Own Bottle) 是一個餐廳資訊平台，讓消費者可以找到支援自帶酒水的餐廳。專案採用「雙軌制」策略，同時從顧客端和餐廳端收集資料。

### 核心目標

* 建立完整的 BYOB 餐廳資料庫
* 提供顧客推薦餐廳機制（降低收集門檻）
* 實現自動化餐廳資料收集（Google Places API 爬蟲）
* 建立餐廳業者會員系統（資料管理與更新）
* 整合 Email 自動化發送系統（邀約、通知、感謝）
* 執行多平台推廣策略（社群媒體、酒商合作）

---

## 🏗️ 技術架構

### 核心技術棧

* **後端**: WordPress + WooCommerce + ACF Pro
* **SEO**: Rank Math SEO
* **資料收集**: Google Places API + Python 爬蟲 + Google Apps Script
* **Email 發送**: SendGrid API
* **會員系統**: 自定義餐廳業者角色 + 邀請碼機制
* **抽獎系統**: 參與者管理、隨機抽獎、通知發送
* **推廣系統**: 多平台社群媒體推廣、酒商合作夥伴

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
├── 重複檢查系統（自動檢測重複餐廳）
├── 審核管理系統（後台審核介面）
├── 推薦成功通知系統
├── 抽獎系統（參與者管理、抽獎執行、通知發送）
├── 會員系統 (Restaurant Owner Role)
└── 多平台推廣工具
    ↓
前端展示
├── 餐廳列表與篩選
├── 餐廳詳細頁面
└── SEO 優化頁面
```

---

## 📊 專案進度概覽

### ✅ 已完成模組

* **餐廳業者表單系統**：完整的表單處理和欄位映射
* **顧客推薦表單系統**：核心功能完整，支援抽獎參與
* **WordPress REST API 整合**：穩定的資料傳輸機制
* **ACF 欄位動態映射**：靈活的欄位處理系統
* **重複檢查系統**：智能檢測重複餐廳，相似度計算演算法
* **審核管理系統**：後台審核介面，一鍵操作功能
* **推薦成功通知系統**：自動觸發、HTML Email 模板
* **抽獎系統**：完整的參與者管理、抽獎執行、通知發送
* **多平台推廣策略**：LinkedIn、Instagram、酒商合作準備

### 🔄 進行中模組

* **酒商合作夥伴拓展**：收集聯絡方式、發送合作邀請
* **Facebook 品酒社團推廣**：社團調研、推廣執行

### ⏳ 待開發模組

* **自動回覆系統實作**
* **KPI 追蹤儀表板**
* **行銷活動管理系統**

---

## 🚀 最新進度（2025年10月8日）

### 今日完成工作

* ✅ **LinkedIn 專業版推廣**
  * 發布專業版抽獎活動貼文
  * 強調商業價值和專業性
  * 分享到相關 LinkedIn 群組
  * 追蹤互動數據和轉換效果

* ✅ **Instagram 推廣**
  * 發布 Instagram 抽獎活動貼文
  * 製作 Instagram Story 版本
  * 加入連結貼紙導向活動頁面
  * 使用相關 hashtag 增加曝光

* ✅ **酒商合作 Email 準備**
  * 準備酒商合作邀請 Email 模板
  * 設計合作提案內容
  * 強調互惠合作效益
  * 建立專業的合作關係

* ✅ **Google 表單網址優化**
  * 解決 Google 表單縮短網址變動問題
  * 建議使用 reurl.cc 建立固定短網址
  * 確保推廣連結穩定性

### 明日工作重點（10月9日）

* **酒商合作夥伴拓展**
  * 收集 15-20 個酒商聯絡方式
  * 發送 10-15 封合作邀請 Email
  * 建立酒商合作資料庫

* **Facebook 品酒社團調研**
  * 確認 10 個品酒社團的發文規則
  * 評估社團適宜性
  * 在適合的社團發布推廣貼文

---

## 🎯 核心功能詳解

### 重複檢查系統

**功能特色：**
* 自動觸發：在餐廳資料建立時自動檢查
* 智能演算法：名稱相似度 + 地址相似度計算
* 閾值設定：≥ 80% 視為可能重複
* 特殊邏輯：地址完全相同時強制判定為重複

**技術實現：**
```php
// 地址完全相同時強制判定為重複
if ($addr1_norm === $addr2_norm) {
    $name_similarity = byob_calculate_string_similarity($name1_norm, $name2_norm);
    if ($name_similarity >= 70) {
        return 95; // 地址相同且名稱相似，極高相似度
    } else {
        return 85; // 地址相同但名稱不同，仍然判定為重複
    }
}
```

### 抽獎系統

**功能架構：**
* 自動記錄：餐廳審核通過時自動記錄推薦者
* 隨機抽獎：使用 Mersenne Twister 演算法確保公平性
* 通知系統：中獎者和未中獎者都會收到通知
* 防重複機制：同 Email 只發送一封未中獎通知

**獎項配置：**
* 一獎：進口酒商電子禮券（1名）
* 二獎：高級進口紅白酒杯（2名）

### 推薦成功通知系統

**功能特色：**
* 觸發機制：`transition_post_status` hook
* 判別邏輯：`source === 'customer_recommendation'` + `contact_person` 檢查
* 防重複機制：`_byob_recommender_notified` post meta
* Email 模板：內嵌 HTML，響應式設計

### 多平台推廣策略

**推廣平台：**
1. **LinkedIn 專業版推廣**：目標餐飲業從業人員、品酒愛好者、商務人士
2. **Instagram 推廣**：貼文和 Story 版本，加入連結貼紙
3. **酒商合作夥伴推廣**：邀請現有合作酒商協助推廣
4. **Facebook 品酒愛好者社團**：5-8個相關社團推廣
5. **Facebook 相關社團**：6個目標社團（台北美食、紅酒愛好者等）
6. **Google 我的商家推廣**：Maps 活動貼文，SEO 優化

---

## 📝 表單系統架構

### 顧客推薦表單

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

### 餐廳業者表單

**功能特色：**
* 動態欄位映射機制
* 餐廳類型「其他」欄位處理
* 開瓶費條件式邏輯
* 完整的聯絡資訊收集

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

## 📊 推廣效果追蹤

### 量化指標
* **觸及人數**：各平台貼文觸及人數
* **互動數**：按讚、留言、分享數量
* **點擊率**：連結點擊次數
* **轉換率**：從推廣到表單提交的轉換
* **參與者增加**：抽獎參與者數量變化

### 質化指標
* **品牌知名度**：搜尋「BYOB 台北」的排名變化
* **用戶反饋**：各平台用戶留言和反應
* **合作夥伴滿意度**：酒商合作推廣效果
* **社群影響力**：在相關社群的討論度

---

## 🎯 抽獎活動資訊

### 活動規則
* 推薦餐廳即可參與抽獎
* 每月定期抽獎
* 使用 Mersenne Twister 演算法確保公平性
* 中獎與否都會收到 Email 通知

### 額外抽獎機會
* 審核通過後分享活動貼文
* 回覆 Email 附上分享連結
* 可獲得額外 1 次抽獎機會

### 推廣素材
* 主圖：黃色酒瓶圖
* 連結：WordPress 活動頁面
* 表單：https://forms.gle/jAnvmwh2BKyVXq5M8
* 分享連結：https://reurl.cc/4N01nL

---

*最後更新：2025年10月8日*  
*專案階段：多平台推廣策略執行階段*  
*版本：v6.0*