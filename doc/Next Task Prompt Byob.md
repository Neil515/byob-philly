# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-11-27

---

## ✅ 今日摘要（2025-11-27）

* 完成 `byob_schema_spec.md`，清楚定義 App 端必備欄位、型別與條件式規則。
* 新增 `philly_yelp_crawler/scripts/byob_export.py`，可一鍵將 Excel 轉為標準化 JSON / CSV，並自動檢查必填欄位。
* 產出 `data/byob_restaurants.csv` 與 `data/byob_restaurants.json` 85 筆資料，並紀錄仍缺的欄位（指定保持空值）。

---

## 🗓️ 明日（2025-11-28）待辦：BYOB App 資料接軌第一步

### 1. 資料供應管線定稿
* **目標**：決定 App 在 MVP 階段如何取得資料（暫時以靜態 JSON / CSV 為主），並訂出版本控管方式。
* **步驟**：
  * 確認 JSON 檔放置位置（暫定 `philly_yelp_crawler/data/byob_restaurants.json`）與同步流程。
  * 撰寫簡短 README／備忘，說明如何重新產出資料（含 `--allow-issues` 使用情境）。
  * 規劃資料更新頻率與 issue log（列出未補欄位，以利後續追蹤）。
* **完成定義**：有文件化的「資料來源 → App」流程，任何人可依文件重新輸出並取得最新版本。

### 2. App 端資料模型與載入模組
* **目標**：把 JSON/CSV 轉成 App 可以直接使用的 Data Module（含 TypeScript 介面或後端 DTO）。
* **步驟**：
  * 依 schema 建立型別 (`Restaurant`, `CorkageInfo`, `EquipmentOption` 等)。
  * 寫一個初版 Data Loader（可先讀本機 JSON），並提供查詢介面。
  * 將缺值策略（官網 / Yelp / 電話 / corkage amount）寫成註解或錯誤提示，方便 UI 顯示「未提供」。
* **完成定義**：App 端可 import Data Module 並取得 85 筆資料；缺值欄位在 UI 有明確處理策略。

### 3. 接續任務排程（預覽 11/29 之後）
* **目標**：先列出在資料載入完成後的下一步（搜尋／篩選邏輯、UI 草稿等），方便你審核後排時間。
* **步驟**：
  * 根據現有欄位，整理搜尋條件清單 + API 參數草稿。
  * 標記需要補資料的欄位與來源（例如 11 筆官網缺失由誰補）。
  * 將這些延伸任務寫成 backlog，供明日會議使用。
* **完成定義**：產出一份 2–3 天的後續任務列表（含優先度與責任分工建議）。

---

> 備註：若 11/28 期間補齊任何缺漏欄位，可直接更新 Excel 並重跑 `byob_export.py`（不加 `--allow-issues`），確保 App 端始終擁有最新且完整的資料。
