# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，現正轉往 Softr App MVP（BYOB near you 地圖體驗）。 |

---

## 🚀 最新進度（2025-12-12）

### 🌟 今日成果

* **Airtable 欄位清理**：建立 `byob_service_level_tmp` 公式，轉換空值/「請選擇」為 `-`；新增 `byob_service_level_fixed`（Single select，含 `-`），批次貼上整理結果。  
* **Softr 顯示檢查**：詳細頁 Tag 類型可用，待資料源刷新後改用 `byob_service_level_fixed`。  
* **任務同步**：`doc/Next Task Prompt Byob`、`doc/ai_progress_byob` 已更新，明日聚焦圖片/封面與餐廳類型呈現。

### 🗓️ 明日計劃（2025-12-13）

1. **圖片/封面**：列表與詳情補預設圖，確認裁切比例與手機預覽。  
2. **餐廳類型呈現**：Softr 改用 `byob_service_level_fixed` Tag，主/補充類型併排顯示，空值不露出「請選擇」。  
3. **發佈與紀錄**：完成調整後 Publish，更新 README 與測試連結；必要時補記 ai_progress。

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
* ⏳ 待辦：自動回覆、儀表板優化（詳情見各模組文件）。

### 費城 BYOB

* ✅ 完成：餐廳資料收集、雙表單整合、餐廳接管流程、WordPress 地圖/定位、驗證徽章、Email 搜尋/批次寄送。
* 🔄 進行：Softr MVP（Airtable 資料、列表/地圖整合、CTA/篩選體驗）、SendGrid 第二封、社群節奏。
* ⏳ 後續：榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 📂 核心文件與工具

* `doc/philly_byob_complete_plan.md`：賽城專案完整實施計畫。  
* `doc/Next Task Prompt Byob.md`：每日任務規劃（已更新至 2025/12/13 Softr 工作）。  
* `doc/ai_progress_byob.md`：詳細進度日誌（最新至 2025/12/12 Softr 進度）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` + Airtable Base：唯一資料來源與 Softr data source。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯（WordPress 版本）。  
* Softr App：`BYOB near you` 頁面 + 餐廳詳細模板（試用版 URL，待 12/12 發佈）。

---

## 🔭 即將聚焦（短期）

1. Softr「BYOB near you」：圖片/封面、類型/Tag 顯示、地圖/CTA/篩選逐步完成。  
2. SendGrid 第二封 Email 與行銷素材同步。  
3. 餐廳資料維運 SOP：Excel 更新 → Airtable 匯入 → Softr 同步；必要時 `update_1117_restaurants.py` 補欄位。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Softr 需自訂地圖 + CTA；透過 Airtable 欄位與資料清理維持體驗一致。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-12*  
*版本：v28.0*  
*下一步：Softr BYOB near you 圖片/類型顯示優化，刷新欄位並發佈測試*
