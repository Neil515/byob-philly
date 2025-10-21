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

## 📅 當前日期：2025年1月20日

---

## 🚀 最近完成任務

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

## 🔴 明日工作重點（1月21日）→ WordPress 英文介面改造

### 🎯 核心目標
將 WordPress 網站的中文介面逐步改為英文介面，為費城 BYOB 專案建立專業的英文網站

**預估總時間：** 6-8 小時

---

## 📝 工作三大重點

### 🚨 第一部分：網站結構英文化（3-4 小時）

#### **任務 1.1：頁面標題和導航英文化（90 分鐘）**

* [ ] **首頁英文化**
  * 修改網站標題：`Philadelphia BYOB Restaurant Guide`
  * 更新網站描述：`Discover the best BYOB restaurants in Philadelphia`
  * 修改導航選單：Home, Restaurants, About, Contact
  * 更新頁腳資訊和版權聲明

* [ ] **餐廳列表頁面英文化**
  * 修改頁面標題：`Philadelphia BYOB Restaurants`
  * 更新篩選選項：Cuisine Type, Neighborhood, Corkage Fee
  * 修改排序選項：Name, Rating, Date Added
  * 更新搜尋框提示文字

* [ ] **單一餐廳頁面英文化**
  * 修改頁面標題格式：`[Restaurant Name] - BYOB Restaurant Guide`
  * 更新欄位標籤：Restaurant Name, Address, Phone, Website
  * 修改 BYOB 政策區塊：Corkage Fee, Special Policies
  * 更新用餐體驗區塊：Atmosphere, Signature Dishes, Service

#### **任務 1.2：表單和互動元素英文化（90 分鐘）**

* [ ] **餐廳推薦表單英文化**
  * 修改表單標題：`Recommend a BYOB Restaurant`
  * 更新表單欄位標籤和提示文字
  * 修改提交按鈕：`Submit Recommendation`
  * 更新成功/錯誤訊息

* [ ] **搜尋和篩選功能英文化**
  * 修改搜尋框：`Search BYOB restaurants...`
  * 更新篩選標籤：`Filter by Cuisine`, `Filter by Area`
  * 修改排序選項：`Sort by Name`, `Sort by Rating`
  * 更新「無結果」訊息

* [ ] **用戶互動元素英文化**
  * 修改評論區塊：`Reviews`, `Write a Review`
  * 更新評分系統：`Rate this restaurant`
  * 修改分享按鈕：`Share`, `Bookmark`
  * 更新聯絡表單和回饋系統

#### **任務 1.3：內容和文案英文化（60 分鐘）**

* [ ] **靜態內容英文化**
  * 修改關於我們頁面：`About Philadelphia BYOB Guide`
  * 更新使用說明：`How to Use This Guide`
  * 修改隱私政策：`Privacy Policy`
  * 更新服務條款：`Terms of Service`

* [ ] **動態內容英文化**
  * 修改文章分類：`Italian BYOB`, `French BYOB`, `Asian BYOB`
  * 更新標籤系統：`Center City`, `Rittenhouse`, `Old City`
  * 修改相關文章推薦：`Related Restaurants`
  * 更新最新文章區塊：`Latest Reviews`

---

### 🚨 第二部分：資料庫和後台英文化（2-3 小時）

#### **任務 2.1：ACF 欄位英文化（90 分鐘）**

* [ ] **餐廳基本資訊欄位英文化**
  * `restaurant_name` → `Restaurant Name`
  * `restaurant_address` → `Address`
  * `restaurant_phone` → `Phone Number`
  * `restaurant_website` → `Website`
  * `restaurant_cuisine` → `Cuisine Type`

* [ ] **BYOB 政策欄位英文化**
  * `corkage_fee` → `Corkage Fee`
  * `corkage_policy` → `Corkage Policy`
  * `byob_equipment` → `BYOB Equipment`
  * `special_policies` → `Special Policies`
  * `wine_recommendations` → `Wine Recommendations`

* [ ] **用餐體驗欄位英文化**
  * `atmosphere` → `Atmosphere`
  * `signature_dishes` → `Signature Dishes`
  * `service_quality` → `Service Quality`
  * `dining_experience` → `Dining Experience`
  * `best_times_to_visit` → `Best Times to Visit`

#### **任務 2.2：後台管理介面英文化（90 分鐘）**

