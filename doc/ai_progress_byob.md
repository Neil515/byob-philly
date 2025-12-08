# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-08）

### **費城 BYOB App — Glide MVP / BYOB near you 階段**
* ✅ `Philly BYOB Restaurant.xlsx` 為唯一資料來源，匯入後同步 `BYOB restaurant 12.03` Google Sheet。  
* ✅ Glide App already running：列表視圖完成基本卡片、電話／Yelp 操作、權限鎖定。  
* ✅ Map 分頁可讀取 85 筆緯經度（使用 Template → Location 欄位）。  
* 🟡 待完成：使用者定位 / 距離排序、chips 篩選、實機 PWA 測試與分享。

---

## ✅ 2025-12-08 — 今日進度摘要
1. **資料維運腳本更新**  
   - `philly_yelp_crawler/update_1117_restaurants.py` 的 `INPUT_FILE` 改為直接讀寫 `Philly BYOB Restaurant.xlsx`。  
   - 新增 `ONLY_LATLNG` 環境變數：設 `1` 時僅回填經緯度欄位，跳過官網 / Yelp / Email。  
   - 重新跑腳本流程：設定 `GOOGLE_API_KEY`、`GOOGLE_CUSTOM_SEARCH_API_KEY`、`GOOGLE_CUSTOM_SEARCH_CX`，搭配 `TARGET_DATE_FILTER` 只處理 2025/12/06 新增的 8 筆。

2. **Glide App 基礎建置**  
   - 以 `BYOB restaurant 12.03` Google Sheet 為資料源，匯入 85 筆餐廳。  
   - 卡片列表：關閉新增/編輯/刪除、Title=Name、Description=Address、Meta=Phone / Yelp，確保顯示整潔。  
   - Map Location：新增 Template 欄將 `latitude,longitude` 組合成字串並轉為 Location 型別，Map 分頁成功顯示 pin。  
   - 另建「BYOB near you」分頁，採 Details layout：上方 Map（Minimal 樣式）、下方列表待加入篩選。

3. **WordPress 顯示調整（配合資料）**  
   - `byob_is_restaurant_complete()` 只要求名稱 / 地址 / `philly_corkage_fee`；Elma 無電話仍顯示於列表。  
   - 確認 `archive-restaurant.php` 顯示 verified 卡片正常。

---

## 🔜 2025-12-09 — 下一步（詳見《Next Task Prompt Byob》）
1. **Glide 使用者定位 + 距離計算**  
   - User Profiles 新增 `user_location`；按鈕觸發 `Set columns → Current location`。  
   - 建立 Distance computed column，卡片顯示距離並提供「距離 < 3 公里」篩選 / 排序。  
2. **Map / Cards 視覺與篩選**  
   - Map Tooltip 顯示名稱、類別、corkage；Inline List 加入 chips（類別、免 corkage、官網 / Yelp）。  
   - 行動裝置實測地圖縮放與列表互動。  
3. **PWA 測試與分享**  
   - 實機安裝（Add to Home Screen），檢查定位權限、距離 fallback。  
   - 產生公開連結並記錄於 doc/README，供 12/09 測試使用。

---

## 📌 操作備忘
* 重新產出資料：更新 Excel → 以 Google Sheet 匯入 → Glide 自動同步；若僅需補經緯度，設定 `ONLY_LATLNG=1` 後執行 `update_1117_restaurants.py`。  
* Glide Map 目前無內建使用者定位顯示，需透過 User Profiles + Distance 欄位自行儲存 / 計算。  
* 若需回更舊任務（schema、匯出腳本），請參考 `doc/README.md` 的「核心文件與工具」區段。
