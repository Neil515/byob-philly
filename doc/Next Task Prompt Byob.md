# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-19

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

## 🗓️ 明日（2025-11-17）待辦

### 1. 餐廳業者 Email 建立與驗證
- 產出並確認「業者歡迎/啟用」Email 英文模板（必要時雙語附註）
- 觸發時機：完成註冊、資料儲存更新、審核通過
- 內容要點：平台定位、後台編輯入口、資料完整度指引、上傳照片 CTA、聯絡窗口
- 技術項目：
  - 新增/更新寄送函數（WordPress）
  - 寄送測試（實寄 + 截圖留存）

### 2. 檢查餐廳業者後台（My Account）
- 表單欄位與邏輯巡檢：
  - 已切換 philly 欄位（`philly_corkage_fee`、`corkage_fee_amount`、`corkage_fee_note`）
  - 顯示切換：`corkage_fee` 顯示金額、`other` 顯示說明
  - 資料回填/儲存是否正確（含必填驗證）
- 權限與導覽：角色/菜單/返回入口
- 錯誤狀態與提示文案

### 3. 「先網友推薦，後餐廳加入」的資料流檢視
- 流程盤點：網友推薦 → 審核/去重 → 產草稿 → 邀請業者 → 業者接管/補完資訊
- 核對關鍵欄位的來源與覆寫規則（避免覆蓋正確資料）
- 事件觸發：完成註冊後自動關聯既有餐廳（或提供搜尋連結機制）
- 通知串接：推薦者通知、業者通知、管理員監控清單

### 4. 列表「餐廳類型」點選篩選（前台 UX）
- 需求：點選列表卡片上的類型標籤，即以該類型為條件重新載入列表
- 技術方案：
  - 以 URL query 帶入類型（支援多值），前端讀取後應用現有排序/過濾邏輯
  - 切換時保留既有排序、分頁、定位設定（可寫入/讀取 `sessionStorage`）
- 驗收：桌機/手機可用、與 Nearby/排序不互斥、可清除篩選

---

## 🗓️ 明日（2025-11-18）待辦

### 1. 2025-11-16 餐廳 Email 爬取
- 從 `Philly BYOB Restaurant_with_websites_merged_20251116.xlsx` 篩出 `Date = 2025-11-16` 餐廳
- 使用既有 `philly_email_searcher.py` / `philly_email_extractor.py` 流程取得官方網站與 Email，結果格式需與 `Philly BYOB Restaurant_with_websites_20251104_142325_with_emails_20251106_114433.xlsx` 相同
- 無官方網站或未抓到 Email 的欄位保留空值（或依流程填入 `no_website` / `not_found` 狀態），完成後將資料 append 到既有 Email 清單檔案末端

### 2. 餐廳經緯度補錄
- 針對 `Philly BYOB Restaurant_with_websites_merged_20251116.xlsx` 仍缺 `Latitude/Longitude` 的餐廳進行補抓
- 沿用現有 Google Places / Geocode 查詢邏輯（`Name + Address + Philadelphia, PA`）；只更新空白欄位
- 寫回同檔，並確認欄位位置仍在 `Yelp_URL` 之後

### 3. 批次發送 Email 給業者
- 匯出已取得 Email 的餐廳名單（含餐廳名稱、Email、前台連結、接管連結）
- 使用既有 Gmail/Apps Script 模板，填入接管連結與前台連結後批次寄送
- 完成後在清單標註寄送日期與狀態，保留實際寄出的樣本

### 4. `philly_yelp_crawler` 資料夾整理
- 移除無用或重複腳本、測試檔案與舊 log，保留正式使用的工具
- 調整資料夾結構與 README，標註每個脚本的用途與輸入/輸出檔
- 確認 `data/` 內檔案命名一致、移除過期中繼檔

---

## 🗓️ 明日（2025-11-19）待辦

