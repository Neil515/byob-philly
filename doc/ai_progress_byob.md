# BYOB 專案開發進度記錄

## 📅 專案概覽

* **專案名稱**：BYOB (Bring Your Own Bottle) 餐廳平台
* **目前階段**：多平台推廣策略執行階段
* **核心功能**：餐廳資料管理、BYOB 服務設定、前台展示、SEO、社群與短影音行銷、自動化資料收集、Email/通知系統、顧客推薦表單系統、推薦成功通知系統、抽獎系統、多平台推廣策略
* **技術架構**：WordPress + ACF + WooCommerce + Google Places API + Google Apps Script + 抽獎系統 + Email 通知系統 + 多平台推廣工具

---

## ✅ 2025年10月8日 — 多平台推廣策略執行

### 🎯 今日目標
執行 LinkedIn 和 Instagram 推廣，準備酒商合作 Email 模板，為明日大規模推廣做準備。

### 已完成項目

* [x] **LinkedIn 專業版推廣** ⭐ 專業平台
  * 發布 LinkedIn 專業版抽獎活動貼文
  * 強調商業價值和專業性
  * 加入相關 hashtag：#BYOB #台北美食 #餐飲業 #品酒文化
  * 分享到相關 LinkedIn 群組
  * 邀請餐飲業聯繫人協助分享
  * 追蹤互動數據和轉換效果

* [x] **Instagram 推廣** ⭐ 社群媒體
  * 發布 Instagram 抽獎活動貼文
  * 使用輕快版文案：
    ```
    推薦你愛的 #BYOB 餐廳 🍷
    抽進口酒商禮券與紅白酒杯！
    👉 詳情請見 @byobmap 粉專最新貼文
    #自帶酒水 #台北美食 #抽獎活動 #紅酒控
    ```
  * 製作 Instagram Story 版本
  * 加入連結貼紙導向活動頁面
  * 使用相關 hashtag 增加曝光

* [x] **酒商合作 Email 準備** ⭐ 合作夥伴
  * 準備酒商合作邀請 Email 模板
  * 設計合作提案內容
  * 強調互惠合作效益
  * 建立專業的合作關係
  * 個人化開頭（提到酒展採購經驗）

* [x] **Google 表單網址優化** ⭐ 技術修正
  * 解決 Google 表單縮短網址變動問題
  * 建議使用 reurl.cc 建立固定短網址
  * 更新程式碼中的連結設定
  * 確保推廣連結穩定性

### 技術實現細節

**LinkedIn 專業版貼文內容：**
```
🍷 【BYOB 台北】專業餐飲推薦平台 - 抽獎活動

親愛的餐飲業同仁與品酒愛好者，

我們正在建立台北最完整的 BYOB（自帶酒水）餐廳資料庫，為餐飲業創造新的商業機會。

🎯 【商業價值】
• 協助餐廳業者接觸精準的酒類愛好者客群
• 為品酒愛好者提供便利的餐廳選擇工具
• 促進餐飲業與酒類產業的跨界合作

🎁 【參與獎勵】
推薦您了解的 BYOB 餐廳，即有機會獲得：
🏆 一獎：進口酒商電子禮券（1名）
🥈 二獎：高級進口紅白酒杯（2名）

🔗 【推薦表單】
👉 https://forms.gle/jAnvmwh2BKyVXq5M8

#BYOB #台北餐飲業 #品酒文化 #餐飲創新 #酒類產業 #台北美食 #餐飲業合作
```

**酒商合作 Email 模板要點：**
- 個人化開頭（提到酒展採購經驗）
- 強調互惠合作效益
- 明確的合作提案（獎品贊助、推廣合作）
- 專業的商業價值說明
- 具體的行動呼籲

---

## ✅ 2025年10月7日 — 抽獎活動文章與推廣素材完成

### 🎯 今日目標
完成抽獎活動的社群媒體宣傳文章，並準備多平台推廣策略。

### 已完成項目

* [x] **抽獎活動文章製作** ⭐ 行銷內容
  * 完成 Facebook 抽獎活動宣傳貼文
  * 包含獎品資訊（一獎：進口酒商電子禮券、二獎：高級進口紅白酒杯）
  * 說明參與方式（推薦餐廳獲得抽獎機會）
  * 加入公平性說明（Mersenne Twister 演算法）
  * 設計視覺元素和排版

* [x] **社群媒體素材準備** ⭐ 視覺設計
  * 完成 Facebook 貼文版本
  * 準備 Instagram 貼文版本
  * 準備相關圖片或視覺素材（黃色酒瓶圖）
  * 設定適當的標籤和標記

* [x] **活動連結建立** ⭐ 技術整合
  * 建立抽獎活動的專屬連結
  * 更新 Email 中的分享連結（https://reurl.cc/4N01nL）
  * 測試連結功能正常

* [x] **重複檢查系統優化** ⭐ 核心功能修正
  * 修正地址100%相同但餐廳名稱不同時的重複檢查邏輯
  * 地址完全相同時強制判定為重複（無論名稱是否相似）
  * 提高相似度分數：名稱相似時95%，名稱不同時85%

* [x] **多平台推廣策略規劃** ⭐ 行銷策略
  * 制定 6 大推廣策略：LinkedIn、酒商合作、品酒社團、Facebook 社團、Instagram、Google 我的商家
  * 設計推廣效果追蹤指標
  * 建立成本效益評估框架
  * 準備各平台推廣素材

