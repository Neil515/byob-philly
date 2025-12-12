# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-12）

### **費城 BYOB App — Softr MVP / BYOB near you**
* ✅ Airtable 為唯一來源，Softr 列表與詳情已連結資料。  
* ✅ byob_service_level_fixed 已建立（Single select），可用於 Softr Tag 顯示。  
* 🟡 進行中：地圖與列表整合、CTA/篩選、圖片與餐廳類型呈現。  
* ⏳ 待辦：換用 fixed 欄位並刷新 Softr、發布後更新 README/測試紀錄。

---

## ✅ 2025-12-12 — 今日進度摘要
1. **Airtable 欄位清理**  
   - 新增 `byob_service_level_tmp` 公式，將空值/「::- 請選擇 --」轉為 `-`。  
   - 新建 `byob_service_level_fixed`（Single select，含 `-`/basic/self/no/Not specified），批次貼上 tmp 結果。

2. **Softr 顯示檢查**  
   - 詳細頁確認 Tag 類型可用；仍需刷新資料源並將欄位改為 `byob_service_level_fixed`。  
   - 目前畫面暫未切換欄位，待同步後再驗證。

3. **明日起手任務整理**  
   - 12/13 聚焦：圖片/封面補強、餐廳類型與補充顯示（含 fixed 欄位）、發布與紀錄（詳見 Next Task Prompt）。

---

## 🔭 2025-12-13 — Softr 待辦（簡述，細節見《Next Task Prompt Byob》）
1. 圖片/封面：列表與詳情補預設圖、確認裁切比例。  
2. 餐廳類型：使用 `byob_service_level_fixed` Tag，主/補充類型併排顯示，空值不露出「請選擇」。  
3. 發布與紀錄：完成調整後 Publish，更新 README／必要時補記 ai_progress。

---

## 🕰️ 歷史里程（精簡）
* 2025-12-11：Softr 列表/詳情初版上線，資料匯入 Airtable，全站切換 Softr 資料源。  
* 2025-12-08：`update_1117_restaurants.py` 完成；Glide MVP 驗證後決議改採 Softr。

---

## 📌 操作備忘
* **資料維運**：唯一來源為 `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx`；更新後匯入 Airtable，Softr 立即同步。  
* **Softr 連線**：若需要重新授權，準備 Airtable Personal Access Token + Base share link。  
* **歷史文件**：更早任務（schema、匯出腳本、WP 調整）請參考 `doc/README.md` 與 `philly_yelp_crawler` 資料夾。
