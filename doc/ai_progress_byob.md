# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-11-27）

### **費城 BYOB App — 資料輸出與接軌階段**
* ✅ `byob_schema_spec.md` 已完成（現存於 `philly_yelp_crawler/`），明確定義 17 個必備欄位與條件式規則。
* ✅ 新增 `scripts/byob_export.py`，可將 Excel 轉為標準 JSON / CSV，並提供欄位驗證與 issue log。
* ✅ 產出 `data/byob_restaurants.csv` 與 `data/byob_restaurants.json`（85 筆），供 App MVP 直接使用。
* ⚠️ 仍有 26 個欄位缺值（官網、Yelp、電話、兩筆開瓶費金額），依決策暫保留空白並在 issue log 中追蹤。

---

## ✅ 2025-11-27 — 今日進度摘要
1. **Schema 文件化**  
   - 把 Excel 原始欄位映射成 App 端 `snake_case` 欄位，列出枚舉值、相依條件與 CSV/JSON 格式規則。
2. **資料匯出腳本**  
   - `byob_export.py` 內建清洗邏輯（移除多餘空白、拆分多值欄位、處理「待確認」狀態），若未補齊欄位會列出 issue 清單並可選擇 `--allow-issues` 輸出。
3. **JSON / CSV 產物**  
   - 產出路徑：`philly_yelp_crawler/data/byob_restaurants.{json,csv}`，App 端可直接引用；再生產流程與命令已記錄於 README 草稿。
4. **缺值追蹤**  
   - `website_url` 缺 11 筆：1408, 1417, 1569, 1574, 1586, 1727, 1729, 1730, 1734, 1735, 1739  
   - `yelp_url` 缺 7 筆：1398, 1399, 1408, 1558, 1561, 1571, 1576  
   - `phone` 缺 6 筆：1412, 1562, 1567, 1578, 1735, 1755  
   - `corkage_fee_amount` 缺 2 筆：1398, 1399（`philly_corkage_fee = corkage_fee`，暫允許空值）

---

## 🔜 2025-11-28 — 下一步（詳見《Next Task Prompt Byob》）
1. **資料供應管線定稿**  
   - 決定 JSON/CSV 的正式發佈位置與版本控管方式，撰寫再生產流程備忘。
2. **App 資料模型與載入模組**  
   - 建立 TypeScript/後端 DTO 型別，先以靜態 JSON Loader 串入 85 筆資料，處理缺值顯示策略。
3. **後續任務排程**  
   - 整理搜尋／篩選邏輯、資料補齊責任與 2–3 天 backlog，做為 11/29 之後的行動清單。

---

## 📌 持續建議
* 補齊缺值欄位後，重新更新 Excel 並跑 `byob_export.py`（無需 `--allow-issues`）即可得到乾淨資料。
* App 若需更動欄位或新增資料來源，請先更新 `byob_schema_spec.md`，再由腳本輸出新版本，確保所有端點一致。
