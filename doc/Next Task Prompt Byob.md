# 🍹 BYOB 專案工作規劃（FlutterFlow）

## 🗓️ 當前日期：2025-12-24

---

## 🗓️ 明日任務（12/24）— 完成列表頁資料綁定

### 目標
將複製的首頁（測試用）ListView 卡片，完整綁定 Firestore `restaurants` 集合欄位，清掉多餘靜態元件，確保圖片與文字皆來自資料庫。

### 待辦
1) 梳理 ListView 結構  
   - ListView 內只留一個卡片容器（Card/Stack）；頁首 Banner、Tab bar 應置於 ListView 外。  
   - 刪除 ListView 同層的多餘 Container/Row/文字，只保留卡片樣板。

2) 綁定卡片元件  
   - 圖片：Image Type 改 Network → Path 以 `restaurants Documents > Item at Index > Get Document Property > cover_image_url`。  
   - 標題：Text 綁 `name`。  
   - 副標/類型：Text 綁 `type_display`（或 restaurant_types）。  
   - 其他欄位（需則綁）：`Add`、`Phone`；經緯度欄位暫不顯示。  
   - 若欄位空值，預設值留空，不要用「idiot」等占位字。

3) 測試預覽  
   - Run/Preview，確認卡片重複顯示多筆餐廳資料，圖片與文字對應 Firestore。  
   - 若無資料，檢查 Firestore 欄位命名與型別（圖片為 URL 字串）。

4) 延伸（如有時間）  
   - 建立 philly_corkage_fee 欄位（Double）後再綁定顯示邏輯：0/空顯示 Free，其他顯示 `$fee`。  
   - 詳情頁：若已有頁面，接受 docId/record，綁定同欄位，地圖按鈕開 `https://maps.google.com/?q=lat,lng`。

### 完成定義
- ListView 僅含一個卡片樣板，所有圖片/文字均來自 Firestore，預覽可滾動多筆且載入正常，無占位假字。***