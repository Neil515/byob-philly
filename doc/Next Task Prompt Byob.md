# 🍷 BYOB 專案工作規劃與進度追蹤

## 📅 當前日期：2025年10月30日

---

## ✅ 今日（2025-10-30）完成工作總結

### 1) 餐廳列表頁顯示邏輯修復完成 ⭐
- **移除 district 限制**：在 `restaurant-member-functions.php` 移除 `byob_is_restaurant_complete` 函數中的 `district` 必填檢查
  - 修改檔案：`wordpress/restaurant-member-functions.php`（2576-2591 行）
  - 影響：費城餐廳不再因缺少 `district` 欄位而被隱藏在列表頁外

- **修復 other 類型顯示問題**：在 `archive-restaurant.php` 增加英文 'other' 支援
  - 修改檔案：`wordpress/archive-restaurant.php`（餐廳類型：298, 313, 316 行；設備：455, 468, 470 行）
  - 影響：當餐廳類型或設備包含 `other` 且存在 `other_note` 時，正確顯示為 `Other: [description]` 而非僅顯示 `other`

### 2) Notes 欄位命名統一 ⭐
- **程式碼顯示標題修改**：
  - `wordpress/functions.php`：餐廳文章 "Dining Experience" → "Notes"（1111 行）
  - `wordpress/Apps script - 費城推薦版.js`：Email 通知 "Dining Experience" → "Notes"（377 行）
- **建議說明文字**："Anything you'd like other BYOB diners to know"

### 3) 需要手動完成的項目
- **Google 表單**：將欄位標題「Your Dining Experience」改為「Notes」
- **Google Sheets 欄位設定表**：將 `philly_dining_experience` 對應顯示名稱改為 "Notes"
- **ACF 欄位標籤**：將「用餐心得」改為「Notes」

---

## 🗓️ 明日（2025-10-31）工作規劃

### 1) 設計「餐廳資料確認」Google 表單（Philly）✨
- **目的**：請餐廳業者核對/補齊其在網站上的資料，並授權公開顯示。
- **表單內容（暫定）**：
  - 基本：Restaurant name、Address（可編輯/建議）、Phone、Website
  - BYOB：Corkage Fee（Free/Corkage Fee/Other + note）、BYOB Service Level（4 選 1）、Wine service equipment（checkbox + other note）
  - 授權與聯絡：Contact email、是否同意公開（yes/no）、備註
- **技術要點**：
  - 加上追蹤參數：`?utm_source=restaurant&utm_campaign=philly_byob_verification`
  - 建立欄位映射表（Sheet）→ Apps Script 解析 → WordPress API 寫入 ACF
  - 治理規則：
    - 單選題「未選擇」存空字串 '' 對應 ACF placeholder
    - 多選題未選擇存空陣列 []
    - 「Other」選項：若 note 有值則強制包含 'other' 並寫入 other_note（equipment/type）
  - 驗證流程：送單 → WP 生成/更新草稿 → 後台 ACF 檢查 → 前台檢視

---

## 🧭 重要技術學習與踩雷紀錄

### 1) 單選題加上 placeholder 後 ACF 回退問題
- **根因**：WP 端把表單「顯示文字」直接寫入 ACF；ACF 期望的是「值鍵」（key）
- **解法**：在 WP `functions.php` 寫入安全映射（就地 if/elseif），將顯示文字 → 值鍵
  - `philly_corkage_fee`：Free → `free`、Corkage Fee → `corkage_fee`、Other → `other`、未選擇 → ''
  - `byob_service_level`：四個長句對應 `full_service`/`basic_service`/`self_service`/`no_service`、未選擇 → ''
  - `show_reddit_username`：以 Yes/No 前綴判斷，並規一撇號與空白；Yes → `yes`、No → `no`、未選擇 → ''

### 2) 餐廳類型/酒器設備的 other 與備註
- **根因**：Apps Script/ACF 欄位鍵不一致，以及將中文「其他」存入導致條件顯示不觸發
- **解法**：
  - ACF 勾選鍵一律使用英文 `'other'`
  - 若有說明文字，確保陣列包含 `'other'`，並寫入對應 other_note
  - 前台顯示：把字串中的 `'other'` 替換為 `Other: [note]`（`archive-restaurant.php` 和 `single_restaurant.php` 已處理）

