# 🍷 BYOB 專案工作規劃與進度追蹤

## 2025-10-21 工作規劃（Reddit 發問訊息／互動與導流）

目標：在 r/philadelphia、美食與餐飲相關社群發起 BYOB 專案的發問貼文與互動回覆，獲得有效回覆（餐廳與政策資訊），並將回覆者導流到 Google 表單，觸發自動產生費城餐廳草稿。

重點里程碑（當日完成）
- 發問模板與回覆話術定稿（中英雙語，優先英文）
- 建立「Reddit 任務面板」Google Sheet（追蹤貼文連結、回覆數、導流表單數）
- 完成第一波發文（至少 3 則不同社群/時段）與 24 小時內回覆 SLA
- 新表單來源追蹤參數 utm_source=reddit、utm_campaign=philly_byob 啟用（Apps Script 直接寫入 `source` 與 `philly_reddit_username`）

執行清單（分工/技術）
1) 策略與內容
   - 發問主題版位與時段清單（子版：r/philadelphia、r/askphilly、r/FoodPhiladelphia 等）
   - 標題與內文模板（3 種角度）：
     - 專案介紹＋徵求 BYOB 推薦（提供表單連結）
     - 針對特定餐類/地區徵詢（如 Italian in Center City）
     - 問答型：費城 BYOB 的 corkage policy 與店家經驗分享
   - 回覆話術：感謝＋導流表單（可私訊提供更完整資訊）

2) 追蹤與資料
   - 在 Google 表單新增/確認隱藏欄位：`utm_source`、`utm_campaign`（預設由連結帶入）
   - Apps Script 解析：寫入 `source=reddit`、`philly_reddit_username`（若貼文或私訊提供）
   - 建立追蹤表（Sheet 欄位）：日期、子版、貼文連結、貼文類型、互動數、表單數、產生草稿數

3) 自動化與風險控管
   - 觸發器只保留單一 onFormSubmit，避免重複草稿
   - 去重規則：同名＋地址在 24 小時內重複則忽略或標記 pending_duplicate_review
   - 失敗備援：API 失敗時 Apps Script 寫入錯誤日誌並寄信通知

交付物
- Reddit 發問與回覆模板（Doc）
- 追蹤表（Google Sheet）
- 帶 UTM 的表單連結（短網址）
- 更新後 Apps Script（僅來源寫入，不改雙軌）

時程（估）
- AM：模板與清單定稿（1.5h）→ 追蹤表建立（0.5h）→ 連結與 UTM 測試（0.5h）
- PM：第一波發文與互動（1h）→ 監控與回覆（持續）→ 成效回填（0.5h）

成功指標（Day 1）
- 發文 ≥ 3 則、互動回覆 ≥ 10、有效表單 ≥ 3、產生草稿 ≥ 2

## 📅 當前日期：2025年10月30日

---

## 🗓️ 2025-10-31 工作規劃（兩大任務）

### 1) 設計「餐廳資料確認」Google 表單（Philly）
- 目的：請餐廳業者核對/補齊其在網站上的資料，並授權公開顯示。
- 表單內容（暫定）：
  - 基本：Restaurant name、Address（可編輯/建議）、Phone、Website
  - BYOB：Corkage Fee（Free/Corkage Fee/Other + note）、BYOB Service Level（4 選 1）、Wine service equipment（checkbox + other note）
  - 授權與聯絡：Contact email、是否同意公開（yes/no）、備註
- 技術要點：
  - 加上追蹤參數：`?utm_source=restaurant&utm_campaign=philly_byob_verification`
  - 建立欄位映射表（Sheet）→ Apps Script 解析 → WordPress API 寫入 ACF
  - 治理規則：
    - 單選題「未選擇」存空字串 '' 對應 ACF placeholder
    - 多選題未選擇存空陣列 []
    - 「Other」選項：若 note 有值則強制包含 'other' 並寫入 other_note（equipment/type）
  - 驗證流程：送單 → WP 生成/更新草稿 → 後台 ACF 檢查 → 前台檢視

