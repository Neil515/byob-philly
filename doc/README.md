# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，Softr 保留預覽；行動端改用 Adalo。 |

---

## 🚀 最新進度（2025-12-17）

### 🌟 今日成果（Adalo）
* **封面圖欄位改用文字 URL**：Airtable 新增可寫欄位 `cover_image_url`，以餐廳類型批次寫入 placeholder（依 `airtable_placeholder_script.md`），並提供安全 fallback 腳本避免類型缺值/空陣列。  
* **App 綁定調整**：Adalo External Collection 重新建立後抓到 `cover_image_url`；Image Source 改綁該欄位，避免前 10 筆後只出現預設圖。  
* **載入問題定位**：確認資料有載入，但圖片第 11 筆後落回 placeholder，研判需在 Airtable REST 設 `pageSize=100` + offset 分頁並於 List 啟用自動分頁/Load more。

### 🗓️ 下一步（2025-12-18，詳見《Next Task Prompt Byob.md》）
1. 圖片：External Collection 設 `pageSize=100`、Offset 分頁；Image 綁 `cover_image_url`；Airtable 檢查空值並覆蓋補齊，驗證 30+ 筆仍有封面。  
2. 地圖：Marker 使用 `[Latitude], [Longitude]`，點擊開 Google Maps。  
3. 篩選：類別 chips（含 All），必要時同步地圖顯示。

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
* 🔄 進行：Softr 網站維持可預覽（搜尋/篩選分離，需文案提示）；Adalo App（行動版，上架為目標）啟動骨架製作；SendGrid 第二封、社群節奏。
* ⏳ 後續：Adalo 完成後進行上架流程；榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 📂 核心文件與工具

* `doc/philly_byob_complete_plan.md`：賽城專案完整實施計畫。  
* `doc/Next Task Prompt Byob.md`：每日任務規劃（最新至 2025/12/18 圖片分頁/地圖/篩選）。  
* `doc/ai_progress_byob.md`：進度日誌（最新至 2025/12/17 Adalo 圖片欄位與載入）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` + Airtable Base：唯一資料來源與 Softr data source。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯（WordPress 版本）。  
* Softr 網站：`BYOB near you`（地圖 + 詳情，搜尋/篩選各自獨立，文案提示）。
* Adalo App：行動版 BYOB Map（Mobile Only 專案，Restaurant 範本，製作骨架中，目標 App Store/Play）。

---

## 🔭 即將聚焦（短期）

1. Adalo BYOB Map：完成地圖標記、圖片顯示與類別篩選，進入上架前驗證。  
2. Softr 網站：維持可預覽，搜尋/篩選分離並保留提示；必要時更新文案與資料。  
3. SendGrid 第二封 Email 與行銷素材同步；資料維運 SOP（Excel → Airtable，同步至前端）。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Softr 需自訂地圖 + CTA；透過 Airtable 欄位與資料清理維持體驗一致。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-17*  
*版本：v32.0*  
*下一步：Adalo 完成圖片分頁、地圖、篩選；Softr 保持可預覽與文案提示*
