# 🍷 BYOB 專案工作規劃與進度追蹤

## 📅 當前日期：2025年10月1日

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

---

## 🔴 今日工作重點（10月1日）→顧客推薦表單細節修正

### 🚨 優先級 1：顧客推薦表單欄位映射修正

**問題描述：**
顧客推薦表單已成功建立餐廳草稿，但存在欄位映射錯誤：
1. 推薦人的 email 出現在原有的餐廳 email 欄位，而 `customer_recommender_email` 欄位空白
2. 推薦人名稱出現在 `contact_person`，而 `customer_recommender_name` 欄位空白

**需求描述：**
修正 Apps Script 程式碼中的欄位映射邏輯，確保顧客推薦資料正確對應到專用 ACF 欄位。

---

### **1. 分析 WordPress API 處理邏輯（上午 9:00-10:00）**

* [ ] 檢查 `byob_create_restaurant_post` 函數的參數處理
* [ ] 分析 ACF 欄位更新邏輯
* [ ] 確認 `customer_recommender_name` 和 `customer_recommender_email` 欄位處理
* [ ] 檢查參數映射表是否包含新欄位

---

### **2. 修正 Apps Script 資料發送（上午 10:00-11:00）**

* [ ] 檢查 `sendToCustomerWordPress` 函數的資料格式
* [ ] 確認發送到 WordPress 的資料包含正確的欄位名稱
* [ ] 修正欄位映射，確保 `customer_recommender_name` 和 `customer_recommender_email` 正確傳送
* [ ] 測試修正後的資料格式

---

### **3. 測試與驗證（上午 11:00-12:00）**

* [ ] 提交測試表單資料
* [ ] 檢查 WordPress 後台草稿文章
* [ ] 驗證 ACF 欄位是否正確填入
* [ ] 確認 `customer_recommender_name` 和 `customer_recommender_email` 欄位有資料
* [ ] 確認原有 `email` 和 `contact_person` 欄位使用預設值

---

## 🎯 今日成功標準（10月1日）

* [ ] 修正顧客推薦表單欄位映射問題
* [ ] `customer_recommender_name` 欄位正確填入推薦者姓名
* [ ] `customer_recommender_email` 欄位正確填入推薦者 Email
* [ ] 原有 `email` 和 `contact_person` 欄位使用預設值
* [ ] 測試表單提交成功，WordPress 後台顯示正確資料

---

## 🟡 明日工作重點（10月2日）— 顧客推薦表單優化與推廣

### A. 顧客推薦表單優化

* [ ] 優化表單使用者體驗（問題順序、說明文字）
* [ ] 設定自動回覆（感謝信 + 抽獎說明）
* [ ] 建立資料驗證機制（重複餐廳檢查）
* [ ] 優化通知郵件格式和內容
* [ ] 建立資料統計和追蹤機制

### B. 社群推廣準備

* [ ] 準備社群素材：IG Reels（含推薦 CTA）、貼文、Story 投票
* [ ] 設計抽獎活動規則（每月：餐酒券/酒杯/禮券）
* [ ] 建立 KPI 追蹤：首日 ≥10 筆提交、CTR ≥2%、完成率 ≥60%
* [ ] 準備推廣時間表和內容日曆

---

## ✅ 明日成功標準（10月2日）

* [ ] 顧客推薦表單優化完成
* [ ] 自動回覆系統設定完成
* [ ] 資料驗證機制建立
* [ ] 社群推廣素材準備完成
* [ ] 抽獎活動規則設計完成

---

## 📝 技術筆記更新

### 顧客推薦表單系統架構

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

**ACF 欄位對應：**
* `customer_recommender_name`：推薦者姓名
* `customer_recommender_email`：推薦者 Email
* `source`：標記為 'customer_recommendation'
* `verify_status`：設為 'pending'

### 待修正問題

**欄位映射錯誤：**
* 推薦人 Email 出現在原有 `email` 欄位
* 推薦人名稱出現在 `contact_person` 欄位
* 需要修正 Apps Script 資料發送邏輯

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