### 2) 修復「餐廳列表頁」顯示問題
- 範圍：`wordpress/archive-restaurant.php`、查詢條件、分頁、ACF 欄位輸出、一致的英文顯示。
- 檢查清單：
  - 查詢是否僅取 `post_type=restaurant` 且正確狀態（publish/draft 過濾）
  - ACF 欄位輸出：地址、電話、Cuisine Type、BYOB 服務、設備，other 顯示一致
  - 排序與分頁：預設最新、可切換（如需）
  - 空值顯示：不顯示 placeholder「: -- 請選擇 --」，以空白或省略顯示
- 交付：
  - 修正過的 `archive-restaurant.php`，與必要的輔助函式
  - 範例截圖與驗證步驟

---

## 🧭 今日踩雷與解法（Google 表單 ↔ ACF）

1) 單選題加上 placeholder「: -- 請選擇 --」後，ACF 一律回退為 placeholder
- 根因：WP 端把表單「顯示文字」直接寫入 ACF；ACF 期望的是「值鍵」（key）。
- 解法：在 WP `functions.php` 寫入安全映射（就地 if/elseif），將顯示文字 → 值鍵：
  - `philly_corkage_fee`：Free → `free`、Corkage Fee → `corkage_fee`、Other → `other`、未選擇 → ''
  - `byob_service_level`：四個長句對應 `full_service`/`basic_service`/`self_service`/`no_service`、未選擇 → ''
  - `show_reddit_username`：以 Yes/No 前綴判斷，並規一撇號與空白；Yes → `yes`、No → `no`、未選擇 → ''

2) 「餐廳類型/酒器設備」的 other 與備註
- 根因：Apps Script/ACF 欄位鍵不一致，以及將中文「其他」存入導致條件顯示不觸發。
- 解法：
  - ACF 勾選鍵一律使用英文 `'other'`
  - 若有說明文字，確保陣列包含 `'other'`，並寫入對應 other_note
  - 前台顯示：把字串中的 `'other'` 替換為 `Other: [note]`（`single_restaurant.php` 已處理）

3) 欄位鍵名稱不一致
- 現象：設備其他說明顯示為鍵名 `philly_equipment_other_note`
- 解法：統一寫入/讀取 `equipment_other_note`（保留 philly 鍵做相容），Apps Script 跳過這兩鍵的直接映射，由設備解析邏輯自動生成

4) 函式重複宣告導致致命錯誤
- 現象：在同一請求重複宣告 mapping 函式導致 500
- 解法：改為就地 if/elseif 版本，完全移除函式/閉包宣告

5) 前台與後台資料不一致
- 現象：前台直接用原始字串顯示，後台 ACF 顯示未勾選
- 解法：前台也加入一致化的替換/顯示邏輯；後台改以值鍵寫入

變更檔案（重點）
- `wordpress/functions.php`：單選映射、安全寫入、設備/類型 other 規則、legacy 欄位同步
- `wordpress/single_restaurant.php`：other → `Other: note` 顯示邏輯
- `wordpress/Apps script - 費城推薦版.js`：設備/餐廳類型的 other 與 note 自動產生；跳過 other_note 的直寫

明日驗收標準（10/31）
- 完成餐廳資料確認表單（欄位、試算表、Apps Script、端到端測試）
- 列表頁顯示正確（含 other 顯示、空值處理、分頁/排序）
- 新測試提交不再出現 placeholder 回退問題

---

## 🚀 最近完成任務

**10月29日完成工作：** ⭐ Google 表單新欄位整合完成

* [x] **Google 表單新增欄位處理** ⭐ 系統整合完成
  * 在費城 BYOB 表單中新增「Reddit 用戶名顯示偏好」問題
  * 完成 Google Sheets「欄位設定表」映射設定
  * 修改 Google Apps Script 處理新欄位資料
  * 修改 WordPress functions.php API 端點接收新欄位
  * 修改 WordPress 資料保存邏輯，正確處理新欄位
  * 更新通知 Email 包含新欄位資訊

