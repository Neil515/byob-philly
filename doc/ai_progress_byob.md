# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-11-25）

### **台北 BYOB**（核心系統維運）
- 核心功能與自動化流程已完整，持續推廣、酒商合作與 Email 模板優化。

### **費城 BYOB**（主要開發重心）
- 前台英文化完成，持續拓展資料源、餐廳接管、自動寄信與社群推廣。
- 技術堆疊：WordPress + WooCommerce + ACF + WP-CLI + Python 資料腳本 + SendGrid。


## ✅ 2025年11月24日 — 地圖 UX / 經緯度整合

### 🎯 今日成就總覽
- **地圖互動全面改版**
  - `archive-restaurant.php` + `assets/js/byob-nearby.js` 將 hover 改成「點擊鎖定 + 資訊面板」，桌機顯示右側卡片、手機顯示底部 sheet，`View Details >>` 固定新視窗。
  - 新增地圖 overlay、延遲關閉、附近清單同步高亮，避免滑鼠移動就消失的問題。
- **Marker 改為全量載入**
  - 地圖資料不再受文章分頁限制，額外查詢全部餐廳 ID 並組成 JSON，現在 85 家都會顯示 marker。
  - 地圖資料仍分頁顯示卡片，避免一次渲染 80+ 張卡片造成卡頓。
- **經緯度與 Post ID 流程文件化**
  - 為 `lookup_post_ids.py`、`push_acf_latlng.py` 撰寫 README，紀錄 slug 產生、REST API 權限、Application Password 設定與常見錯誤（404 / incorrect_password）。
  - 已驗證兩套 Excel（主資料 + geocode_full）都能填回 `WP_Post_ID`、並以 ACF API 更新經緯度。

### 🔧 主要修改檔案
- `wordpress/archive-restaurant.php`
- `wordpress/assets/js/byob-nearby.js`
- `philly_yelp_crawler/lookup_post_ids.py` + `lookup_post_ids_README.md`
- `philly_yelp_crawler/push_acf_latlng.py` + `push_acf_latlng_README.md`

### 📌 備註 / 重要決策
- Geolocation / Nearby 清單仍以現有 API 取得資料，地圖 JSON 分開 cache，方便之後升級。
- REST API 一律使用 Application Password（已啟用 ACF to REST API 外掛），確保腳本可重複使用。

## ✅ 2025年11月25日 — 類別篩選體驗優化

### 🎯 今日成就總覽
- **Other 類別行為統一**
  - `archive-restaurant.php` 讓頂部 chip 與卡片內的 `Other: XXX` 都指向相同 URL，並且支援再次點擊就移除篩選。
  - Chip 標籤固定顯示「Other」，避免出現 `Other: Sushi Bars` 這類混淆文字，前後台同步。
- **明日作業預備**
  - 彙整列表頁餐廳名稱亂碼案例、推測與資料庫編碼或 `mb_convert_case` 相關，作為 11/26 排查起點。
  - 與內容行銷合作規畫下一篇文章的寫作方向與 CTA，先整理素材需求與預期導流路徑。

### 🔧 主要修改檔案
- `wordpress/archive-restaurant.php`
- `doc/Next Task Prompt Byob.md`

### 📌 備註 / 重要決策
- 之後若要支援「Only Other: Pizza」的精準篩選，必須額外儲存 other note 並提供新 slug；目前維持聚合邏輯。
- 11/26 會以「亂碼修正」與「文章引流 brief」為主，今日已在 Next Task Prompt 中排程。

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

**近期重點（11/26）：**
* 🛠️ 排查列表頁餐廳名稱亂碼（確認資料庫編碼、ACF/模板輸出）。
* 📰 與內容/行銷討論下一篇文章與引流策略，產出 brief。

---