### 技術實現細節

**重複檢查系統優化：**
```php
// 如果地址完全相同，強制判定為重複（無論名稱是否相似）
if ($addr1_norm === $addr2_norm) {
    $name_similarity = byob_calculate_string_similarity($name1_norm, $name2_norm);
    if ($name_similarity >= 70) {
        return 95; // 地址相同且名稱相似，極高相似度
    } else {
        return 85; // 地址相同但名稱不同，仍然判定為重複
    }
}
```

**Facebook 抽獎活動貼文：**
```
🍷【BYOB 推薦餐廳抽獎活動】🍷

愛享用好酒的你，是否曾經為了找一家可以自帶酒水的餐廳而煩惱？

現在，只要分享推薦你喜歡的 BYOB 餐廳，就有機會獲得超值好禮！

🎁【獎品內容】
🏆 一獎（1名）：進口酒商電子禮券
🥈 二獎（2名）：高級進口紅白酒杯

🎯【參加方式超簡單】
1️⃣ 點擊下方連結推薦你喜歡的 BYOB 餐廳 
2️⃣ 填寫餐廳基本資訊（名稱、地址、開瓶費等）
3️⃣ 留下你的姓名和 Email
4️⃣ 一旦審核通過，立即獲得抽獎機會！

📝【推薦表單按這裡】
👉 https://forms.gle/jAnvmwh2BKyVXq5M8

📅 每月定期抽獎，中獎與否都會收到 Email 通知
💡 本抽獎使用 Mersenne Twister 演算法，確保結果隨機公正。

✨【額外抽獎機會】
審核通過的推薦人，分享活動貼文到你的社群媒體，並回覆 Email 附上分享連結，就能再獲得 1 次額外抽獎機會！
🔗 分享連結：https://reurl.cc/4N01nL

#BYOB #自帶酒水 #BYOB台北 #自帶酒水餐廳 #推薦抽好禮 #餐廳推薦 #抽獎活動 #紅酒 #白酒 #美食推薦 #台北美食 #新北美食
```

---

## ✅ 2025年10月6日 — 抽獎系統測試與優化

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

* [x] **發布機制優化** ⭐ 流程簡化
  * 改為審核通過即立刻發布
  * 與 WordPress 文章管理頁面發布機制一致
  * 觸發 `transition_post_status` hook
  * 自動發送推薦成功通知

---

## 📊 專案整體進度（截至 2025-10-08）

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

* ✅ **重複檢查系統**（完整實作 + 優化）
  * 相似度計算演算法
  * 自動觸發機制
  * 後台管理介面
  * 審核處理機制
  * 地址相同強制判定重複邏輯

* ✅ **抽獎系統**（完整實作）
  * 抽獎參與者記錄
  * 隨機抽獎執行
  * 中獎/未中獎通知
  * 後台管理介面
  * 動態月份選擇

* ✅ **多平台推廣策略**（執行中）
  * LinkedIn 專業版推廣
  * Instagram 推廣
  * 酒商合作 Email 準備
  * Facebook 社團調研準備

* ✅ **餐廳業者表單系統**（純淨版）
  * 動態欄位映射機制
  * 餐廳類型「其他」欄位處理
  * 開瓶費條件式邏輯

* ✅ **WordPress 架構優化**
  * 必填欄位簡化為 3 個核心欄位
  * 支援多種資料來源標記
  * ACF 欄位動態映射

### 進行中模組

* 🔄 酒商合作夥伴拓展與 Facebook 社團推廣（明日執行）

### 待開發模組

* ⏳ 自動回覆系統實作
* ⏳ KPI 追蹤儀表板
* ⏳ 行銷活動管理系統

---

## 📝 技術筆記

### 重複檢查系統架構（已完成 + 優化）

**功能架構：**
* 自動觸發：在 `byob_create_restaurant_article` 中檢查重複
* 相似度計算：名稱相似度 + 地址相似度 ÷ 2
* 閾值設定：≥ 80% 視為可能重複
* 狀態管理：重複設為 `pending`，不重複設為 `draft`

**核心函數：**
* `byob_check_duplicate_restaurant`：主檢查函數
* `byob_calculate_simple_similarity`：簡化版相似度計算
* `byob_calculate_string_similarity`：字串相似度計算
* `byob_extract_road_name`：路名提取
* `byob_extract_house_number`：門牌號碼提取

**相似度計算邏輯（優化版）：**
* 地址完全相同：強制判定為重複（85% 或 95%）
* 名稱完全相同：檢查地址相似度
* 綜合評分：名稱相似度 × 0.4 + 地址相似度 × 0.6
* 閾值：≥ 80% 視為可能重複

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

## 🎯 明日工作重點（10月9日）

### **主要任務：**

1. **酒商合作夥伴拓展**
   - 收集 15-20 個酒商聯絡方式
   - 發送 10-15 封合作邀請 Email
   - 建立酒商合作資料庫

2. **Facebook 品酒社團調研**
   - 確認 10 個品酒社團的發文規則
   - 評估社團適宜性
   - 在適合的社團發布推廣貼文

3. **效果追蹤與分析**
   - 記錄各平台互動數據
   - 追蹤 Email 回覆率
   - 設定後續跟進提醒

---

*最後更新：2025年10月8日*
*版本：v5.0*