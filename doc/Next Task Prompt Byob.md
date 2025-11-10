# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-10

---

## ✅ 今日摘要
- 完成費城餐廳類型欄位英文化與資料同步。
- 移除業者後台地址的台灣格式限制，支援海外地址。
- 前台類型顯示改讀 `philly_restaurant_type`，避免顯示中文標籤。

---

## 🗓️ 明日（2025-11-11）待辦

1. **放大單一餐廳頁 LOGO**  
   - 檢查 `single_restaurant.php` 與相關 CSS。  
   - 調整桌機／手機的圖片容器與 `object-fit` 設定，避免失真或溢出。  
   - 驗證前台在不同裝置尺寸下排版正常。

2. **統一餐廳照片欄位來源**  
   - 目標：ACF 後台與業者後台上傳的照片使用同一組 `philly_restaurant_photo` 欄位。  
   - 檢視並更新 `archive-restaurant.php`、`single_restaurant.php`、`restaurant-member-functions.php` 內的存取邏輯。  
  - 測試：在 ACF 與業者帳號各上傳/刪除一次，確認前台同步顯示。

3. **Rank Math 頁面標題調整**  
   - 進入 Rank Math > Titles & Meta，檢查 `restaurant` 文章型別設定。  
   - 若無法 GUI 設定，評估在 `functions.php` 以 Hook 覆寫 `<title>`。  
   - 驗證：重整餐廳列表與單頁，確認瀏覽器分頁顯示英文標題。

---

*最後更新：2025-11-10*  