### 3) 欄位鍵名稱不一致
- **現象**：設備其他說明顯示為鍵名 `philly_equipment_other_note`
- **解法**：統一寫入/讀取 `equipment_other_note`（保留 philly 鍵做相容），Apps Script 跳過這兩鍵的直接映射，由設備解析邏輯自動生成

### 4) 函式重複宣告導致致命錯誤
- **現象**：在同一請求重複宣告 mapping 函式導致 500
- **解法**：改為就地 if/elseif 版本，完全移除函式/閉包宣告

### 5) 前台與後台資料不一致
- **現象**：前台直接用原始字串顯示，後台 ACF 顯示未勾選
- **解法**：前台也加入一致化的替換/顯示邏輯；後台改以值鍵寫入

---

## 📊 專案進度概覽

### 🍷 費城 BYOB 專案（進行中）
- ✅ **資料收集完成**：269 家候選餐廳（Yelp + Google Places）
- ✅ **Reddit 帳號建立**：u/findingBYOB 準備就緒
- ✅ **互動追蹤系統**：完整的管理工具建立
- ✅ **Google 表單建立**：費城 BYOB 餐廳驗證表單完成
- ✅ **自動化整合完成**：Google Apps Script + WordPress API 整合
- ✅ **Reddit 貼文策略準備**：社群互動內容和追蹤系統完成
- ✅ **WordPress 程式碼英文化完成**：所有 PHP 檔案前台顯示文字已改為英文
- ✅ **Google 表單新欄位整合完成**：Reddit 用戶名顯示偏好欄位已整合
- ✅ **ACF 欄位群組顯示問題修復**：費城餐廳只顯示專用欄位群組
- ✅ **ACF 欄位空值處理優化**：選填選擇題正確處理空值
- ✅ **餐廳列表頁顯示邏輯修復**：移除 district 限制、支援 other 顯示
- ✅ **Notes 欄位命名統一**：程式碼已完成修改
- 🔄 **Reddit 社群互動階段**：準備發布費城 BYOB 餐廳詢問貼文
- ⏳ **待執行**：餐廳資料確認表單、手動內容英文化、英文網站上線、用戶招募、榮譽系統實作

### 🍷 台北 BYOB 專案（既有專案）
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

---

## 🔍 參考文檔

### **費城專案文檔**
* `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
* `philly_yelp_crawler/data/combined_byob_restaurants.csv`：269 家候選餐廳資料
* `philly_yelp_crawler/data/high_confidence_byob_restaurants.csv`：10 家高信心度餐廳
* `philly_yelp_crawler/data/crawl_report.json`：詳細爬取統計報告

### **台北專案文檔**
* `doc/ai_progress_byob.md`：台北專案開發進度記錄
* `doc/lottery_activity_planning.md`：抽獎活動規劃
* `doc/message_and_form/`：Email 通知模板

---

## 🚨 當前挑戰與風險

### **Reddit 社群接受度**
- **風險**：新帳號可能被視為推廣或 spam
- **緩解**：先建立信譽，提供有價值的建議
- **備案**：準備多個社群平台互動

### **資料品質控制**
- **風險**：Reddit 回覆可能包含錯誤資訊
- **緩解**：交叉驗證多個回覆，記錄資訊來源
- **備案**：保留原始爬蟲資料作為備份

### **內容創作挑戰**
- **風險**：缺乏實際用餐經驗
- **緩解**：基於爬蟲資料和社群回饋
- **備案**：標明資料來源，邀請用戶補充

---

## 📝 技術工具與資源

### **費城專案工具（已完成）**
- **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
- **智能去重系統**：保留來源資訊的資料整合
- **信心度評估**：High/Medium/Low 三級評分系統
- **Reddit 互動追蹤系統**：完整的管理和分析工具
- **Google 表單系統**：費城 BYOB 餐廳驗證表單
- **費城專用 Google Apps Script**：處理費城表單提交
- **費城專用 WordPress API**：自動生成文章草稿
- **自動化文章生成系統**：英文內容模板和 SEO 優化
- **費城專用通知系統**：成功和錯誤通知機制

### **台北專案工具（已完成）**
- **葡萄酒展參展商爬蟲**：酒商名單收集
- **Email 提取器**：聯絡資訊收集
- **抽獎系統**：推薦者激勵機制
- **重複檢查系統**：自動檢測重複餐廳

---

*最後更新：2025年10月30日*
*版本：v15.0*
