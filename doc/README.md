# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，持續推廣／酒商合作。紀錄僅保留高層狀態，細節集中在各模組文件。 |
| 🇺🇸 賽城 BYOB | Yelp 的 BYOB 專業補充平台 | 地圖/ 定位/ 表單皆完成，進入資料維運、餐廳聯絡、社群推廣與 UX 深化階段。 |

---

## 🚀 最新進度（2025-11-26）

### 🌟 今日成果

* 完成初步餐廳清單建立、格式標準化與詳細欄位填寫，包含通訊資訊、Yelp、是否 BYOB、開瓶費、提供設備、類型等。
* 資料經補齊已達到可直接進入結構設計與前端使用的準備狀態，預備轉成 JSON/CSV 格式。
* 設計搜尋與篩選邏輯、內部欄位與 UI 對應關係既進入擴寬設計階段，支援最簡 MVP 系統。
* 更新 `Next Task Prompt Byob.md` 與 `ai_progress_byob.md` 文件，細節述說明日緊接課題與分工說明。

### 🗓️ 明日計劃

1. JSON / SQL 資料結構轉換
2. 搜尋條件 / UI 篩選對應表入手
3. MVP 功能測試集與前端測試

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
* 🔄 進行：Email 模板第二封、FAQ/後台英文化、資料夾整理、社群節奏。
* ⏳ 後續：榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 📂 核心文件與工具

* `doc/philly_byob_complete_plan.md`：賽城專案完整實施計畫。
* `doc/Next Task Prompt Byob.md`：每日任務規劃（目前已更新到 2025/11/26）。
* `doc/ai_progress_byob.md`：詳細進度日誌（最新日期 2025/11/26）。
* `philly_yelp_crawler/lookup_post_ids.py` + `lookup_post_ids_README.md`：Excel↔WP Post ID 映射。
* `philly_yelp_crawler/push_acf_latlng.py` + README：將 Excel 經總度寫入 ACF。
* `wordpress/assets/js/byob-nearby.js`、`wordpress/archive-restaurant.php`：地圖互動與資料來源。

---

## 🔭 即將聚焦（短期）

1. JSON/SQL 資料轉換與 Schema 碼寫入
2. 搜尋 UI 條件與篩選邏輯實作
3. SendGrid 第二封配合文案準備

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：透過兩階段 Email 搜尋與 Restaurant Transfer token 解決資料缺口；必要時進行人工追蹤。
* **地圖體驗與資料品質**：整合 geocode/pipeline、加入資料清理腳本，並以 README 記錄操作 SOP。
* **社群/內容節奏**：社群行事曆固定化（Reddit 週末長帖、FB Spotlight + 任務），CTA 統一導流三大入口。

---

*最後更新：2025-11-26*
*版本：v25.2*
*下一步：資料轉換、搜尋 UI 設計、SendGrid 字案配合*
