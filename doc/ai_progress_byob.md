# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-23）
* 資料源切換：Airtable 退場，改以 Firebase/Firestore 為唯一資料源；Softr 僅保留預覽。  
* 行動端：將在 FlutterFlow 以 Firebase 重建列表→詳情 MVP（重新起專案）。

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

---

## ✅ 2025-12-22 — 今日進度摘要（準備改用 Firebase）
1) **重寫工作計畫**  
   * 《Next Task Prompt Byob.md》改為 2025-12-23 起用 Firebase/Firestore 重建 FlutterFlow 專案，移除 Airtable 依賴。  
   * 規劃：新專案或複製清空 → Firestore 集合 `restaurants`（欄位 Name、cover_image_url、type_display、Phone、Add、Latitude、Longitude）→ 列表/詳情綁 Firestore → 取消所有 Airtable API/綁定。
2) **資料匯入準備**  
   * Airtable 匯出 CSV，並轉成 JSON（pandas 指令：`df.to_json(..., orient='records', force_ascii=False, indent=2)`），供 Firestore 匯入。  
   * 明確認定 `cover_image_url` 以文字欄位為主，附件連結過期可忽略。
3) **模板/專案策略**  
   * 既有模板無法重複使用時，可直接 Duplicate 現有專案後清空，或用其他相近模板重新建置。  
   * 清除舊 Airtable Query/綁定：API Group、JSON Path、`record.fields` 綁定、Page Params 需改為 Firestore docId。

---

## ✅ 2025-12-23 — 今日進度摘要（FlutterFlow 綁定準備）
1) **Firebase / FlutterFlow 連線**  
   * Firebase Auth 啟用 Email/Password，邀請 `firebase@flutterflow.io` 為 Editor。  
   * 註冊 Android/iOS App，下載並上傳 `google-services.json`、`GoogleService-Info.plist`，FlutterFlow 顯示 Firebase Setup Complete。  
   * Firestore 規則：read 開放、write 鎖定；Storage 暫時全鎖（不用）。
2) **Schema 建立**  
   * Firestore `restaurants` 已建欄位：`name`、`cover_image_url`、`type_display`、`Phone`、`Add`、`Latitude`、`Longitude`（可後加 `philly_corkage_fee`）。  
   * FlutterFlow Schema 同步建立，解決「has no fields」警告。
3) **列表頁接線（進行中）**  
   * ListView 已連 Firestore `restaurants`，但頁面內仍有多餘靜態區塊需清掉。  
   * 已確認 Image/文字的綁定路徑：Image Type 設 Network → Path 用 `Item at Index > Get Document Property > cover_image_url`；Text 用 `name`、`type_display` 等。  
   * 尚需：只留一張卡片樣板，刪除 ListView 內多餘 Container/Row/Banner，確保卡片顯示來自 Firestore 的圖片與文字。
4) **規劃更新**  
   * 《Next Task Prompt Byob.md》已改寫 12/24 行動：清理 ListView、完整綁定卡片欄位、預覽驗證，若有時間再加 corkage fee 邏輯與詳情頁導航。

## 🔭 下一步（2025-12-24，詳見《Next Task Prompt Byob.md》）
* 清掉 ListView 內多餘區塊，只留卡片樣板。  
* 綁定卡片：Image→`cover_image_url`；Title→`name`；副標→`type_display`；必要時地址/電話。  
* Run/Preview 確認 Firestore 資料正常載入；若有空再加 `philly_corkage_fee` 邏輯與詳情頁導航。
