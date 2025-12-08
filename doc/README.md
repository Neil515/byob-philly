# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，正轉往 Glide App MVP（BYOB near you 地圖體驗）。 |

---

## 🚀 最新進度（2025-12-08）

### 🌟 今日成果

* **資料維運腳本**：`philly_yelp_crawler/update_1117_restaurants.py` 改為讀寫 `Philly BYOB Restaurant.xlsx`，並新增 `ONLY_LATLNG` 參數，可只回填經緯度（搭配 `TARGET_DATE_FILTER` 鎖定 12/6 新增餐廳）。  
* **Glide App 建置**：完成 `BYOB restaurant 12.03` Google Sheet → Glide 匯入流程；卡片列表整理（關閉新增/編輯、Title/Description/Meta 皆對應正確欄位）。  
* **地圖資料處理**：以 Template 組成 `latitude,longitude` 字串後轉為 Location 欄位，BYOB near you 分頁成功顯示 85 筆 pin；額外建立 Map + Cards Layout 等待後續篩選。  
* **WordPress 顯示修正**：`byob_is_restaurant_complete()` 只檢查名稱 / 地址 / `philly_corkage_fee`，確保 Elma 等無電話但 verified 餐廳仍顯示。

### 🗓️ 明日計劃（2025-12-09）

1. **Glide 使用者定位 / 距離排序**：User Profiles 新增 `user_location`，以按鈕觸發 `Set columns → Current location`；建立 Distance 欄位並可依距離排序或篩選（如距離 < 3 公里）。  
2. **地圖 + 列表視覺優化**：Map Tooltip 顯示名稱 + 類別 + corkage；Inline List 加入 chips（餐廳類別、免 corkage、官網 / Yelp），並統一品牌色。  
3. **PWA 實機測試**：在 iOS/Android 使用「加入主畫面」測試定位權限與 fallback，產生公開連結記錄於 doc/README。

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

* ✅ 完成：餐廳資料收集、雙表單整合、餐廳接管流程、WP 地圖/定位、驗證徽章、Email 搜尋/批次寄送。
* 🔄 進行：Glide MVP（資料匯入、列表、BYOB near you 地圖、距離體驗）、SendGrid 第二封、社群節奏。
* ⏳ 後續：榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 📂 核心文件與工具

* `doc/philly_byob_complete_plan.md`：賽城專案完整實施計畫。  
* `doc/Next Task Prompt Byob.md`：每日任務規劃（已更新至 2025/12/09 工作）。  
* `doc/ai_progress_byob.md`：詳細進度日誌（最新至 2025/12/08）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/byob_restaurants.{json,csv}` + `Philly BYOB Restaurant.xlsx`：最新 85 筆餐廳資料來源。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯。  
* `BYOB restaurant 12.03` Google Sheet + Glide App：MVP 資料與前端實作。

---

## 🔭 即將聚焦（短期）

1. Glide「BYOB near you」完整體驗（定位、距離排序、Chips 篩選）。  
2. SendGrid 第二封 Email 與行銷素材同步。  
3. 餐廳資料維運 SOP：Excel 更新 → `update_1117_restaurants.py` → Google Sheet → Glide。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Glide 需手動保存使用者定位；透過 User Profiles + Distance 欄位自建「near you」邏輯。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-08*  
*版本：v26.0*  
*下一步：Glide BYOB near you、距離排序、PWA 實機測試*
