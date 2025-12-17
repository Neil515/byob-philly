# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-17）
* Airtable 為唯一來源；Softr 網站保留預覽。  
* 行動端改用 Adalo（Mobile Only + Restaurant 範本），目標上架 App Store/Play。

---

## ✅ 2025-12-16 — 今日進度摘要（Adalo）
1) **Airtable 類別合併顯示**  
   * 新增公式欄位 `type_display = ARRAYJOIN({philly_restaurant_type}, " · ")`，多選類別可一次顯示；保持原多選欄位供篩選。  
2) **列表文字與圖片綁定**  
   * Magic Text 一律用 Current Restaurant’s（避免整集合）。  
   * 描述可在同一 Text 以空格/符號分隔；需換行則按 Enter。  
   * 圖片：Image Source 選 URL → `fields > cover_image > url`（或 thumbnails.large.url）；缺圖可用 placeholder。  
3) **地圖設定（使用經緯度）**  
   * Marker Collection: Restaurants；Number of markers: Multiple。  
   * Marker Address 僅一行輸入 `[Latitude], [Longitude]`（Magic Text 插入，逗號空格分隔），確保欄位為數字且無空值。  
   * 可設定 Click Action → Open Link `https://maps.google.com/?q=[Latitude],[Longitude]`。  
4) **欄位同步與快取**  
   * `cover_image`（附件）與 `type_display` 已同步到 External Collection；若看不到新欄位，Refresh Schema 即可。

---

## ✅ 2025-12-17 — 今日進度摘要（Adalo 圖片欄位/載入）
1) **改用 `cover_image_url` 供 App 綁定**  
   * Airtable 新增可寫的單行文字欄位 `cover_image_url`，不再依附件物件。  
   * 依餐廳類型批次寫入 placeholder（使用 `airtable_placeholder_script.md` 連結），提供「安全 fallback」腳本避免無對應類型/空陣列。
2) **Adalo 綁定調整**  
   * External Collection 重新建立後抓到 `cover_image_url`；Image Source 改綁該欄位，避免所有餐廳同圖或缺圖顯示 placeholder。  
3) **問題定位：僅前 10 筆顯示圖片**  
   * 確認資料筆數有載入，但第 11 筆之後仍顯示預設圖，研判為 Airtable REST 分頁/offset 未設置；規劃改用 `pageSize=100` + offset 分頁，並在 List 啟用 load more/自動分頁。

---

## 🔭 下一步（詳見《Next Task Prompt Byob.md》12/18）
* 圖片：External Collection 設 `pageSize=100`、Offset 分頁，Image 綁 `cover_image_url`；Airtable 檢查空值並覆蓋補齊，確保 30+ 筆仍顯示封面。  
* 地圖：Marker 使用 `[Latitude], [Longitude]`，點擊開 Google Maps。  
* 篩選：類別 chips（含 All），必要時同步地圖顯示。
