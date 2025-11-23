# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-11-23）

### **台北 BYOB**（核心系統維運）
- 核心功能與自動化流程已完整，持續推廣、酒商合作與 Email 模板優化。

### **費城 BYOB**（主要開發重心）
- 前台英文化完成，持續拓展資料源、餐廳接管、自動寄信與社群推廣。
- 技術堆疊：WordPress + WooCommerce + ACF + WP-CLI + Python 資料腳本 + SendGrid。


## ✅ 2025年11月23日 — 第二封 Email A/B 與社群行事曆

### 🎯 今日成就總覽
- **SendGrid 第二封排程準備到位**
  - 將 `philly_yelp_crawler/mail_11.30.json` 拆成 `mail_11.30_template2.json`、`mail_11.30_template3.json`，並加入兩筆模擬餐廳（wavyclub21 測試）以便隨時驗證模板。
  - 產出單獨測試檔（Mabu / Yuhiro）並以模板 2、模板 3 寄出，即時調整 SendGrid 動態欄位與模板錯誤（Template Render Fail）問題。
  - 新增 `doc/sendgrid_followup_ab_test.md`，將 72 小時限制、PowerShell 指令、測試/驗證步驟寫成操作手冊，未來只需等到 11/30 22:45（台北）前 72 小時再執行即可。
- **社群節奏文件化**
  - 依照今日確立的時間/成本模型，編寫 `doc/social_calendar.md`，把 Reddit 每週 1 貼 + FB 每週 2 貼的主題輪替、CTA 連結與貼文語氣整理成白話清單。
  - `doc/Next Task Prompt Byob.md` 更新至 11/23，並將 11/24 的兩項重點（FB 週一 Spotlight + Reddit 週五草稿）寫入，附上 Little Fish 草稿以供複製。

### 🔧 主要修改檔案
- `philly_yelp_crawler/mail_11.30.json`：新增測試餐廳、校正 personalizations。
- `philly_yelp_crawler/mail_11.30_template2.json` / `mail_11.30_template3.json` / 測試 JSON：分拆 A/B 名單、設定 send_at。
- `doc/sendgrid_followup_ab_test.md`：新增 SendGrid 排程操作指引。
- `doc/social_calendar.md`：新增 Reddit + FB 行事曆。
- `doc/Next Task Prompt Byob.md`：更新 11/24 工作內容與貼文草稿.

### 📌 備註 / 重要決策
- SendGrid `/mail/send` 只能在 72 小時內排程；操作手冊已提醒需等到 11/27 晚上後再送出正式批次。
- 模板 2、3 綁定的 Handlebars 需維持 `{{restaurant_name}}`、`{{listing_url}}`、`{{takeover_link}}`；若再度異動，記得用 `Test Your Email` 驗證。
- 社群節奏以 60 分鐘/週維運：Reddit 週末長帖 + FB 週一 Spotlight/週五任務；所有 CTA 統一導向推薦表單與 takeover。

---

## ✅ 2025年11月22日 — SendGrid 稽核與社群節奏

### 🎯 今日成就總覽
- **SendGrid 寄送稽核 SOP 完成**
  - 梳理 11/19 兩批餐廳 Email 的追蹤流程，於說明中示範 Activity 搜尋（依 `batch_id`、日期範圍、主旨）與 `POST /v3/messages/search` API 查詢，確保可快速核對送達、退信與開信事件。
  - 盤點 `philly_yelp_crawler/testmail.json`：確認 31 家餐廳、8 家擁有多組 email、總計 40 個收件地址，作為報表與後續跟進的基準資料。
- **社群輕量經營藍圖落地**
  - 建立 `social_channel_playbook.md`，定義 Reddit 每週單一主題貼文 + Facebook 私密社團週貼與日常互動的 60 分鐘節奏，並提供 CTA 樣板、素材共享與 KPI 表格（推薦數 / 接管數 / Email 回覆數）。
  - 針對 Facebook 社團確立 `Philly BYOB Club` 作為官方陣地，Reddit 則輪替收集串、討論串、地圖進度、回顧串，降低經營成本但維持導流。
- **工作排程同步**
  - `Next Task Prompt Byob.md` 更新至 2025-11-22，新增 11/23 的兩項重點：第二封餐廳 Email 排程、Reddit + Facebook 貼文規律與固定內容設定，並刪除舊的 11/19～11/21 任務。

### 🔧 主要修改檔案
- `doc/Next Task Prompt Byob.md`：重寫今日摘要與 11/23 待辦。
- `philly_yelp_crawler/social_channel_playbook.md`：新增社群節奏、貼文模板與 KPI 追蹤表。

