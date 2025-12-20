# 🍹 BYOB 專案工作規劃（FlutterFlow）

## 🗓️ 當前日期：2025-12-20

---

## 🗓️ 明日（2025-12-21）待辦：FlutterFlow MVP（列表→詳情）

### 1. 建專案與資料源
* **目標**：建立 FlutterFlow 專案並接上 Airtable。
* **步驟**：
  * New Project 選 Blank，命名 BYOB FlutterFlow MVP。
  * Data → Add Collection → Airtable：填 Base ID、PAT/API Key、Table=restaurants，主鍵=id。
  * 驗證欄位：Name、cover_image_url、type_display、Phone、Add、Latitude、Longitude。

### 2. 列表頁（Restaurants List）
* **目標**：顯示餐廳清單含封面、名稱、類型。
* **步驟**：
  * 新增 List Page（或 Blank+ListView.builder），Data Source 綁 Airtable collection。
  * 綁定欄位：Title=Name，Subtitle=type_display，Leading Image=cover_image_url。
  * On Tap → Navigate to Detail Page，傳遞 Record 參數。
* **完成定義**：捲動 30 筆仍正常載圖與文字。

### 3. 詳情頁（RestaurantDetails）
* **目標**：顯示點擊餐廳的完整資訊。
* **步驟**：
  * 新增 Detail Page，Widgets 綁 Page Parameter（Record）。
  * Image: cover_image_url；Title: Name；Text: type_display、Phone、Add。
  * Map/導航按鈕：Launch URL `https://maps.google.com/?q=${Latitude},${Longitude}`。
* **完成定義**：從列表點任一餐廳可正確顯示對應資料並可開地圖。

### 4. 驗證與發佈
* **目標**：基本流程可在預覽運行。
* **步驟**：
  * Run/Preview 測試列表載入與詳情導航。
  * 檢查圖片載入、空值 fallback（必要時加 placeholder）。
  * 記錄問題（效能、版位、欄位缺失）供後續優化。