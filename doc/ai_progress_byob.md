# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-14）

### **費城 BYOB App — Softr + Adalo**
* ✅ Airtable 仍為唯一來源，Softr 網站可預覽。  
* ⚠️ Softr：內建搜尋/篩選無法共用地圖與列表，改以文案提醒兩組控件各自獨立；全域 Logo 上傳受方案限制，需在導航 block 內上傳。  
* 🚧 新方向：啟動 Adalo 行動版 App（目標 App Store/Play），先用 Mobile Only + Restaurant 範本搭骨架。

---

## ✅ 2025-12-14 — 今日進度摘要
1. **Softr 調整**  
   - 搜尋/篩選改用提示文案，明確「地圖與列表互不影響」。  
   - 確認可用自有網域 `byobmap.com`；全域 Logo 受方案限制，需在導航 block 內上傳。

2. **Adalo 啟動**  
   - 新建專案（Mobile Only，Restaurant 模板），設定品牌名稱。  
   - 熟悉編輯器縮放/畫布操作，準備替換預設頁與資料。

3. **任務規劃**  
   - `Next Task Prompt Byob.md` 更新至 12/15：聚焦 Adalo 資料模型、列表/地圖/詳情骨架、品牌替換與基礎驗證。

---

## 🔭 2025-12-15 — Adalo 待辦（簡述，詳見《Next Task Prompt Byob》）
1. 資料模型：Restaurants（name/address/phone/lat/lng/type/corkage_fee/image/slug）、Types/Tags（多對多），必要時 Users。  
2. 畫面骨架：底部導航；列表頁搜尋+篩選 Chips；地圖頁綁 lat/lng，標記開啟詳情；詳情頁顯示餐廳資訊。  
3. 樣式替換：移除模板咖啡素材，套用品牌色與 Placeholder。  
4. 驗證：Preview 測試列表/地圖/詳情流程，列出匯入與上架需求。

---

## 🕰️ 歷史里程（精簡）
* 2025-12-11：Softr 列表/詳情初版上線，資料匯入 Airtable，全站切換 Softr 資料源。  
* 2025-12-08：`update_1117_restaurants.py` 完成；Glide MVP 驗證後決議改採 Softr。

---

## 📌 操作備忘
* **資料維運**：`philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` 為唯一來源；更新後匯入 Airtable。Adalo 先用小樣本/匯入，後續再決定 Airtable 直連或 API。  
* **Softr**：Logo 請在導航 block 上傳；若需重連，準備 Airtable Personal Access Token + Base share link。  
* **歷史文件**：更多規格與腳本見 `doc/README.md`、`philly_yelp_crawler`。
