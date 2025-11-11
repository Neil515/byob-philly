# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-11

---

## ✅ 今日摘要
- 調整單一餐廳頁 `BYOB Service` 顯示邏輯，改讀 `byob_service_level` 並支援舊欄位 fallback。
- 更新餐廳列表頁的 `BYOB Service` 程式碼（暫時註解，待啟用）。
- 釐清 LOGO 欄位來源，確認 `_restaurant_logo` 為前台與業者後台的主要顯示依據。
- 檢視 CPT UI 設定，預備將後台選單名稱改為英文。

---

## 🗓️ 明日（2025-11-12）待辦

1. **Placeholder Image 策略實作**  
   - 針對無 LOGO 的餐廳，在 `archive-restaurant.php` 與 `single_restaurant.php` 加入示意圖 fallback。  
   - 規劃示意圖素材與版本管理（依餐廳類型套用）。  
   - 在示意圖上加入「Placeholder Image – Awaiting Official Restaurant Logo」文字。

2. **餐廳排序規則優化**  
   - 盤點目前餐廳列表排序邏輯（`pre_get_posts` 或查詢參數）。  
   - 設計新排序規則（如優先顯示已驗證餐廳、業者已確認項目）。  
   - 在測試環境驗證排序結果，再同步到前台。

3. **業者通知 Email 起草與驗證**  
   - 定義 email 觸發流程（例如資料確認提醒、LOGO 上傳邀請）。  
   - 撰寫英文通知模板並整合現有程式（`restaurant-member-functions.php` 或 Apps Script）。  
   - 寄送測試信件確認格式、連結與內容正確。

---

*最後更新：2025-11-11*  

