# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-20）
* Airtable 為唯一資料源；Softr 僅作預覽。  
* 行動端：Adalo 嘗試告一段落，改以 FlutterFlow 建立 MVP（列表→詳情）。

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

---

## ✅ 2025-12-20 — 今日進度摘要（Thunkable→FlutterFlow 決策）
1) **Thunkable 列表→詳情嘗試**  
   * 已在 Thunkable 連接 Airtable、建立列表與詳情頁、設定點擊事件與導航。  
   * 受限於 Data Viewer 清單未提供整筆物件/欄位讀取積木，需逐欄位以 Data Source 取值並手組物件，流程過於繁瑣。
2) **決策：改用 FlutterFlow**  
   * 明日 12/21 轉向 FlutterFlow 重建列表→詳情 MVP。  
   * 已更新《Next Task Prompt Byob.md》為 FlutterFlow 工作計畫（Airtable 連線、列表綁定、詳情參數、地圖按鈕）。

## 🔭 下一步（詳見《Next Task Prompt Byob.md》2025-12-21）
* FlutterFlow：建立專案、接 Airtable、列表頁綁 Name/type_display/cover_image_url，點擊傳 Record；詳情頁綁參數並加地圖開啟連結。  
* 預覽驗證：列表載入 30+ 筆、詳情資料正確、地圖按鈕可開導航；記錄待優化事項。
