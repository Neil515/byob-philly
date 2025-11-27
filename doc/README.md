# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，持續推廣／酒商合作。紀錄僅保留高層狀態，細節集中在各模組文件。 |
| 🇺🇸 賽城 BYOB | Yelp 的 BYOB 專業補充平台 | 地圖/ 定位/ 表單皆完成，進入資料維運、餐廳聯絡、社群推廣與 UX 深化階段。 |

---

## 🚀 最新進度（2025-11-27）

### 🌟 今日成果

* **Schema 定稿**：`philly_yelp_crawler/byob_schema_spec.md` 完成，定義 17 個必備欄位、枚舉值與 CSV/JSON 規則。
* **匯出腳本**：`philly_yelp_crawler/scripts/byob_export.py` 可清洗 Excel、檢查必填欄位並輸出標準 JSON/CSV。
* **資料輸出**：產出 `philly_yelp_crawler/data/byob_restaurants.json`、`byob_restaurants.csv`（共 85 筆），App MVP 可直接串接。
* **缺值追蹤**：紀錄 26 個欄位仍空白（官網 / Yelp / 電話 / 兩筆開瓶費金額），已在 issue log 內標示，後續視需求補齊。

### 🗓️ 明日計劃（2025-11-28）

1. **資料供應管線定稿**：文件化 JSON/CSV 的存放位置、再生產流程與版本控管方式。
2. **App 資料模型／載入模組**：建立 TypeScript/後端 DTO、靜態 Loader，並設計缺值顯示規則。
3. **後續任務排程**：整理搜尋／篩選邏輯 backlog、資料補齊責任、以及 11/29 之後的行動清單。

---

## 🌊 技術架構

```
資料收集層 → 表單驗證 → WordPress/ACF → 地圖 & UX → 餐廳聯絡 → 社群/Email
  • Yelp/Google 爬蟲          • 推薦/接管表單        • ACF 自訂欄位       • Google Maps JS API
  • 智能去重/去重標記        • Apps Script 自動化    • REST API / WP-CLI   • 自帶酒水地圖互動
  • 酒商/Email 搜尋工具      • Token 接管流程        • 榮譽/徽章系統       • SendGrid A/B + 社群節奏
```

---

## 📊 專案進度摘要

### 臺北 BYOB

* ✅ 核心模組（表單、審核、抽獎、重複檢查、推播）穩定運作。
* 🔄 持續：酒商合作邀約、社團推廣、KPI 追蹤。
* ⏳ 待辦：自動回覆、儀表板優化（細節另見各模組文件）。

### 賽城 BYOB

* ✅ 完成：資料收集、雙表單整合、餐廳接管流程、地圖/定位、驗證徽章、Email 搜尋/批次寄送。
* 🔄 進行：資料匯出與 App 接軌（schema、JSON/CSV、資料載入模組）、Email 模板第二封、社群節奏。
* ⏳ 後續：榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 📂 核心文件與工具

* `doc/philly_byob_complete_plan.md`：賽城專案完整實施計畫。
* `doc/Next Task Prompt Byob.md`：每日任務規劃（最新至 2025/11/27）。
* `doc/ai_progress_byob.md`：詳細進度日誌（最新至 2025/11/27）。
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表。
* `philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。
* `philly_yelp_crawler/data/byob_restaurants.{json,csv}`：最新 85 筆 BYOB 餐廳資料。
* `philly_yelp_crawler/lookup_post_ids.py` + `lookup_post_ids_README.md`：Excel↔WP Post ID 映射。
* `philly_yelp_crawler/push_acf_latlng.py` + README：將 Excel 經總度寫入 ACF。
* `wordpress/assets/js/byob-nearby.js`、`wordpress/archive-restaurant.php`：地圖互動與資料來源。

---

## 🔭 即將聚焦（短期）

1. 資料供應管線定稿與資料載入模組
2. 搜尋 UI 條件與篩選邏輯實作
3. SendGrid 第二封配合文案準備

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：透過兩階段 Email 搜尋與 Restaurant Transfer token 解決資料缺口；必要時進行人工追蹤。
* **地圖體驗與資料品質**：整合 geocode/pipeline、加入資料清理腳本，並以 README 記錄操作 SOP。
* **社群/內容節奏**：社群行事曆固定化（Reddit 週末長帖、FB Spotlight + 任務），CTA 統一導流三大入口。

---

*最後更新：2025-11-27*
*版本：v25.3*
*下一步：資料管線定稿、App 資料模型、SendGrid 第二封*
