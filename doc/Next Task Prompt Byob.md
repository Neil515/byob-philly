# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-12-14

---

## ✅ 今日摘要（2025-12-14）

* Softr：搜尋/篩選提示文案調整，明確標示地圖與列表各自獨立，不再追求共用控件。
* Domain/Logo：確認可用自有網域 byobmap.com；Softr 全域 Logo 上傳受方案限制，需在導航 block 內上傳。
* App 策略：決定改以 Adalo 製作行動 App（目標 App Store/Play），今日完成專案建立（Mobile Only，Restaurant 模板）並確認基本編輯器操作。

---

## 🗓️ 明日（2025-12-15）待辦：Adalo BYOB Map 初版骨架

### 1. 資料模型建立
* **目標**：完成核心 Collections（Restaurants、Restaurant Types／Tags、Users 如需登入）。
* **步驟**：
  * Restaurants 欄位：name、address、phone、lat、lng、cuisine/type（多選可用關聯）、corkage_fee、image/placeholder、slug/ID。
  * 若需分類 Chips：建立 Types 集合並關聯多對多；預填常用類別。
  * 確認可否直接接 Airtable／CSV；若不行先用 Adalo DB 匯入小樣本。
* **完成定義**：集合與欄位就緒，可在編輯器看到範例資料並可查詢。

### 2. 畫面與流程骨架
* **目標**：建立主要螢幕與導航，替換餐廳模板預設頁。
* **步驟**：
  * 保留/替換底部導航：Home（列表+搜尋）、Map（地圖）、Favorites/Account（可先占位）。
  * 列表頁：接 Restaurants 集合，加入搜尋（Name/Address）與篩選 Chips（Cuisine/Type）。
  * 地圖頁：使用 Map 元件，綁定 lat/lng，點標記開啟餐廳詳情。
  * 詳情頁：顯示圖/名稱/地址/電話/費用/類型；放收藏或「打開地圖導航」按鈕（可先空白動作）。
* **完成定義**：導航可切換；列表、地圖、詳情三頁皆可載入同一份假資料。

### 3. 樣式與品牌
* **目標**：套用基本色系與名稱，去除模板咖啡廳素材。
* **步驟**：
  * App Name：Philly BYOB Map（或 Philadelphia BYOB Map）。
  * Primary/Secondary Colors：套用品牌色（可先 #7B1FA2 / #FFC107 或保留預設，再視覺調整）。
  * 替換模板圖片為 placeholder，移除咖啡文案。
* **完成定義**：主要頁面無咖啡示例圖與文字，顏色一致。

### 4. 驗證與下一步準備
* **目標**：手機預覽確認互動流暢，列出資料匯入與上架需求。
* **步驟**：
  * Adalo Preview 測試：搜尋/篩選對列表，地圖標記可點，詳情正常開。
  * 列出後續：資料批次匯入方案、推播/登入要否啟用、App Store/Play 打包需求。
* **完成定義**：有可演示的最小互動流，並列出上架前的缺口清單。

---

> 備註：Softr 網站暫維持可預覽；行動端改走 Adalo。若要回填真實資料，可先用小批次 CSV/手動輸入測試，後續再決定 Airtable 直連或 API 匯入。
