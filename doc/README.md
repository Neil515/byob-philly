# 🍷 BYOB 專案總覽文檔

## 📋 專案概述

BYOB (Bring Your Own Bottle) 是一個自帶酒水餐廳推薦平台，目前運營兩個獨立專案：

### 🇹🇼 台北 BYOB（主要專案）
- **定位**：台北自帶酒水餐廳推薦平台
- **階段**：核心系統完成，推廣與酒商合作階段
- **策略**：抽獎激勵 + 多平台推廣

### 🇺🇸 費城 BYOB（新專案）
- **定位**：Yelp 的 BYOB 專業補充平台
- **階段**：地圖與定位功能完成，進行 Email 優化與餐廳業者連結功能
- **策略**：多平台資料收集 + 雙向表單驗證 + 榮譽系統驅動 + 餐廳聯絡機制

---

## 🚀 最新進度（2025年11月18日）

### 今日完成：資料整併、Token 批次、SendGrid 測試

**🎯 關鍵成就**

- ✅ **資料整併與爬蟲擴充**  
  - `update_1117_restaurants.py` 現可針對 11/17~18 餐廳一次取得官網、Yelp、Latitude/Longitude 與 Email，並支援 `.env` 管理 Google/Custom Search 金鑰。  
  - Email 擷取邏輯加入無效信箱過濾與常見聯絡頁搜尋，結果可自動寫入 `Email_1~n`。  
  - `merge_token_emails.py`（後續已移除）用於將 Excel 的 Email 欄位合併至 takeover token CSV，產生 `takeover_tokens_20251118_with_emails.csv`。

- ✅ **WP-CLI Token 批次產生**  
  - 建立 `wp-content/mu-plugins/byob-takeover-cli.php`，註冊 `wp byob-takeovers batch` 指令，可讀 JSON/CSV（如 `token_generating.json`）批次產生 takeover token、輸出 CSV、寄送單一 Summary Email。  
  - `philly_yelp_crawler/token_batch_memo.md` 紀錄 Cloudways SSH、路徑、指令與還原流程，方便重複操作。

- ✅ **SendGrid 測試與環境設定**  
  - `philly_yelp_crawler/sendgrid_test.py` 讀取 `takeover_tokens_20251118_copy.csv` 前兩筆，寄給 wavyclub21/slow3605 測試。  
  - 指導使用 `.env` 或系統環境設定 `SENDGRID_API_KEY`，並說明 HTTP 403 可能原因（Sender 未驗證或 API Key 權限不足）。

- ✅ **文件同步**  
  - `doc/Next Task Prompt Byob.md` 更新 11/19 三大任務：SendGrid 批次發信、餐廳 Logo 補齊、餐廳類型篩選。  
  - `doc/ai_progress_byob.md` 加入本日進度並精簡舊紀錄。

**🗓️ 明日（11/19）**
- ✉️ 使用 SendGrid 以 token CSV + Email 欄位批次寄信（含 log、dry-run、安全節流）。
- 🖼️ 整理/上傳餐廳 Logo，補齊 WordPress post meta 與前端顯示。
- 🏷️ 實作餐廳類型篩選（多選 UI + URL query + 後端 tax query）。

---


## 🏗️ 技術架構

### 台北專案技術棧
```
資料收集層
├── 顧客推薦表單 (Google Form + Apps Script)
├── 餐廳業者表單 (Google Form + Apps Script)
└── 酒商名單收集 (Python 爬蟲 + Email 提取器)
    ↓
WordPress 核心
├── REST API (/byob/v1/restaurant)
├── ACF Pro（自訂欄位管理）
├── 重複檢查系統
├── 審核管理系統
├── 抽獎系統
└── 推薦通知系統
    ↓
前端展示
├── 餐廳列表與篩選
└── 餐廳詳細頁面
```

### 費城專案技術架構
```
資料收集層
├── Yelp Fusion API 爬蟲
├── Google Places API 爬蟲
└── 智能去重系統
    ↓
表單驗證層
├── 網友推薦表單 + Apps Script
├── 老闆確認表單 + Apps Script
└── WordPress API 整合
    ↓
驗證與展示層
├── 驗證徽章系統 ✓
├── Yelp 連結整合 ✓
└── 前端格式統一 ✓
    ↓
地圖與定位系統 ✓
├── Google Maps JavaScript API 整合
├── HTML5 Geolocation 定位功能
├── 距離計算與排序邏輯
├── 自定義 SVG 圖標與 Attribution
└── 地圖標記互動（hover/click）
    ↓
聯絡與驗證機制（進行中）
├── 餐廳 Email 搜尋系統 ✓
├── Restaurant Access Transfer（接管流程）✓
├── Email 模板優化（11/18）
└── 餐廳業者連結功能（11/17）
    ↓
網站展示層
├── WordPress 程式碼英文化 ✓
├── 前台頁面英文化 ✓
├── 地圖與最近餐廳功能 ✓
├── FAQ 頁面英文化（準備中）
└── 後台介面英文化（準備中）
```

---

## 📊 專案進度概覽

### 🍷 台北 BYOB 專案
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

