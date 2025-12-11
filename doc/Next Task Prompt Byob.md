# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-12-11

---

## ✅ 今日摘要（2025-12-11）

* Airtable 已匯入 `Philly BYOB Restaurant.xlsx` 全量資料並連結 Softr App。
* Softr 建立 `BYOB near you` 頁面：卡片列表含搜尋與類別 Chips，卡片顯示名稱 / 地址 / 類別。
* 新增餐廳詳細頁模板：顯示 corkage / 服務等欄位，並加入 `Call` 按鈕，所有餐廳共用。
* 初步 UX 調整：卡片標題、副標、基本說明完成；準備進一步加入地圖與更多動作按鈕。

---

## 🗓️ 明日（2025-12-12）待辦：Softr App 功能深化

### 1. 地圖 + 列表整合
* **目標**：在 Softr `BYOB near you` 頁面加入 Map block，與現有卡片列表同步資料。
* **步驟**：
  * 新增 `Map with list` 區塊，綁定 `restaurants` 集合並設定 `latitude` / `longitude`。
  * 調整 tooltip 內容（名稱、餐廳類別、corkage）與品牌色。
  * 確保列表動作跳轉到現有餐廳詳細頁。
* **完成定義**：地圖與卡片資料一致，點擊 pin 或卡片皆可進入詳細頁。

### 2. 卡片與詳細頁動作優化
* **目標**：提供常用 CTA（撥打電話、查看官網、Yelp、導航）。
* **步驟**：
  * 在卡片區塊 `Actions` 新增 `Call`、`Website`、`Yelp` 按鈕（使用 `tel:`、外部 URL）。
  * 詳細頁加入 `Open in Maps` 按鈕（URL：`https://www.google.com/maps/search/?api=1&query={latitude},{longitude}`）。
  * 針對 corkage / service level 加上 badge 或 icon，幫助使用者快速辨識。
* **完成定義**：所有餐廳卡片與詳細頁皆顯示一致的 CTA，行動裝置測試可正常觸發。

### 3. 篩選與排序體驗
* **目標**：讓使用者可依類別、開瓶費、服務層級篩選並排序。
* **步驟**：
  * Chips 分組：保留常用類別、其餘改為 dropdown filter。
  * 新增 `Toggle filter` 針對 `corkage_fee_type = free`；新增 `Multi-select filter` 用於 `byob_service_level`。
  * 開啟 `Allow user to change sorting`，提供 `名稱 A→Z` 與 `最後驗證日期`。
* **完成定義**：桌機 / 手機預覽時可快速切換篩選條件並改變排序，畫面無破版。

### 4. 發佈與紀錄
* **目標**：保留 Softr 畫面更新狀態並產生可測試連結。
* **步驟**：
  * 完成調整後按 `Publish`，取得公開網址。
  * 在 `doc/README.md` 更新 Softr 測試連結與目前功能狀態。
  * 若仍需調整資料欄位，記錄於 `ai_progress_byob.md` 供後續追蹤。
* **完成定義**：README 已記錄最新 Softr 連結與注意事項，方便 12/12 測試續作。

---

> 備註：若需更新資料，只要覆蓋 `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` 並重新匯入 Google Sheet，Glide 會即時同步；經緯度更新可透過 `update_1117_restaurants.py`（搭配 `ONLY_LATLNG=1`）批次完成。
