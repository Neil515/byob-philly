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
* **酒商工具**: 葡萄酒展參展商爬蟲 + Email 提取器

### 系統架構圖

```
資料收集層
├── 顧客推薦表單 (Google Form + Apps Script)
├── 餐廳業者表單 (Google Form + Apps Script)
├── 自動爬蟲 (Google Places API + Python)
└── 酒商名單收集 (葡萄酒展爬蟲 + Email 提取器)
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

* **餐廳表單系統**：業者和顧客推薦表單
* **WordPress 整合**：REST API 和 ACF 欄位映射
* **推薦通知系統**：自動發送推薦成功通知
* **重複檢查系統**：智能檢測重複餐廳
* **審核管理系統**：後台審核流程
* **抽獎系統**：完整的中獎/未中獎通知
* **多平台推廣**：LinkedIn、Instagram 推廣
* **酒商名單收集**：爬蟲工具和 Email 提取器

### 🔄 進行中模組

* **酒商合作邀約 Email 擬定**
* **Facebook 品酒社團規則確認**

### ⏳ 待開發模組

* **自動回覆系統實作**
* **KPI 追蹤儀表板**
* **行銷活動管理系統**

---

## 🚀 最新進度（2025年10月9日）

### 今日完成工作

* ✅ **葡萄酒展參展商爬蟲開發**
  * 開發 `wine_exhibitor_crawler.py` 爬蟲程式
  * 支援舊世界和新世界葡萄酒分類爬取
  * 自動處理分頁和彈出視窗問題
  * 修正網址抓取邏輯，避免抓取追蹤碼
  * 成功抓取廠商詳細頁面的公司網址

* ✅ **Email 提取工具開發**
  * 開發 `email_extractor.py` 程式
  * 支援從 Excel 檔案批量提取 Email
  * 雙重請求機制（requests + Selenium）
  * 智能 Email 過濾和驗證系統
  * 自動排除無效和測試用 Email

* ✅ **技術問題解決**
  * 解決爬蟲抓取錯誤網址問題（Google Tag Manager 追蹤碼）
  * 優化網址驗證邏輯，確保抓取真正的公司官網
  * 修正 CSS selector 和 XPath 抓取邏輯
  * 改善錯誤處理和進度顯示

### 明日工作重點（10月10日）

* **酒商合作邀約 Email 擬定**
  * 整理已爬取的酒商 Excel 檔案
  * 使用 Email 提取器取得聯絡 Email
  * 擬定專業的合作邀約 Email 模板
  * 為不同類型酒商製作專屬版本

* **Facebook 品酒社團規則確認**
  * 逐一確認 10 個社團的發文規則
  * 評估社團適宜性並建立發文策略
  * 記錄適合發文的社團清單和注意事項

---

## 🎯 核心功能概覽

### 重複檢查系統
* 自動觸發檢查、智能相似度計算
* 地址相同強制判定重複邏輯
* 審核管理介面、一鍵操作

### 抽獎系統
* Mersenne Twister 隨機演算法確保公平性
* 自動記錄推薦者、通知中獎/未中獎者
* 獎項：一獎（進口酒商電子禮券）、二獎（高級進口紅白酒杯）

### 推薦成功通知系統
* 自動觸發、HTML Email 模板
* 防重複機制、響應式設計

### 多平台推廣策略
* LinkedIn 專業版、Instagram、Facebook 社團
* 酒商合作夥伴、Google 我的商家

### 酒商名單收集工具
* 葡萄酒展參展商爬蟲：自動抓取廠商名單和網址
* Email 提取器：從網址批量提取聯絡 Email
* 智能過濾和驗證系統

---

## 📝 表單系統架構

### 顧客推薦表單
* 11個欄位：餐廳基本資料、BYOB 政策、推薦者資訊
* 支援抽獎參與、社群分享獎勵
* 欄位映射：Google 表單 → Apps Script → WordPress API → ACF

### 餐廳業者表單
* 動態欄位映射、條件式邏輯
* 餐廳類型「其他」欄位處理
* 完整聯絡資訊收集

---

## 🔍 快速參考

### 核心檔案
* **表單處理**: `wordpress/Apps script - 顧客推薦版.js`
* **API 端點**: `wordpress/functions.php`
* **爬蟲工具**: `wine_exhibitor_crawler.py`, `email_extractor.py`
* **工作規劃**: `doc/Next Task Prompt Byob.md`
* **進度記錄**: `doc/ai_progress_byob.md`

### 資料來源標記
* `customer_recommendation`: 顧客推薦表單
* `google_form`: 餐廳業者表單
* `direct`: 直接加入（網站表單）

### 重要 ACF 欄位
* **餐廳資料**: restaurant_name, restaurant_type, address, phone
* **BYOB 政策**: is_charged, corkage_fee_amount, equipment
* **推薦者**: customer_recommender_name, customer_recommender_email
* **抽獎**: lottery_month, additional_chances, lottery_result

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
* 每月定期抽獎，使用 Mersenne Twister 演算法確保公平性
* 中獎與否都會收到 Email 通知

### 推廣素材
* **主圖**：黃色酒瓶圖
* **表單**：https://forms.gle/jAnvmwh2BKyVXq5M8
* **分享連結**：https://reurl.cc/4N01nL
* **獎項**：一獎（進口酒商電子禮券）、二獎（高級進口紅白酒杯）

---

*最後更新：2025年10月9日*  
*專案階段：酒商合作夥伴拓展與資料收集階段*  
*版本：v7.0*