### 1. SendGrid 批次發信
- **資料整備**：使用 `takeover_tokens_20251118_with_emails.csv`（或最新 CSV），確認欄位含 Restaurant、Restaurant URL、Takeover Link、Email_1~n。
- **API 與 Sender**：在 `.env`/系統環境設定 `SENDGRID_API_KEY`，並確認 `team@byobphilly.com` 已完成 Domain 或 Single Sender 驗證。
- **腳本更新**：擴寫 `philly_yelp_crawler/sendgrid_test.py` → 讀完整 CSV、逐筆寄送、對同餐廳多 Email 以 `to_emails` 或 `bcc` 全數寄出，加入節流與 retry。
- **安全流程**：支援 `--dry-run` 參數、寫入寄送 log（包含 HTTP status 與失敗原因），寄前先跑前兩筆到測試信箱確認。
- **稽核**：完成後輸出成功/失敗報表，附寄送時間、信件主旨、Takeover Link 供後續追蹤。

### 2. 餐廳 Logo 圖片補齊
- **清單**：比對 WordPress `restaurant` posts 與 Excel 是否缺少 Logo，產出需補圖名單。
- **素材處理**：向餐廳索取或從網站擷取 Logo，統一裁切為 600x600 PNG（透明背景），命名為 `restaurant-slug-logo.png`。
- **上傳與綁定**：批次上傳到 `wp-content/uploads/byob-logos/`（或媒體庫），並將檔案 ID 寫入對應 post meta/ACF 欄位。
- **前端顯示**：在 `archive-restaurant.php`、`single-restaurant.php` 引用 Logo，未提供時顯示預設圖；確認 RWD 與 Lazy-load 設定。
- **驗收**：抽查 5 筆（含手機/桌機），確保圖片載入正常且通過 Lighthouse 尺寸/壓縮建議。

### 3. 餐廳類型篩選優化
- **資料面**：確定 `restaurant_type` taxonomy 或 meta 值完整；必要時從 `Philly BYOB Restaurant_with_websites_merged.xlsx` 匯入並去重。
- **UI/UX**：在前台列表新增多選類型篩選（pill 或抽屜式），操作後更新 URL query（例：`?types=italian,seafood`），提供一鍵清除。
- **後端邏輯**：調整 REST API 或 `WP_Query`，解析 `types` 參數並以 `tax_query` 過濾，同時與分頁、排序、定位共存；必要時加 transient cache。
- **QA**：測試多種組合、空結果、重新整理後狀態是否保留，並記錄效能指標（查詢時間 < 500ms）。
- **文件化**：更新開發筆記/README，說明欄位來源、前端操作、後端 query 與新增設定。

---

## 🗓️ 明日（2025-11-20）待辦

### 1. 單一餐廳頁顯示官方網站
- 盤點 `single_restaurant.php` 及 ACF 欄位，確認可用的 Website / Google 表單連結欄位；若無資料使用 `restaurant_url`、`website` 或 `map_link` 作為 fallback。
- 在標題或 Contact 區塊新增「Visit Website」按鈕，採 `target="_blank"`、`rel="noopener"`，同時提供 icon 與 hover 狀態。
- 行動版需維持易點擊（44px 高度），並確認沒有資料時隱藏整行，避免留空。

### 2. 替換餐廳 Placeholder 圖片
- 從待補清單挑選優先餐廳（例如曝光高或首頁推薦），取得品牌照/菜色照並裁切為 3:2 或 1:1 尺寸，保留 <200KB。
- 上傳至媒體庫後記錄檔名（建議 `restaurant-slug-cover.jpg`），在 `archive-restaurant.php` / `single_restaurant.php` 的 ACF meta 中更新 attachment ID。
- 清除對應快取、重新載入列表抽查至少 5 筆（桌機+手機），確認 Lazy-load 與 fallback 正常。

### 3. 媒體庫整理
- 依日期/用途篩出舊 Placeholder、重複或未引用的影像，建立命名規則（`yyyy-mm-desc.ext` 或 `restaurant-slug-*`）後進行批次重新命名。
- 將正式素材、Placeholder、Logo 各自放入對應資料夾或使用 Media Tags/分類，方便後續檢索。
- 刪除不再使用的測試檔，並匯出整理前/後清單（Excel 或 Notion），做為日後上傳規範。

---

*最後更新：2025-11-19*  

