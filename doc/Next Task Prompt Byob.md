# 🍷 BYOB 專案工作規劃與進度追蹤

## 📅 當前日期：2025年10月14日

---

## 🚀 最近完成任務

**10月14日完成工作：** ⭐ 費城 BYOB 爬蟲策略調整

* [x] **Yelp 爬蟲策略重新評估** ⭐ 技術方案調整
  * 分析 Yelp 403 封鎖問題（User-Agent 偵測、請求頻率限制）
  * 評估多種解決方案：Selenium、代理 IP、Yelp Fusion API
  * 確定最佳方案：Yelp Fusion API（穩定、免費、官方支援）

* [x] **專案檔案清理** ⭐ 程式碼管理
  * 刪除手動清單程式（不符合自動化需求）
  * 清理不必要的爬蟲檔案
  * 更新 requirements.txt 支援 API 方案

* [x] **Yelp API 申請準備** ⭐ 帳號註冊
  * 研究 Yelp Fusion API 申請流程
  * 遇到 hCaptcha 重複驗證問題
  * 提供多種解決方案（清除瀏覽器、無痕模式、VPN 等）

**10月13日完成工作：** ⭐ 費城 BYOB 專案規劃完成

* [x] **費城 BYOB 專案可行性分析** ⭐ 市場調研
  * 分析費城 BYOB 市場特性和機會
  * 確認與 Yelp 的互補定位策略
  * 評估技術複製可行性

* [x] **AD 方案完整規劃** ⭐ 策略制定
  * 確定資料收集策略：Yelp 抓取 + Reddit 驗證
  * 設計交叉驗證流程和信心度評級系統
  * 規劃 4 週詳細時程（資料收集、驗證、建站）
  * 制定風險管控和成功指標

* [x] **榮譽系統與遊戲化設計** ⭐ 用戶激勵機制
  * 設計 5 級榮譽系統（Explorer → Legend）
  * 規劃費城 12 區域攻佔機制
  * 設計 24 個專業領域徽章
  * 建立創始成員特權方案

* [x] **專案文檔建立** ⭐ 文檔管理
  * 建立完整專案計畫文檔（`philly_byob_complete_plan.md`）
  * 整合技術、策略、時程於一份文檔
  * 清理和整理專案檔案結構

---

## 🔴 明日工作重點（10月15日）→ Yelp API 申請與爬蟲開發

### 🚨 優先級 1：完成 Yelp Fusion API 申請

**目標：**
解決 hCaptcha 問題，成功申請 Yelp Fusion API Key

**預估時間：** 1-2 小時

---

#### **任務 1.1：解決 Yelp 帳號註冊問題（60 分鐘）**

* [ ] **嘗試解決 hCaptcha 重複問題**
  * 方法 1：清除瀏覽器 Cookie 和快取（Ctrl + Shift + Delete）
  * 方法 2：使用無痕模式重新註冊（Ctrl + Shift + N）
  * 方法 3：更換瀏覽器（Chrome → Firefox 或 Edge）
  * 方法 4：使用 VPN 或手機熱點更換 IP

* [ ] **完成 Yelp 帳號註冊**
  * 提供真實 Email 和個人資訊
  * 仔細完成 hCaptcha 驗證（選擇所有相關圖片）
  * 等待足夠時間，確保網路穩定

* [ ] **創建 Yelp 開發者應用**
  * App Name：`Philly BYOB Crawler`
  * Industry：`Food & Dining`
  * Description：`Collecting BYOB restaurant data for Philadelphia food guide`
  * 同意服務條款

* [ ] **獲取並保存 API Key**
  * 複製 Client ID 和 API Key
  * 建立 `.env` 檔案儲存 API Key
  * 確認 API 限制：每日 500 次請求

---

#### **任務 1.2：開發 Yelp API 爬蟲（90 分鐘）**

