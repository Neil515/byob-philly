# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，現正轉往 Softr App MVP（BYOB near you 地圖體驗）。 |

---

## 🚀 最新進度（2025-12-13）

### 🌟 今日成果

* **Airtable placeholder 腳本**：新增 `doc/airtable_placeholder_script.md`，正規化 `philly_restaurant_type`，依類型隨機填入 WP 公開圖作為 placeholder（僅補空白附件，保留 `other/default`）。  
* **Softr 地圖/列表配置**：Google Maps block 接上 API key、中心改費城並啟用 cluster；檢視分頁（Pages / 30 per page）與 Load more 文案；確認行動端需以點擊而非 hover。  
* **Search/Filter 規劃**：確認地圖與列表分別的搜尋/篩選會造成結果不同步，建議改用單一（或 Map+List 合併）控件共用；`Next Task` 及 `ai_progress` 已同步到 12/14 聚焦共用搜尋/篩選與上架前收尾。

### 🗓️ 明日計劃（2025-12-14）

1. **地圖＆列表共用 Search/Filter**：優先採用合併模板或保留單一控件，綁同欄位後測試桌機/行動一致性。  
2. **上架前整理與發布**：移除多餘區塊/預設文案，檢查圖片比例與 placeholder 一致性，測試搜尋/篩選、卡片/標記點擊與分頁，Publish 並更新 README / ai_progress。

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
* `doc/Next Task Prompt Byob.md`：每日任務規劃（已更新至 2025/12/14 共用搜尋/篩選與收尾）。  
* `doc/ai_progress_byob.md`：詳細進度日誌（最新至 2025/12/13 Softr 進度）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` + Airtable Base：唯一資料來源與 Softr data source。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯（WordPress 版本）。  
* Softr App：`BYOB near you` 地圖 + 餐廳詳細模板（已接 API key，進行共用搜尋/篩選與上架前收尾）。

---

## 🔭 即將聚焦（短期）

1. Softr「BYOB near you」：完成地圖＆列表共用搜尋/篩選，行動版驗證，收尾後 Publish。  
2. SendGrid 第二封 Email 與行銷素材同步。  
3. 餐廳資料維運 SOP：Excel 更新 → Airtable 匯入 → Softr 同步；必要時 `update_1117_restaurants.py` 補欄位。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Softr 需自訂地圖 + CTA；透過 Airtable 欄位與資料清理維持體驗一致。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-13*  
*版本：v29.0*  
*下一步：完成 Softr 地圖/列表共用搜尋/篩選並發布，更新測試連結*
