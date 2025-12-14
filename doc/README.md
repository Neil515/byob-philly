# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，現正轉往 Softr App MVP（BYOB near you 地圖體驗）。 |

---

## 🚀 最新進度（2025-12-14）

### 🌟 今日成果

* **Softr 調整**：搜尋/篩選以文案標示「地圖與列表各自獨立」；確認可用自有網域 `byobmap.com`，全域 Logo 上傳受方案限制需在導航 block 內上傳。  
* **Adalo 啟動**：新建 Mobile Only 專案（Restaurant 模板），設定品牌名稱，熟悉畫布縮放與頁面切換，準備替換預設頁與資料。  
* **任務同步**：`Next Task Prompt Byob.md` 更新至 12/15，聚焦 Adalo 資料模型、列表/地圖/詳情骨架與樣式替換。

### 🗓️ 明日計劃（2025-12-15）

1. **Adalo 資料模型**：建立 Restaurants（name/address/phone/lat/lng/type/corkage_fee/image/slug）、Types/Tags（多對多），必要時 Users。  
2. **畫面骨架**：底部導航；列表頁搜尋+篩選 Chips；地圖頁綁 lat/lng，標記開啟詳情；詳情頁顯示餐廳資訊。  
3. **樣式替換**：移除模板咖啡素材，套品牌色與 placeholder。  
4. **驗證**：Preview 測試列表/地圖/詳情流程，列出匯入與上架需求。

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
* `doc/Next Task Prompt Byob.md`：每日任務規劃（已更新至 2025/12/15 Adalo 骨架任務）。  
* `doc/ai_progress_byob.md`：詳細進度日誌（最新至 2025/12/14 Softr 調整與 Adalo 啟動）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` + Airtable Base：唯一資料來源與 Softr data source。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯（WordPress 版本）。  
* Softr 網站：`BYOB near you`（地圖 + 詳情，搜尋/篩選各自獨立，文案提示）。
* Adalo App：行動版 BYOB Map（Mobile Only 專案，Restaurant 範本，製作骨架中，目標 App Store/Play）。

---

## 🔭 即將聚焦（短期）

1. Adalo BYOB Map：完成資料模型與列表/地圖/詳情骨架，進入上架前驗證。  
2. Softr 網站：維持可預覽，搜尋/篩選分離並保留提示；必要時更新文案與資料。  
3. SendGrid 第二封 Email 與行銷素材同步；資料維運 SOP（Excel → Airtable，同步至前端）。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Softr 需自訂地圖 + CTA；透過 Airtable 欄位與資料清理維持體驗一致。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-14*  
*版本：v30.0*  
*下一步：Adalo 完成資料模型與畫面骨架，進行預覽驗證；Softr 保持可預覽與文案提示*
