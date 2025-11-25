# 🍷 BYOB 專案總覽文檔

## 💡 專案概述

| 城市 | 目標定位 | 目前狀態 |
|------|----------|----------|
| 🇹🇼 台北 BYOB | 自帶酒水餐廳推薦平台 | 核心系統穩定運作，持續推廣／酒商合作。紀錄僅保留高層狀態，細節集中在各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | 地圖/定位/表單皆完成，進入資料維運、餐廳聯絡、社群推廣與 UX 深化階段。 |

---

## 🚀 最新進度（2025-11-25）

### 🎯 今日成果
- **Other 類別體驗修正**：更新 `archive-restaurant.php`，讓頂部 chip 與卡片內的 `Other: XXX` 連結同源並可雙向開關，Chip 標籤固定顯示「Other」，篩選狀態一目了然。
- **進度文件同步**：`doc/Next Task Prompt Byob.md` 與 `doc/ai_progress_byob.md` 已更新至 2025/11/25，明確排定 11/26 的亂碼排查與內容規畫。

### 🗓️ 明日（11/26）計畫
1. **餐廳名稱亂碼排查**：鎖定前台列表的亂碼案例、檢查後台資料與輸出流程，確保 UTF-8 顯示正常。
2. **文章／引流 brief**：與內容/行銷討論下一篇文章題材、CTA 與素材需求，產出可直接寫稿的企劃文件。

---

## 🏗️ 技術架構（簡版）

```
資料收集層 → 表單驗證 → WordPress/ACF → 地圖 & UX → 餐廳聯絡 → 社群/Email
  • Yelp/Google 爬蟲          • 推薦/接管表單        • ACF 自訂欄位       • Google Maps JS API
  • 智能去重/去重標記        • Apps Script 自動化    • REST API / WP-CLI   • 自帶酒水地圖互動
  • 酒商/Email 搜尋工具      • Token 接管流程        • 榮譽/徽章系統       • SendGrid A/B + 社群節奏
```

台北專案沿用同一套流程，但目前僅維護 KPI／酒商合作，不在此文件展開。

---

## 📊 專案進度摘要

### 台北 BYOB
- ✅ 核心模組（表單、審核、抽獎、重複檢查、推播）穩定運作。
- 🔄 持續：酒商合作邀約、社團推廣、KPI 追蹤。
- ⏳ 待辦：自動回覆、儀表板優化（細節另見各模組文件）。

### 費城 BYOB
- ✅ 完成：資料收集、雙表單整合、餐廳接管流程、地圖/定位、驗證徽章、Email 搜尋/批次寄送。
- 🔄 進行：Email 模板第二封、FAQ/後台英文化、資料夾整理、社群節奏。
- ⏳ 後續：榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 🗂️ 核心文件與工具
- `doc/philly_byob_complete_plan.md`：費城專案完整實施計畫。
- `doc/Next Task Prompt Byob.md`：每日任務規畫（目前已更新到 2025/11/25）。
- `doc/ai_progress_byob.md`：詳細進度日誌（最新日期 2025/11/25）。
- `philly_yelp_crawler/lookup_post_ids.py` + `lookup_post_ids_README.md`：Excel↔WP Post ID 映射。
- `philly_yelp_crawler/push_acf_latlng.py` + README：將 Excel 經緯度寫入 ACF。
- `wordpress/assets/js/byob-nearby.js`、`wordpress/archive-restaurant.php`：地圖互動與資料來源。

---

## 🛠️ 關鍵里程碑（精簡版）
- **11/24**：地圖 UX 改版、全量 marker、REST 工具文件化。
- **11/23**：SendGrid 第二封模板就緒、社群節奏文件化。
- **11/17**：Restaurant Access Transfer（餐廳接管）流程完成。
- **11/14–15**：地圖與定位系統上線，自定義 marker + 距離排序。
- **11/06 以前**：雙表單、資料去重、英文化、Email 搜尋等基礎模組完成。

---

## 🔭 即將聚焦（短期）
1. **餐廳名稱亂碼排查**（資料庫編碼、模板輸出、`mb_convert_case` 等）。
2. **文章／引流 brief**（題材、CTA、素材需求、發布渠道）。
3. **SendGrid 第二封**：等待 11/27 後的 72 小時視窗排程並備妥內容。

---

## ⚙️ 持續挑戰與對策
- **餐廳聯絡資料蒐集**：透過兩階段 Email 搜尋與 Restaurant Transfer token 解決資料缺口；必要時進行人工追蹤。
- **地圖體驗與資料品質**：整合 geocode/pipeline、加入資料清理腳本，並以 README 記錄操作 SOP。
- **社群／內容節奏**：社群行事曆固定化（Reddit 週末長帖、FB Spotlight + 任務），CTA 統一導流三大入口。

---

*最後更新：2025-11-25*  
*版本：v25.1*  
*下一步：餐廳名稱亂碼排查、文章／引流 brief、SendGrid 第二封排程*
