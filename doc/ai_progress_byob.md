# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-13）

### **費城 BYOB App — Softr MVP / BYOB near you**
* ✅ Airtable 為唯一來源，Softr 列表與詳情已連結資料。  
* ✅ byob_service_level_fixed 已建立（Single select），可用於 Softr Tag 顯示。  
* 🟡 進行中：地圖/列表 Search & Filter 共用、圖片/placeholder 呈現優化。  
* ⏳ 待辦：共用篩選完成後發布並更新 README/測試紀錄。

---

## ✅ 2025-12-13 — 今日進度摘要
1. **Airtable placeholder 批次腳本**  
   - 新增 `doc/airtable_placeholder_script.md`：將 `philly_restaurant_type` 正規化為單一類型列表，依類型隨機填入 placeholder URL 至附件欄位，僅補空白圖。  
   - 說明使用 WP 公開圖、支援多張隨機，並保留預設 `other`/`default`。

2. **Softr 地圖與列表配置**  
   - Google Maps block 接上 API key，中心調整至 Philadelphia，啟用標記聚合。  
   - 檢視 Pagination（Pages、每頁 30）、Load more 文案，確認行動版介面；提醒 hover 不適用手機，需用點擊動作。  
   - 討論 Search/Filter 重複問題：建議改用單一（或 Map+List 合併）控件共用；若分開會導致地圖/列表結果不一致。

3. **次日任務同步**  
   - `Next Task Prompt Byob.md` 已更新至 12/14：聚焦地圖/列表共用 Search & Filter、上架前視覺與互動收尾、Publish 並更新 README/ai_progress。

---

## 🔭 2025-12-14 — Softr 待辦（簡述，細節見《Next Task Prompt Byob》）
1. 地圖＆列表共用 Search/Filter：優先採用合併模板或保留單一控件，綁同欄位並測試行動版。  
2. 上架前整理：移除多餘區塊/文案、檢查圖片比例與 placeholder，一致化 CTA；測試 Search/Filter、卡片/標記點擊與分頁，Publish 並更新 README/ai_progress。

---

## 🕰️ 歷史里程（精簡）
* 2025-12-11：Softr 列表/詳情初版上線，資料匯入 Airtable，全站切換 Softr 資料源。  
* 2025-12-08：`update_1117_restaurants.py` 完成；Glide MVP 驗證後決議改採 Softr。

---

## 📌 操作備忘
* **資料維運**：唯一來源為 `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx`；更新後匯入 Airtable，Softr 立即同步。  
* **Softr 連線**：若需要重新授權，準備 Airtable Personal Access Token + Base share link。  
* **歷史文件**：更早任務（schema、匯出腳本、WP 調整）請參考 `doc/README.md` 與 `philly_yelp_crawler` 資料夾。
