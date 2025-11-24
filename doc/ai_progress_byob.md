# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-11-24）

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

**近期重點（11/25）：**
* 🧭 修復「Other」類別篩選，確保篩選/共享連結都能顯示完整結果。
* 📝 完成下一篇網站文章（含 CTA、SEO meta），支援地圖導流。
* 🗂️ 整理 `restaurant_crawler/` 資料夾，標註仍使用/可封存的腳本與資料檔。

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

