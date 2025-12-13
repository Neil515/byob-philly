# 🍹 BYOB 專案工作規劃

## 🗓️ 當前日期：2025-12-13

---

## ✅ 今日摘要（2025-12-12）

* Airtable 調整 `byob_service_level` 顯示欄位（fixed/select），準備在 Softr 換用。
* Softr 詳細頁已驗證 tag 顯示正常，待資料源刷新後切換新欄位。
* 介面調整暫停，明日續作。

---

## 🗓️ 明日（2025-12-14）待辦：地圖/列表共用搜尋＆上架前整理

### 1. 地圖＆列表共用 Search/Filter
* **目標**：地圖與列表使用同一組搜尋/篩選，避免重複操作。
* **步驟**：
  * 優先考慮改用「Map + List」合併模板；若保留現有區塊，關閉列表區的獨立 Search/Filter，改由上方地圖區控件（同一 Data source/view）。
  * 綁定同欄位（Search：Name/Address/Phone；Filters：`philly_restaurant_type`、`philly_corkage_fee` 等），測試預覽確認地圖與列表同步更新。
  * 行動版檢查：篩選收合/Chips 排版、Search placeholder 說明「搜尋餐廳/地址」。
* **完成定義**：一組 Search/Filter 即可同時影響地圖標記與列表卡片，行動端體驗正常。

### 2. 上架前整理與發布
* **目標**：完成最後視覺與互動檢查，發布可供測試。
* **步驟**：
  * 版面收尾：移除多餘 block／預設文案，保留單行 CTA（如需要）；列表圖片比例檢查，Placeholder 顯示一致。
  * 實機預覽（桌機/手機）：測試 Search/Filter、點擊卡片/標記開啟詳情、Load more/分頁。
  * Publish 並在 `doc/README.md` 更新最新測試連結與狀態，必要時同步 `ai_progress_byob.md`。
* **完成定義**：新版已發布、主要互動無阻塞，README 已更新連結與簡要變更。

---

> 備註：若需更新資料，只要覆蓋 `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` 並重新匯入 Google Sheet，Glide 會即時同步；經緯度更新可透過 `update_1117_restaurants.py`（搭配 `ONLY_LATLNG=1`）批次完成。