### 🍷 費城 BYOB 專案
- ✅ 資料收集：269 家候選餐廳（Yelp + Google Places）
- ✅ 雙表單系統：網友推薦 + 老闆確認表單
- ✅ 自動化整合：雙 Apps Script + WordPress API
- ✅ 驗證徽章系統：前台顯示與後台管理機制
- ✅ Yelp 連結整合：表單、後端、前台完整流程
- ✅ 餐廳 Email 搜尋系統：兩階段自動化工具
- ✅ 重複餐廳處理機制：標題加註、推薦次數欄位、相似度優化
- ✅ 網站前台英文化與評論功能清理
- ✅ 地圖與定位功能：Google Maps 整合、距離排序、最近餐廳列表
- ✅ 地圖標記圖標優化：自定義 SVG 圖標、Attribution 添加
- ✅ Restaurant Access Transfer：接管流程、通知信、後台 meta box
- 🔄 進行中：Email 模板設計、FAQ／後台英文化、資料夾整理
- ⏳ 後續階段：Reddit 回覆流程、資料衝突處理、網站上線、榮譽系統

---

## 📁 核心文檔

### 專案規劃文檔
- `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
- `doc/Next Task Prompt Byob.md`：工作規劃與任務追蹤
- `doc/ai_progress_byob.md`：開發進度記錄

### 技術文檔
- `philly_yelp_crawler/`：多平台爬蟲系統
  - `philly_email_searcher.py`：Email 搜尋步驟 1（取得 website）
  - `philly_email_extractor.py`：Email 搜尋步驟 2（搜尋 email）
  - `README.md`：Email 搜尋工具使用說明
- `wordpress/`：WordPress 整合檔案
  - `Apps script - 費城推薦版.js`：網友推薦表單處理
  - `Apps script - 費城餐廳確認版.js`：老闆確認表單處理
- `restaurant_crawler/`：台北專案爬蟲工具

---

## 🎯 下一步計畫

### 短期（本週）
1. **費城專案**：
   - ✅ 地圖與定位、圖標優化（11/14-15）
   - ✅ Restaurant Access Transfer（11/17）
   - ✅ Token 批次與資料整併（11/18）
   - 🚀 11/19：SendGrid 批次寄信、餐廳 Logo 補齊、類型篩選
   - ⏳ FAQ／後台英文化、Reddit 回覆流程

2. **台北專案**：
   - 🔄 酒商合作邀約、Facebook 社團推廣

### 中期（未來 1 個月）
1. **費城專案**：
   - 完成餐廳聯絡與驗證機制
   - 啟動 Reddit 社群互動
   - 英文網站上線
   - 招募創始成員

2. **台北專案**：
   - 建立酒商合作關係
   - 優化推廣策略

### 長期（未來 3-6 個月）
1. **費城專案**：
   - 實作榮譽系統和遊戲化功能
   - Wine Shop 合作分潤
   - 建立可持續商業模式

2. **多城市擴展**：
   - 評估其他城市的可行性
   - 建立可複製的擴展模式

---

## 💡 關鍵策略差異

### 台北模式 vs 費城模式

**台北模式：**
- 抽獎激勵、物質獎勵驅動、一次性參與

**費城模式升級：**
- 榮譽系統、專業認同驅動、長期持續參與
- 創始成員特殊身份、更低成本、更可持續

**調整原因：**
1. **成本考量**：海外專案初期無法負擔持續物質獎勵
2. **文化差異**：美國用戶更重視專業認同和社群地位
3. **可擴展性**：榮譽系統可無限擴展，物質獎勵不行
4. **長期價值**：建立專家社群而非獎品獵人

---

## 🚨 當前挑戰與解決方案

### 餐廳聯絡與驗證 ⚠️
- **挑戰**：餐廳 email 難以取得、餐廳老闆回覆率可能較低
- **進展**：✅ Email 搜尋系統（11/4）、Restaurant Access Transfer（11/17）
- **下一步**：11/18 Email 爬取與批次寄信、FAQ／後台英文化

---

## 🔑 重要技術突破

### 餐廳接管流程（2025/11/17）
- **Token 生成與管理**：後台 meta box 提供生成/查看/重設 token 的介面，token 32 字元、30 天有效。  
- **前端頁面**：`/takeover-restaurant?token=xxx` 使用 `template_redirect` 注入自訂頁面，UI 全英文並提供註冊/登入切換。  
- **權限移轉**：接管時確認該 email 是否已綁定其他餐廳，可選擇覆蓋既有業者，並同步更新 `_restaurant_owner_id` 與 `_owned_restaurant_id`。  
- **通知與導流**：成功接管後寄信給管理員並導向 `restaurant-profile`，登入流程自動完成。

### 地圖與定位系統（11/14-11/15）
- **Google Maps JavaScript API 整合**：實作互動式地圖，支援使用者定位與餐廳標記
- **多層級排序邏輯**：驗證狀態 > 資料完整度 > 餐廳照片 > 距離 > 收藏數 > 名稱
- **前端自動排序**：JavaScript 即時計算距離與排序權重，確保列表正確排列
- **響應式互動設計**：桌機版 hover 互動，行動版點擊 InfoWindow
- **自定義 SVG 圖標**：使用自定義圖標替代預設標記，優化視覺呈現
- **Attribution 管理**：添加圖標來源標註，符合授權要求
- **`.env` API key 管理**：在 `wp-config.php` 實作 `.env` 讀取，優先從環境變數讀取 API key

### 驗證徽章系統
- 視覺化展示驗證狀態，提升用戶信任度
- 管理員可手動覆蓋驗證狀態，保持管理彈性

### 資料來源自動辨識系統
- 雙表單系統透過 `source` 欄位自動標記
- 後台管理員清楚知道每筆資料來源

---

*最後更新：2025年11月18日*
*版本：v21.0*
*明日重點（11/19）：SendGrid 批次寄信、餐廳 Logo 補齊、餐廳類型篩選*
