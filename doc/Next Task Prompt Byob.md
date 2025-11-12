# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-12

---

## ✅ 今日摘要
- 前台列表與單頁已支援 ACF `restaurant_logo` 欄位，LOGO fallback 讀取順序調整完成。
- ACF 新增 `Latitude` / `Longitude` 欄位，並建立 `geocode_restaurant_locations.py` 腳本準備批次轉換地址為座標。
- 腳本已能讀取 `Name/Add` 欄位與 `.env` API Key，待調整 API 查詢與結果驗證以取得成功配對。

---

## 🗓️ 明日（2025-11-13）待辦

1. **批次產出餐廳經緯度資料**  
   - 調整 `geocode_restaurant_locations.py` 查詢策略（Places/Geocode API 比對邏輯、失敗重試）。  
   - 以小量測試確認成功率後跑完整名單，輸出 `Latitude`、`Longitude`、`Geocode_Status`。  
   - 彙整失敗名單，整理待人工修正的地址。

2. **經緯度寫回 WordPress / ACF**  
   - 將成功筆數匯入 ACF `Latitude` / `Longitude` 欄位（可先用 REST API 或 WP-CLI 測試寫入流程）。  
   - 隨機抽查頁面確認座標已正確儲存，確保後台可手動覆寫。

3. **附近 BYOB 餐廳功能實作**  
   - 在 `functions.php` / REST API 建立距離排序查詢（Haversine，支援 `lat/lng/radius` 參數）。  
   - 更新前台列表頁：新增「Find BYOB Near Me」入口、顯示距離與 fallback UI。  
   - 處理拒絕授權或無座標案例（提示改輸入 ZIP code 或顯示預設排序）。

---

*最後更新：2025-11-12*  

