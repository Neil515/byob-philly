# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，現正轉往 Softr App MVP（BYOB near you 地圖體驗）。 |

---

## 🚀 最新進度（2025-12-11）

### 🌟 今日成果

* **Airtable + Softr 轉換**：`Philly BYOB Restaurant.xlsx` 最新 1794 筆資料已匯入 Airtable，Softr App 以此為唯一資料源，完成 data source 授權。  
* **Softr 列表頁**：建立 `BYOB near you` 頁面，卡片顯示名稱 / 地址 / 餐廳類別，啟用搜尋列與 chips；更新標題、副標與筆數資訊。  
* **餐廳詳細頁**：新增共用 Item details block，呈現 corkage / service level / wine equipment / 官網 / Yelp，並加入 `Call` 按鈕（tel 協定），所有餐廳共享模板。  
* **任務同步**：`doc/Next Task Prompt Byob`、`doc/ai_progress_byob` 皆更新為 Softr 待辦與進度，準備 12/12 進一步完成地圖、CTA、篩選與發佈。

### 🗓️ 明日計劃（2025-12-12）

1. **Map with list 整合**：在 Softr 加入地圖區塊，pin 內容顯示名稱 / 類別 / corkage，與卡片連結至同一詳細頁。  
2. **CTA 優化**：卡片與詳情頁新增 `Call` / `Website` / `Yelp` / `Open in Google Maps` 按鈕。  
3. **篩選與排序**：Chips 分組、免開瓶費 toggle、服務層級多選篩選、排序下拉（名稱、最後驗證）。  
4. **發佈與紀錄**：完成調整後 `Publish`，更新 README 與測試連結，準備 user testing。

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
* `doc/Next Task Prompt Byob.md`：每日任務規劃（已更新至 2025/12/12 Softr 工作）。  
* `doc/ai_progress_byob.md`：詳細進度日誌（最新至 2025/12/11 Softr 進度）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` + Airtable Base：唯一資料來源與 Softr data source。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯（WordPress 版本）。  
* Softr App：`BYOB near you` 頁面 + 餐廳詳細模板（試用版 URL，待 12/12 發佈）。

---

## 🔭 即將聚焦（短期）

1. Softr「BYOB near you」完整體驗（地圖、CTA、篩選、排序、品牌視覺）。  
2. SendGrid 第二封 Email 與行銷素材同步。  
3. 餐廳資料維運 SOP：Excel 更新 → Airtable 匯入 → Softr 同步；必要時 `update_1117_restaurants.py` 補欄位。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Softr 需自訂地圖 + CTA；透過 Airtable 欄位與資料清理維持體驗一致。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-11*  
*版本：v27.0*  
*下一步：Softr BYOB near you 地圖整合、CTA/篩選優化、發佈測試*
