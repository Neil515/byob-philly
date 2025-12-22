# 🍹 BYOB 專案工作規劃（FlutterFlow）

## 🗓️ 當前日期：2025-12-23

---

## 🗓️ 今日（2025-12-23）重建：Firebase 新專案（取代 Airtable）

### 1. 新專案與模板
* **目標**：用相同模板或複製現有專案，建立全新 FlutterFlow 專案（改用 Firebase）。
* **步驟**：
  * 若找得到模板：`Create New` → 選原模板；找不到則用現有專案 `Duplicate` 後再清空頁面。
  * 命名：`BYOB Firebase MVP`。

### 2. Firebase 設定與資料匯入
* **目標**：Firestore 成為唯一資料源，匯入餐廳資料。
* **步驟**：
  * 確認 Firebase 專案與 Firestore 已建立；FlutterFlow Data Sources 僅保留 Firebase。
  * 依結構建立集合（如 `restaurants`），欄位：Name、cover_image_url、type_display、Phone、Add、Latitude、Longitude。
  * 用既有 CSV/JSON（byob_restaurants.json 或轉好的 CSV）批次匯入 Firestore（可用導入工具或手動腳本）。

### 3. 列表頁（Restaurants List）
* **目標**：從 Firestore 顯示餐廳清單含封面、名稱、類型。
* **步驟**：
  * ListView 資料源改為 Firestore `restaurants`；只保留一張卡片樣板。
  * 綁定：Image→cover_image_url；Title→Name；Subtitle→type_display；On Tap 傳 docId。
* **完成定義**：捲動 30+ 筆仍正常載圖與文字。

### 4. 詳情頁（RestaurantDetails）
* **目標**：顯示單筆餐廳完整資訊並可導航。
* **步驟**：
  * 接收 docId；FireStore Query 取該筆。
  * 綁定：cover_image_url、Name、type_display、Phone、Add。
  * 地圖按鈕：Launch URL `https://maps.google.com/?q=${Latitude},${Longitude}`。
* **完成定義**：從列表任一筆點入能正確顯示並開地圖。

### 5. 清理 Airtable 依賴 & 驗證
* **目標**：專案僅剩 Firebase。
* **步驟**：
  * Connect → API Calls / Integrations：刪除 Airtable Group/Calls、移除舊 JSON Path。
  * 檢查頁面綁定，移除 `record.fields` 相關綁定與 Page Params，改用 Firestore doc 資料。
  * Run/Preview：確認無 Airtable 401/404，列表與詳情皆來自 Firestore，圖片/導航正常。