### 📌 備註 / 重要決策
- SendGrid 稽核需保留 `batch_id` 與寄送 JSON，後續按照 Activity 或 API 查詢即可掌握未回信業者清單，作為第二封 Email 排程基礎。
- Reddit/Facebook 雙軌以「固定模板 + 集中時段」運作，可在一週內用 60 分鐘完成所有貼文與互動；所有 CTA 導向推薦表單與 takeover link，確保三大 KPI（推薦、接管、Email 回覆）同步提升。
- 社群素材（成功案例、Email 指標、待回覆名單）集中於共享資料夾，貼文時僅需複用資料即可加速產出。

---

## ✅ 2025年11月20日 — 單頁官網顯示與類型排序權重

### 🎯 今日成就總覽
- **單一餐廳頁連結體驗升級**
  - Yelp 區塊下方新增「Website / Social」欄位，若同時有官網與多個社群連結會依序顯示並自動加入 `View Website`、`Social Profile` 連結。
  - 新增社群欄位解析器，支援單一 URL、逗號/換行清單與 ACF Link/Reapter 格式；無資料時整段隱藏。
  - Link 區塊統一套用淺藍色樣式與 hover 效果，與頁面底部 CTA 風格一致。
- **ACF 欄位與資料流同步**
  - 新增 `social_links` ACF URL 欄位後，後台表單與 `restaurant-member-functions.php` 原邏輯可直接使用；僅需在 API 匯入時對社群 URL 做 `esc_url_raw()` 清洗。
  - 確認 `Next Task Prompt Byob`、相關表單不需額外改動即可支援新欄位格式。
- **餐廳類型篩選排序與完整性**
  - `byob_get_all_restaurant_type_terms()` 會計算每個類型在現有文章中的出現次數，並依 `count DESC + label ASC` 排序；UI 仍將已選類型優先顯示。
  - 新增固定展示的預設類型清單（包含 Steakhouse、Vegetarian/Vegan、Indian、Spanish 等），即使目前零筆餐廳也會顯示篩選 pill。
  - 以 transient 快取結果，並提供臨時清除 hook 便於驗證（已於作業完成後移除）。
- **文件與待辦更新**
  - `Next Task Prompt Byob.md` 更新至 2025-11-20，新增 11/21 兩項重點任務（Reddit 貼文活化、第二封餐廳 Email 草擬），並移除過早歷史紀錄。

### 🔧 主要修改檔案
- `wordpress/single_restaurant.php`：新增 Website/Social 區塊、連結樣式與多格式解析。
- `wordpress/archive-restaurant.php`：預留相同顯示邏輯（暫時註解），供列表頁未來啟用。
- `wordpress/functions.php`：餐廳類型統計排序、預設類型集合、`social_links` 匯入清洗、臨時快取清除 hook（已撤除）。
- `doc/Next Task Prompt Byob.md`：重寫 11/20 摘要與 11/21 待辦。

### 📌 備註 / 重要決策
- 新的 Website/Social 區塊僅在該餐廳有填寫時渲染，避免空白欄位；列表頁需求待日後解除註解即可。
- 類型排序改以實際資料熱度為主，預設類型則確保平台主力分類持續曝光；cache 仍以 save_post/delete_post 事件清除。
- 11/21 重點鎖定社群活化與第二封餐廳 Email，確保資料更新與市場推廣同步推進。

---

## ✅ 2025年11月19日 — SendGrid 排程與餐廳類型篩選優化

### 🎯 今日成就總覽
- **SendGrid 批次寄信落地**
  - 整理 `takeover_tokens_20251118_copy.csv`，依每家餐廳的 Email_1~3 產出 31 筆 `personalizations`，寫入 `philly_yelp_crawler/testmail.json`。
  - 透過 API 建立 `batch_id` 與 `send_at`（對應台北 2025/11/19 20:28），使用 curl/PowerShell 成功排程並記錄驗證步驟、取消方式。
  - 新增 `doc/sendgrid_batch_email_flow.md`，把 CSV 準備、JSON 組裝、排程寄送、Activity 驗證與取消流程整理成白話 SOP。
- **餐廳類型篩選體驗大幅升級**
  - 在 `wordpress/functions.php` 新增 slug/label helper、URL 參數解析、快取與 `pre_get_posts` 過濾邏輯，支援多選 OR 條件與「Other」聚合。
  - `archive-restaurant.php` 類型 pill 可切換、清除、保留多選狀態，並在餐廳卡片/單頁以 chip 顯示，可直接連至指定篩選。
  - 類型已選項會自動排在列表頂端，並調整 chip 與地址間距、RWD 滑動體驗，確保桌機/手機一致。
- **文件與待辦同步**
  - `Next Task Prompt Byob.md` 更新至 2025-11-19，新增 11/20 的三項任務（官網按鈕、Placeholder 更換、媒體庫整理）。
  - `ai_progress_byob.md` 本檔案與 sendgrid flow, test JSON 等皆更新，確保日誌一致。

