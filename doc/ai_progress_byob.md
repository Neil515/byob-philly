# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-11）

### **費城 BYOB App — Softr MVP / BYOB near you 轉換**
* ✅ `Philly BYOB Restaurant.xlsx` 已完整匯入 Airtable，作為 Softr 唯一資料來源。  
* ✅ Softr 建立 `BYOB near you` 頁面：卡片列表含搜尋、餐廳類別 chips，並連結共用詳細頁。  
* 🟡 進行中：地圖與列表整合、CTA（Call / Maps / Yelp）優化、篩選排序與品牌視覺。  
* ⏳ 待辦：完成 Softr 發佈與 README/測試紀錄同步，準備 user testing。

---

## ✅ 2025-12-11 — 今日進度摘要
1. **Airtable 資料庫整理**  
   - 將 `Philly BYOB Restaurant.xlsx` 最新 1794 筆完整匯入 Airtable，欄位（緯經度、corkage、服務層級）整理為 Softr 可用型態。  
   - 確認 Softr data source 使用 Personal Access Token + base share link 正常同步。

2. **Softr 列表頁與篩選**  
   - 建立 `BYOB near you` 頁面，採 Grid cards：顯示名稱/地址/餐廳類別，啟用搜尋列與 restaurant_types chips。  
   - 基礎 UX 調整：修改標題副標、顯示筆數、準備 corkage badge 與 CTA 位置。

3. **詳細頁模板**  
   - 新增 Softr Item details block，呈現 corkage_fee / service_level / wine_service_equipment / 官網 / Yelp。  
   - 建立共用 `Call` 按鈕（tel 協定），並刪除預設 `Edit`；驗證所有餐廳皆套用同一模板。

4. **後續規劃同步**  
   - `doc/Next Task Prompt Byob` 已改為 Softr 待辦（地圖整合、CTA、篩選排序、發佈紀錄）。

---

## 🔭 2025-12-12 — Softr 待辦（詳見《Next Task Prompt Byob》）
1. **Map with list**：加入地圖區塊、統一 tooltip 與品牌色，卡片/Pin 均能進入詳細頁。  
2. **CTA 強化**：卡片與詳情頁新增 Call / Website / Yelp / Google Maps 導航。  
3. **篩選排序**：chips 分組、免開瓶費 toggle、服務層級多選、排序下拉（名稱、最後驗證）。  
4. **發佈紀錄**：完成設定後 `Publish`，並在 README 更新 Softr 測試連結與功能現況。

---

## 🕰️ 2025-12-08 里程碑（精簡紀錄）
* 更新 `update_1117_restaurants.py`，支援 `ONLY_LATLNG`、直接讀寫 Excel。  
* Glide MVP：完成 85 筆資料匯入、Map pin 顯示、卡片操作與 WordPress 驗證邏輯。  
* 後續決議改採 Softr，以提升列表/地圖體驗與行銷彈性。

---

## 📌 操作備忘
* **資料維運**：唯一來源為 `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx`；更新後匯入 Airtable，Softr 立即同步。  
* **Softr 連線**：若需要重新授權，準備 Airtable Personal Access Token + Base share link。  
* **歷史文件**：更早任務（schema、匯出腳本、WP 調整）請參考 `doc/README.md` 與 `philly_yelp_crawler` 資料夾。
