# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-12-08

---

## ✅ 今日摘要（2025-12-08）

* 完成 `BYOB restaurant 12.03` Google Sheet 建置，並以 Glide 建立第一版卡片列表（匯入 85 筆資料）。
* 成功在 Glide 中建立 `Location` 欄位（以緯經度 Template 組合），地圖分頁可顯示所有餐廳 pin。
* 清理列表介面：移除新增/編輯/刪除權限、設定卡片欄位（名稱、地址、電話/Yelp）。
* 開始拆分「BYOB near you」分頁：地圖置頂、列表置底，等待加入使用者定位與篩選邏輯。

---

## 🗓️ 明日（2025-12-09）待辦：Glide「BYOB near you」體驗強化

### 1. 使用者定位與距離計算
* **目標**：讓使用者可寫入自己的位置，並在列表/地圖顯示距離。
* **步驟**：
  * 在 User Profiles 表新增 `user_location` 欄位，按鈕觸發 `Set columns → Current location`。
  * 新增 `Distance` computed column（user_location ↔ 餐廳 location），以公里顯示。
  * 在卡片上顯示距離並允許依距離排序；建立「距離 < 3 公里」快速篩選。
* **完成定義**：使用者點一次定位按鈕即可看到與自己距離，並可依距離排序/篩選列表。

### 2. 地圖與列表視覺調整
* **目標**：讓 Glide 分頁視覺與網站一致（地圖置頂、卡片列表含篩選）。
* **步驟**：
  * Map 元件設定 Tooltip 內容（名稱 + 類別 + 開瓶費），統一品牌色。
  * 下方 Inline List 改為 Cards，加入 chips（餐廳類別、免開瓶費、官網/Yelp）。
  * 關閉 `+ Add`，替換 `...` 動作為「查看詳情 / 開導航 / 拨號」。
* **完成定義**：手機預覽時，上方為可縮放的地圖，下方為可篩選的卡片列表，操作流程與網站一致。

### 3. 測試與分享
* **目標**：確保 PWA 體驗正常，並提供測試連結。
* **步驟**：
  * 在實機（iOS / Android）測試定位權限、距離計算與 chips 篩選。
  * 透過 `Share → Public link` 產生 URL，記錄於 doc/README 或 Slack 備忘。
  * 若使用者未允許定位，提供 fallback（顯示提示或距離欄隱藏）。
* **完成定義**：實機測試通過並有可分享連結，連同定位 fallback 行為已確認。

---

> 備註：若需更新資料，只要覆蓋 `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` 並重新匯入 Google Sheet，Glide 會即時同步；經緯度更新可透過 `update_1117_restaurants.py`（搭配 `ONLY_LATLNG=1`）批次完成。