### 🔧 主要修改檔案
- `wordpress/functions.php`：餐廳類型 helper、URL 篩選、快取清理、Other 聚合等。
- `wordpress/archive-restaurant.php`：篩選列、卡片 chip、RWD/互動調整（含 active pill 排序、距離調整）。
- `wordpress/single_restaurant.php`：單頁 chip 可點擊導至列表。
- `philly_yelp_crawler/testmail.json`：批次寄信 JSON（含 batch_id/send_at）。
- `doc/sendgrid_batch_email_flow.md`、`doc/Next Task Prompt Byob.md`、`doc/ai_progress_byob.md`：流程與待辦紀錄。

### 📌 備註 / 重要決策
- 類型篩選採 OR 邏輯，並使用 URL query，使其他頁面可直接鏈接（例：`?types=italian`）。
- 「Other」類型在篩選列合併為單一按鈕，但餐廳卡片仍顯示詳細描述，兼顧易用性與資訊完整度。
- 建議寄信前先跑 1-2 筆測試並檢查 Sender Identity，確保 SendGrid 排程穩定；必要時透過 batch API 立即取消。

---

## 📊 專案整體進度

### 🍷 台北 BYOB 專案

**已完成核心模組：**
* ✅ 餐廳表單系統、WordPress 整合、推薦通知系統
* ✅ 重複檢查系統、審核管理系統、抽獎系統
* ✅ 多平台推廣、酒商名單收集

**進行中：**
* 🔄 酒商合作邀約 Email 擬定
* 🔄 Facebook 品酒社團規則確認和推廣

**待開發：**
* ⏳ 自動回覆系統、KPI 追蹤儀表板

---

### 🍷 費城 BYOB 專案

**已完成：**
* ✅ **專案規劃**：市場分析、AD 方案、榮譽系統設計
* ✅ **資料收集**：269 家候選餐廳（Yelp + Google Places）
* ✅ **爬蟲系統**：多平台整合、智能去重、信心度評估
* ✅ **雙表單系統**：網友推薦表單 + 老闆確認表單
* ✅ **自動化整合**：雙 Apps Script + WordPress API
* ✅ **Reddit 準備**：帳號建立、貼文策略、追蹤系統
* ✅ **系統優化**：英文化、ACF 修復、顯示邏輯優化
* ✅ **驗證徽章系統**：前台顯示與後台管理機制
* ✅ **Yelp 連結整合**：表單、後端、前台完整整合
* ✅ **餐廳 Email 搜尋系統**：兩階段自動化工具（11/4）
* ✅ **重複餐廳處理機制**：智能去重與標記（11/5）
* ✅ **網站前台英文化**：列表、單頁、表單全面英文化（11/6）
* ✅ **經緯度資料產出**：批次產出餐廳座標資料（11/13）
* ✅ **離你最近的 BYOB 餐廳功能**：地圖、定位、距離排序（11/14）
* ✅ **地圖標記圖標優化**：自定義 SVG 圖標、Attribution 添加（11/15）

**當前狀態：**
* ✅ 兩套完整表單系統運作中
* ✅ 資料來源自動辨識與追蹤
* ✅ 驗證狀態視覺化展示
* ✅ 地圖與定位功能完成
* ✅ 餐廳列表多層級排序邏輯完成

**近期重點（11/23）：**
* 📧 11/30 第二封 Email 排程：等到 SendGrid 72 小時視窗開啟後立即送出模板 2/3。
* 📣 FB 週一 Spotlight + Reddit 週五貼文草稿：依行事曆維持週節奏並導流至表單/Takeover。
* 🔄 持續蒐集餐廳回覆，反饋到 KPI 與地圖資料，確保後續寄送名單更新。

---

## 📝 技術工具與資源

### **費城專案工具**
* **多平台爬蟲系統**：Yelp + Google Places 整合爬蟲
* **智能去重系統**：保留來源資訊的資料整合
* **信心度評估**：High/Medium/Low 三級評分系統
* **Reddit 互動追蹤系統**：完整的管理和分析工具
* **Google 表單系統**：雙表單（推薦 + 確認）
* **Apps Script 自動化**：推薦版 + 確認版獨立處理
* **WordPress API 整合**：動態 source 處理、自動生成草稿
* **自動化文章生成系統**：英文內容模板和 SEO 優化
* **雙重通知系統**：成功和錯誤通知機制
* **驗證徽章系統**：前台顯示與後台管理
* **Yelp 連結整合**：表單、後端、前台完整整合
* **餐廳 Email 搜尋系統**：兩階段自動化搜尋工具
  - Google Places API 搜尋（取得 website）
  - Website email 提取（搜尋 email）
  - 支援多個 email 自動展開
* **地圖與定位系統**：Google Maps JavaScript API、客製 SVG 圖標、Attribution、定位排序
* `.env` 檔案 API key 管理：Python 爬蟲 / SendGrid / WordPress 指令

### **台北專案工具**
* `wine_exhibitor_crawler.py`：葡萄酒展參展商爬蟲
* `email_extractor.py`：Email 提取器
* WordPress 抽獎系統、重複檢查系統

---

