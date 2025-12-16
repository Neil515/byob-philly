# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-12-16

---

## 🗓️ 明日（2025-12-17）待辦：Adalo 地圖 / 圖片 / 篩選

### 1. 地圖（Markers 正確帶經緯度）
* **目標**：地圖標記出現且點擊可開導航。
* **步驟**：
  * Map 元件 → Marker Collection: Restaurants；Number of markers: Multiple。
  * Marker Address：單行輸入 `[Latitude], [Longitude]`（Magic Text 插入 Latitude、逗號空格、Longitude），確保欄位為數字且無空值。
  * Click Action：Open Link → `https://maps.google.com/?q=[Latitude],[Longitude]`（Magic Text 插入）。
  * 驗證 API Key 已啟用 Maps/Geocoding/Places 並可計費；預覽確認標記出現。
* **完成定義**：每筆餐廳在地圖上有標記，點擊可開啟 Google Maps 導航。

### 2. 圖片（List 顯示 cover_image）
* **目標**：列表圖片顯示 Airtable cover_image。
* **步驟**：
  * List 項目內 Image → Image Source: URL → Magic Text 選 `fields > cover_image > url`（或 thumbnails.large.url）。
  * 若需預設圖：在 Image 設定 Placeholder 或條件顯示（若 url 為空則用內建 placeholder）。
  * 預覽檢查至少數筆有圖、無圖時顯示預設。
* **完成定義**：列表每列能正常顯示封面圖；缺圖時不影響版面。

### 3. 篩選 / 搜尋（類別 chips）
* **目標**：依餐廳類型快速篩選列表。
* **步驟**：
  * 使用 `type_display`（公式）或原多選欄位建立篩選邏輯；優先簡單方案：文字包含搜尋。
  * 在列表畫面加入一排 Chips/Buttons（常見類別：Italian、Asian、Japanese、Mediterranean…），點擊時設定篩選條件（List Filter：Name/Type contains 選定類別）。
  * 可留一顆「All」清空篩選；若要多選再決定是否增加。
  * 確認篩選後列表與地圖（如需）同步資料源；若無法同步，先完成列表篩選為主。
* **完成定義**：點擊類別 chip 後，列表僅顯示該類別餐廳；可恢復為全部。