* [x] **ACF 欄位群組顯示問題修復** ⭐ 後台管理優化
  * 調整「Philly BYOB restaurant」和「餐廳欄位」群組的 ACF 位置規則
  * 解決費城餐廳同時顯示兩個欄位群組的問題
  * 確保費城餐廳只顯示「Philly BYOB restaurant」欄位群組

* [x] **ACF 欄位空值處理優化** ⭐ 資料品質提升
  * 為三個選填選擇題 ACF 欄位新增空白選項
  * 修改程式碼正確處理表單空值，避免自動選擇第一個選項
  * 確保跳過的選填問題在 ACF 中正確顯示為空白

---

**1月18日完成工作：** ⭐ Reddit 帳號建立與互動追蹤系統

* [x] **Reddit 帳號建立** ⭐ 社群準備完成
  * 成功註冊 Reddit 帳號：`u/findingBYOB`
  * 帳號名稱選擇：專業且友善，適合長期使用
  * 了解 Reddit 用戶名格式（u/ 前綴）
  * 準備個人檔案設定和頭像上傳

* [x] **Reddit 互動追蹤系統建立** ⭐ 管理工具完成
  * 建立完整的 Reddit 互動追蹤系統
  * 包含互動記錄、結果分析、用戶管理功能
  * 支援週度總結和策略測試追蹤
  * 預留 AI 整合和自動化功能

* [x] **工作規劃更新** ⭐ 明日任務明確
  * 更新 Next Task Prompt 檔案
  * 規劃 Reddit 互動、Google 表單、文章草稿工作
  * 清理早期過時資料，聚焦當前任務

**1月17日完成工作：** ⭐ 費城 BYOB 多平台爬蟲系統完成

* [x] **多平台爬蟲系統開發** ⭐ 技術架構完成
  * 建立 Yelp + Google Places + TripAdvisor 三平台整合爬蟲
  * 實作智能去重系統，保留所有來源資訊
  * 建立信心度評估系統（High/Medium/Low）
  * 整合主控程式，一鍵執行多平台爬取

* [x] **資料收集完成** ⭐ 269 家餐廳資料
  * **Yelp 資料**：191 家餐廳，來源正確標記
  * **Google Places 資料**：78 家餐廳，來源正確標記
  * **信心度分布**：
    - 高信心度：10 家（明確 BYOB 標示）
    - 中信心度：50 家（包含 wine/bottle 關鍵字）
    - 低信心度：209 家（需要進一步驗證）

---

## ✅ 1月19日完成工作：Google 表單建立

**1月20日完成工作：** ⭐ Reddit 社群互動系統建立完成

* [x] **Reddit 互動追蹤系統建立** ⭐ 管理工具完成
  * 建立完整的 Reddit 互動追蹤 Excel 檔案
  * 包含 7 個工作表：貼文總覽、用戶互動記錄、回覆詳細記錄、餐廳資訊收集、每日統計、週度分析、關鍵指標追蹤
  * 設計貼文記錄 Markdown 檔案，分為一般貼文和 BYOB 貼文兩類
  * 建立結構化的數據收集和分析系統

* [x] **Google Apps Script 開發完成** ⭐ 自動化整合完成
  * 完成費城專用 Google Apps Script 開發
  * 建立費城專用 WordPress API 端點
  * 實作自動化文章生成系統
  * 測試完整的表單提交到文章生成流程

* [x] **Reddit 貼文策略準備** ⭐ 社群互動準備就緒
  * 準備酒類文化討論貼文（建立信譽）
  * 準備 BBQ 餐廳詢問貼文（建立美食愛好者形象）
  * 準備 BYOB 餐廳詢問貼文（收集目標資訊）
  * 建立貼文內容記錄和追蹤系統

**1月19日完成工作：** ⭐ 費城 BYOB Google 表單建立完成

* [x] **Google 表單設計與建立** ⭐ 資料收集系統完成
  * 建立「Philadelphia BYOB Restaurant Verification Form」
  * 設計完整的表單結構（基本資訊、BYOB 政策、用餐體驗、貢獻者資訊）
  * 設定英文問題和選項，符合費城 BYOB 文化
  * 包含 10 個核心問題：餐廳名稱、地址、電話、網站、開瓶費、特殊政策、料理類型、用餐心得、Reddit 用戶名、聯絡 Email