* [ ] **WordPress 後台英文化**
  * 修改餐廳管理頁面標題
  * 更新欄位標籤和說明文字
  * 修改分類和標籤管理介面
  * 更新媒體庫和檔案管理

* [ ] **自訂欄位群組英文化**
  * 修改 ACF 欄位群組名稱
  * 更新欄位說明和提示文字
  * 修改選項值和預設文字
  * 更新條件邏輯和驗證規則

* [ ] **管理員通知和訊息英文化**
  * 修改成功/錯誤通知訊息
  * 更新 Email 通知模板
  * 修改系統狀態訊息
  * 更新除錯和日誌訊息

---

### 🚨 第三部分：SEO 和內容優化（1-2 小時）

#### **任務 3.1：SEO 設定英文化（60 分鐘）**

* [ ] **頁面 SEO 設定**
  * 修改頁面標題（Title Tags）
  * 更新頁面描述（Meta Descriptions）
  * 修改關鍵字設定
  * 更新 Open Graph 標籤

* [ ] **內容 SEO 優化**
  * 修改標題結構（H1, H2, H3）
  * 更新圖片 Alt 文字
  * 修改內部連結文字
  * 更新麵包屑導航

* [ ] **技術 SEO 設定**
  * 修改 sitemap 設定
  * 更新 robots.txt
  * 修改 URL 結構
  * 更新快取和效能設定

#### **任務 3.2：內容品質檢查（60 分鐘）**

* [ ] **內容一致性檢查**
  * 檢查所有頁面的英文內容
  * 確認術語使用一致性
  * 檢查語法和拼字錯誤
  * 更新過時或錯誤的資訊

* [ ] **用戶體驗測試**
  * 測試英文介面的易用性
  * 檢查響應式設計
  * 測試載入速度和效能
  * 確認跨瀏覽器相容性

* [ ] **內容本地化**
  * 確認費城地區資訊正確
  * 更新聯絡資訊和地址
  * 修改時區和日期格式
  * 更新貨幣和單位顯示

---

## 🎯 明日成功標準（1月21日）

* [ ] ✅ 完成網站主要頁面英文化
* [ ] ✅ 更新所有表單和互動元素
* [ ] ✅ 完成 ACF 欄位和後台英文化
* [ ] ✅ 優化 SEO 設定和內容
* [ ] ✅ 完成內容品質檢查和測試
* [ ] ✅ 準備英文網站上線

---

## 📊 專案進度概覽更新

### 🍷 費城 BYOB 專案（進行中）
- ✅ **資料收集完成**：269 家候選餐廳（Yelp + Google Places）
- ✅ **Reddit 帳號建立**：u/findingBYOB 準備就緒
- ✅ **互動追蹤系統**：完整的管理工具建立
- ✅ **Google 表單建立**：費城 BYOB 餐廳驗證表單完成
- ✅ **自動化整合完成**：Google Apps Script + WordPress API 整合
- ✅ **Reddit 貼文策略準備**：社群互動內容和追蹤系統完成
- 🔄 **網站英文化階段**：WordPress 介面逐步改為英文
- ⏳ **待執行**：Reddit 社群互動、英文網站上線、用戶招募、榮譽系統實作

### 🍷 台北 BYOB 專案（既有專案）
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

---

## 💡 明日工作提醒

### **WordPress 英文化重點**
- **介面一致性**：確保所有頁面和元素使用一致的英文術語
- **用戶體驗**：保持英文介面的直觀性和易用性
- **內容品質**：檢查語法、拼字和術語使用
- **本地化**：確保費城地區資訊和格式正確

### **SEO 優化重點**
- **關鍵字策略**：使用費城 BYOB 相關的英文關鍵字
- **內容結構**：優化標題結構和內容層次
- **技術 SEO**：確保網站技術設定符合英文網站標準
- **本地 SEO**：優化費城地區的搜尋可見性

### **進度追蹤**
- 每完成一個任務就更新此檔案
- 記錄 WordPress 英文化進度
- 追蹤 ACF 欄位更新狀態
- 監控 SEO 優化效果
- 為 Reddit 社群互動階段做準備

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
- **英文網站介面**：WordPress 前台和後台英文化
- **ACF 欄位英文化**：所有自訂欄位和標籤
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
*版本：v12.0*