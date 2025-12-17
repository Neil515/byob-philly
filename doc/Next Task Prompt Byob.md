# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-12-17

---

## 🗓️ 明日（2025-12-18）待辦：Adalo 圖片分頁 / 地圖 / 篩選

### 1. 圖片只顯示前 10 筆（優先）
* **目標**：List 內所有餐廳都顯示封面，不只前 10 筆。
* **步驟**：
  * External Collection（Airtable REST）：加 `pageSize=100`、Pagination type=Offset，Next Page Offset Key=`offset`、Offset Query Param=`offset`，儲存後 Refresh Schema。
  * List → Image Source 綁定 `cover_image_url`（非 `cover_image > url`）；若需要 placeholder，設在 Image 的 fallback。
  * Airtable 檢查 `cover_image_url is empty` view；若有空值，跑 placeholder 腳本覆蓋寫入，再 Refresh Schema。
  * 預覽捲到第 11 筆以後，確認圖片仍正常載入；如需 Load more/Auto pagination，開啟並測試。
* **完成定義**：列表連續載入 30+ 筆仍有封面圖，無再出現 placeholder（除非原本無圖且預期 placeholder）。

### 2. 地圖（Markers 正確帶經緯度）
* **目標**：地圖標記出現且點擊可開導航。
* **步驟**：
  * Map 元件 → Marker Collection: Restaurants；Number of markers: Multiple。
  * Marker Address：`[Latitude], [Longitude]`（Magic Text 插入，逗號+空格），確保欄位為數字且無空值。
  * Click Action：Open Link → `https://maps.google.com/?q=[Latitude],[Longitude]`。
  * 驗證 Maps/Geocoding/Places API Key 可計費；預覽確認全部標記。
* **完成定義**：每筆餐廳在地圖上有標記，點擊可開啟 Google Maps 導航。

### 3. 篩選 / 搜尋（類別 chips）
* **目標**：依餐廳類型快速篩選列表（必要時同步地圖）。
* **步驟**：
  * 使用 `type_display` 或原多選欄位，建立 chips（含「All」清空）。
  * List Filter：Name/Type contains 選定類別；若要多選或同步 Map，再視需求調整。
  * 預覽確認切換 chips 後資料正確、效能可接受。
* **完成定義**：點擊類別 chip 後，列表僅顯示該類別餐廳，可恢復為全部；若啟用地圖同步，顯示一致。