# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-15

---

## ✅ 今日摘要（2025-11-15）

### 🗺️ 地圖標記圖標優化與 Attribution 添加
- **地圖標記圖標調整**：
  - 將自定義 SVG 圖標（`placeholder.svg`）尺寸從 64x64 調整為 32x32 像素（默認）
  - 高亮圖標尺寸從 72x72 調整為 40x40 像素
  - 調整錨點位置以確保圖標正確對齊地圖位置
  - 修改文件：`wordpress/assets/js/byob-nearby.js`

- **Attribution 添加**：
  - 在地圖下方添加圖標來源 attribution
  - 內容：`Wine icons created by surang - Flaticon`
  - 連結到：`https://www.flaticon.com/free-icons/wine`
  - 樣式：小字體（0.75rem）、靠右對齊、灰色文字
  - 懸停效果：變為品牌色並顯示底線

- **間距調整**：
  - 增加地圖與 "Closest 5 Restaurants" 之間的間距
  - 將 `.byob-nearby-wrapper` 的 `margin-top` 從 24px 調整為 40px

- **修改文件**：
  - `wordpress/archive-restaurant.php`：添加 attribution HTML 和 CSS 樣式

---

## 🗓️ 明日（2025-11-16）待辦

### 1. **發給餐廳的 Email 優化**
   - **需求確認**：
     - 確認 Email 發送時機（餐廳註冊時、資料更新時、驗證通過時等）
     - 確認 Email 語言（英文、中文、或雙語）
     - 確認 Email 內容重點（歡迎訊息、資料確認、後續步驟、CTA 等）
   
   - **實作項目**：
     - 檢視現有的餐廳通知 Email 模板：
       - `wordpress/functions.php` 中的 `byob_send_approval_notification()` 函數
       - `wordpress/functions.php` 中的 `byob_send_welcome_email()` 函數
       - `wordpress/Apps script - 費城餐廳確認版.js` 中的 `sendPhillyOwnerNotificationEmail()` 函數
     - 草擬新版 Email 內容，納入：
       - 歡迎加入 BYOB 平台
       - 資料確認與補充說明
       - 排序邏輯說明（驗證狀態、完整度、照片的重要性）
       - 鼓勵上傳餐廳照片與補充完整資訊
       - 提供後台編輯連結或說明
     - 更新相關 Email 模板函數
     - 測試 Email 發送流程與內容顯示

### 2. **餐廳業者與既有文章建立連結功能**
   - **需求說明**：
     - 當已經有網友推薦的餐廳文章存在時，餐廳業者註冊後需要能夠與既有文章建立連結
     - 目前情況：餐廳業者註冊後，如果沒有關聯的餐廳，後台會顯示「You currently have no associated restaurants」
     - 需要實作：讓餐廳業者可以在後台搜尋並連結到既有的餐廳文章
   
   - **實作項目**：
     - **後台頁面修改**：
       - 修改 `wordpress/woocommerce/myaccount/restaurant-profile.php`
       - 當餐廳業者沒有關聯餐廳時，顯示「連結既有餐廳」功能
     - **搜尋功能**：
       - 實作餐廳搜尋功能（依餐廳名稱、地址等）
       - 顯示搜尋結果列表（餐廳名稱、地址、狀態等）
       - 允許業者選擇要連結的餐廳
     - **連結功能**：
       - 實作連結確認機制（防止誤連結）
       - 更新 `_restaurant_owner_id` 和 `_owned_restaurant_id` meta
       - 發送通知給管理員（如有需要）
     - **權限檢查**：
       - 檢查餐廳是否已有其他業者連結
       - 檢查餐廳狀態（是否已發布）
       - 驗證業者身份（email 是否與餐廳資料匹配）
     - **相關文件**：
       - `wordpress/restaurant-member-functions.php`：可能需要新增連結相關函數
       - `wordpress/functions.php`：可能需要新增搜尋 API endpoint

---

*最後更新：2025-11-15*  