* [ ] **建立 API 爬蟲架構**
  * 建立 `yelp_api_crawler.py` 主程式
  * 設定 API 認證和請求處理
  * 實作多關鍵字搜尋功能：
    * "BYOB Philadelphia"
    * "bring your own wine Philadelphia"
    * "bring your own bottle Philadelphia"
    * "corkage fee Philadelphia"
    * "BYO Philadelphia"
    * "BYOB restaurants Philadelphia"
    * "bring wine Philadelphia"
    * "corkage Philadelphia"

* [ ] **資料處理和去重**
  * 實作餐廳名稱和地址比對去重
  * 建立信心度評估邏輯（基於關鍵字匹配）
  * 輸出為 CSV 和 JSON 格式

* [ ] **測試和驗證**
  * 執行小規模測試（1-2 個關鍵字）
  * 檢查資料品質和格式
  * 確認 API 使用量在限制內

**技術規格：**
```
✅ 使用官方 Yelp Fusion API
✅ 每日 500 次請求限制（足夠 150 家餐廳）
✅ 結構化 JSON 資料，易於處理
✅ 包含餐廳名稱、地址、電話、評分、評論數
❌ 不抓取評論內容（避免額外 API 消耗）
```

---

### 🚨 優先級 2：Reddit 帳號建立與社群準備

**目標：**
建立 Reddit 帳號並準備社群驗證策略

**預估時間：** 1.5-2 小時

---

#### **任務 2.1：Reddit 帳號設定（30 分鐘）**

* [ ] **建立 Reddit 帳號**
  * 註冊專業的 Reddit 帳號
  * 建議用戶名：`PhillyBYOBGuide` 或類似
  * 設定個人檔案：
    * 簡介：開發費城 BYOB 餐廳指南
    * 連結到未來的網站（待建立）

* [ ] **了解社群規則**
  * 閱讀 r/philadelphia 的發文規則
  * 確認是否需要最低 Karma 才能發文
  * 了解社群文化和語氣

* [ ] **建立信譽（如需要）**
  * 如果需要 Karma，先在相關討論串回覆
  * 提供有價值的 BYOB 相關建議
  * 避免立刻發推廣文（可能被視為 spam）

---

#### **任務 2.2：Reddit 發文內容擬定（60 分鐘）**

* [ ] **主發文撰寫**
  * 標題設計（吸引注意又不過度推銷）
  * 正文內容撰寫（參考 `philly_byob_complete_plan.md` 的模板）
  * 調整語氣為真誠、社群導向
  * 強調互惠價值和創始成員身份

* [ ] **發文策略規劃**
  * 決定最佳發文時間（美國東岸時區）
  * 準備回覆常見問題的答案
  * 設計追蹤回覆的策略（快速回應每個貢獻者）

* [ ] **備用平台準備**
  * 調查 r/PhillyFood 的發文規則
  * 準備 Facebook "Philadelphia Foodies" 群組版本
  * 規劃 LinkedIn 專業版本（如適用）

---

#### **任務 2.3：Reddit 發文內容草稿**

**建議草稿框架：**

```markdown
標題選項：
1. "Help me build the ultimate BYOB restaurant guide for Philly!"
2. "Building Philadelphia's first comprehensive BYOB restaurant resource - need your expertise!"
3. "Calling all Philly BYOB lovers - help create something amazing for our city"

正文結構：
━━━━━━━━━━━━━━━━━━
🍷 開場白
• 自我介紹（開發者）
• 說明專案目的（費城 BYOB 文化值得更好的資源）

🎯 專案差異化
• 與 Yelp 的互補關係（專精 vs 通用）
• 詳細的開瓶費政策和 BYOB 資訊
• 專業社群而非匿名評論

📝 需要的幫助
• 推薦你喜愛的 BYOB 餐廳
• 分享開瓶費和政策細節
• 提供 BYOB 經驗和建議

🏆 回饋機制
• 創始成員身份
• 榮譽系統和專家認證（預告）
• 幫助塑造費城 BYOB 文化

💬 行動呼籲
• 在留言區分享推薦
• 歡迎私訊深度討論
━━━━━━━━━━━━━━━━━━
```