* [x] **表單欄位優化** ⭐ 用戶體驗完成
  * 開瓶費選項：Free / Corkage Fee / Other
  * 料理類型：18 種常見類型（Italian, French, American, Asian 等）
  * 特殊政策說明：Special Corkage Policy
  * 設定必填欄位和選填欄位
  * 加入詳細的說明文字和使用者指引

* [x] **表單設定完成** ⭐ 發布準備就緒
  * 設定自動回覆訊息和 Email 通知
  * 啟用進度列和回應編輯功能
  * 測試表單功能和資料收集
  * 準備表單連結和推廣策略

---

<!-- 過時的 Reddit 發文「明日工作重點（10/30）」區塊已移除，改以 10/31 的兩大任務為主。 -->

---

## 📊 專案進度概覽更新

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
- 🔄 **Reddit 社群互動階段**：準備發布費城 BYOB 餐廳詢問貼文
- ⏳ **待執行**：手動內容英文化、英文網站上線、用戶招募、榮譽系統實作

### 🍷 台北 BYOB 專案（既有專案）
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

---

## 💡 明日工作提醒

### **Reddit 貼文發布重點**
- **社群友善**：避免過度推廣，保持真誠和有用的態度
- **內容品質**：確保貼文內容專業且符合 Reddit 文化
- **即時互動**：24 小時內回覆所有評論，建立良好印象
- **資料記錄**：完整記錄所有互動和收集到的資訊

### **風險控管**
- **避免 spam 嫌疑**：不要在短時間內在多個子版發布相同內容
- **尊重社群規則**：閱讀各子版規則，確保貼文符合規範
- **真誠互動**：避免只為了推廣，要真正參與社群討論
- **資料驗證**：記錄資訊來源，後續需要交叉驗證

### **進度追蹤**
- 使用 Reddit 互動追蹤 Excel 記錄所有互動
- 更新「貼文總覽」工作表（日期、子版、連結、類型、互動數）
- 更新「餐廳資訊收集」工作表（從回覆中提取的餐廳資料）
- 更新「每日統計」工作表（當天成效數據）
- 記錄學習洞察到「週度分析」工作表

### **參考資源**
- **貼文模板**：參考 `doc/philly_byob_complete_plan.md` 中的「主發文模板」
- **表單連結**：確保使用帶 UTM 參數的連結追蹤來源
- **追蹤工具**：`reddit_tracker/Reddit_Interaction_Tracker.xlsx`
- **帳號資訊**：Reddit 帳號 u/findingBYOB

---

## 📝 技術工具與資源

### **費城專案工具（已完成）**
- **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
- **智能去重系統**：保留來源資訊的資料整合
- **信心度評估**：High/Medium/Low 三級評分系統
- **Reddit 互動追蹤系統**：完整的管理和分析工具
- **Google 表單系統**：費城 BYOB 餐廳驗證表單

### **費城專案工具（已完成）**
- **費城專用 Google Apps Script**：處理費城表單提交
- **費城專用 WordPress API**：自動生成文章草稿
- **自動化文章生成系統**：英文內容模板和 SEO 優化
- **費城專用通知系統**：成功和錯誤通知機制
- **Reddit 互動追蹤系統**：Excel 檔案和 Markdown 記錄

### **費城專案工具（明日開發）**
- **手動內容英文化**：首頁、About Us、餐廳加入頁面等靜態內容
- **WordPress 後台英文化**：後台頁面、選單、設定等內容
- **SEO 優化系統**：英文關鍵字和內容優化
- **內容品質檢查**：英文內容一致性和本地化

### **台北專案工具（已完成）**
- **葡萄酒展參展商爬蟲**：酒商名單收集
- **Email 提取器**：聯絡資訊收集
- **抽獎系統**：推薦者激勵機制
- **重複檢查系統**：自動檢測重複餐廳

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

*最後更新：2025年1月20日*
*版本：v14.0*