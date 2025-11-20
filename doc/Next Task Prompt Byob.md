# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-20

---

## ✅ 今日摘要（2025-11-20）

### 🔗 單一餐廳頁外部連結更新
- Yelp 區塊下方新增「Website / Social」欄位：依序顯示官方網站與多個社群連結，無資料時自動隱藏。
- 新增社群欄位解析邏輯，支援單一 URL、逗號/換行清單與 ACF Link/Reapter 格式。
- 所有連結採淺藍色樣式，與頁面底部導航一致。

### 🏷️ 餐廳類型篩選優化
- `byob_get_all_restaurant_type_terms()` 會計算每個類型的實際出現次數並依人氣排序。
- 新增固定展示的預設類型（包含 Steakhouse、Vegetarian/Vegan、Indian、Spanish 等），即使目前零筆餐廳也能顯示按鈕。
- 列表篩選 UI 仍將已選類型優先顯示，其餘按出現次數排序。

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

## 🗓️ 明日（2025-11-21）待辦

### 1. Reddit 貼文活化
- **盤點現況**：整理既有 Reddit 貼文（r/philadelphia、r/PhillyFood 等）的互動狀態，列出需回覆的留言與潛在跟進對象。
- **互動節奏**：撰寫 2-3 則範例回覆/更新貼文，強調進度（網站更新、餐廳接管成果）、詢問讀者想知道的 BYOB 主題。
- **任務清單**：為下一波推文建立甘特（時間、子任務、所需素材），同時擬定 cross-post or AMA 計畫，避免與餐廳招募衝突。
- **驗收**：完成回覆並記錄截圖、互動數；更新 `ai_progress_byob.md` 或專用追蹤表。

### 2. 第二封餐廳 Email 草擬
- **定位**：定義這封信的觸發情境（例如：第一次邀請後 3-5 天未回應、資料部分補齊、提醒上傳照片）。
- **內容架構**：包含「感謝／提醒」「平台價值」「下一步操作（後台登入、FAQ、客服）」「CTA」；草擬英文主體並附中文註記。
- **素材準備**：列出需要插入的動態欄位（餐廳名稱、接管連結、前台頁面、客服 Email），確定由 SendGrid 或 Apps Script 發送時的代碼欄位。
- **檢查重點**：文案 tone & manner、加入 unsub / 偏好設定說明、測試郵件 placeholder，並在 `message_and_form/` 內建立對應草稿檔。

---

*最後更新：2025-11-20*  