* [ ] **根據框架撰寫完整發文**
* [ ] **準備 3-5 個回覆模板**（感謝、追問細節、邀請深度參與）
* [ ] **設計用戶資訊收集表格**（私訊或 Google Form）

---

### 🚨 優先級 3：資料驗證準備

**目標：**
建立資料交叉驗證的準備工作

**預估時間：** 30 分鐘

---

* [ ] **建立資料比對邏輯**
  * 設計 Yelp 資料與 Reddit 推薦的比對方法
  * 準備資料表格（Excel 或 Google Sheets）
  * 設計信心度評分系統

* [ ] **準備驗證工作流程**
  * 規劃如何追蹤每家餐廳的驗證狀態
  * 設計「待驗證」、「已確認」、「有爭議」的標記系統
  * 準備官網確認的檢查清單

---

## 🎯 明日成功標準（10月15日）

* [ ] ✅ 成功解決 hCaptcha 問題並完成 Yelp 帳號註冊
* [ ] ✅ 獲取 Yelp Fusion API Key 並建立 `.env` 檔案
* [ ] ✅ 完成 Yelp API 爬蟲開發並測試成功
* [ ] ✅ 收集到 100+ 家費城 BYOB 餐廳候選名單
* [ ] ✅ 建立 Reddit 帳號並了解社群規則

---

## 📊 專案進度概覽

### 🍷 費城 BYOB 專案（新專案）
- ✅ **專案規劃完成**：AD 方案、榮譽系統、遊戲化設計
- 🔄 **第一階段啟動**：Yelp 資料收集 + Reddit 驗證
- ⏳ **待執行**：網站建設、用戶招募、內容建立

### 🍷 台北 BYOB 專案（既有專案）
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

---

## 📝 技術工具與資源

### **費城專案工具（開發中）**
- **Yelp Fusion API 爬蟲**：費城 BYOB 餐廳資料收集（使用官方 API）
- **資料驗證系統**：交叉比對和信心度評估
- **WordPress 英文版**：複製台北架構，英文化

### **台北專案工具（已完成）**
- **葡萄酒展參展商爬蟲**：酒商名單收集
- **Email 提取器**：聯絡資訊收集
- **抽獎系統**：推薦者激勵機制
- **重複檢查系統**：自動檢測重複餐廳

---

## 🔍 參考文檔

### **費城專案文檔**
* `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
  * AD 方案詳細執行規劃
  * 榮譽系統與遊戲化設計
  * 4 週時程和成功指標

### **台北專案文檔**
* `doc/ai_progress_byob.md`：台北專案開發進度記錄
* `doc/lottery_activity_planning.md`：抽獎活動規劃
* `doc/message_and_form/`：Email 通知模板

---

## 💡 明日工作提醒

### **時區注意事項**
- 美國東岸時間與台灣時差：-12 小時（冬令時間）或 -13 小時（夏令時間）
- Reddit 發文最佳時間（美國東岸）：
  * 上午 9-11 AM（台灣晚上 9-11 PM）
  * 下午 6-8 PM（台灣早上 6-8 AM）
  * 建議準備好草稿，選擇適當時機發布

### **進度追蹤**
- 每完成一個任務就更新此檔案
- 記錄遇到的問題和解決方案
- 為後續工作做筆記和改進建議

---

---

## 🚨 當前阻塞問題

### **Yelp 帳號註冊 hCaptcha 問題**
- **問題描述**：hCaptcha 驗證完成後重複跳出，無法完成註冊
- **影響**：無法申請 Yelp Fusion API Key，阻礙爬蟲開發
- **嘗試解決方案**：
  - 清除瀏覽器資料
  - 使用無痕模式
  - 更換瀏覽器
  - 使用 VPN 更換 IP
- **明日優先處理**：繼續嘗試解決 hCaptcha 問題

---

*最後更新：2025年10月14日*
*版本：v7.0*
