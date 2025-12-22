# 🍹 BYOB 專案總覽文檔

## 💡 專案概述

| 城市           | 目標定位               | 目前狀態                                     |
| ------------ | ------------------ | ---------------------------------------- |
| 🇹🇼 臺北 BYOB | 自帶酒水餐廳推薦平台         | 核心系統穩定運作，重點在酒商合作與社群推廣，細節集中於各模組文件。 |
| 🇺🇸 費城 BYOB | Yelp 的 BYOB 專業補充平台 | WordPress 流程完成，Softr 保留預覽；行動端改用 FlutterFlow（原 Adalo/Thunkable 暫停）。 |

---

## 🚀 最新進度（2025-12-22）

### 🌟 今日成果
* 決策：行動端全面改用 Firebase/Firestore，Airtable 退場；FlutterFlow 將以 Firebase 重建列表→詳情 MVP。
* 已重寫《Next Task Prompt Byob.md》：12/23 開始新專案或複製清空，建立 Firestore 集合 `restaurants`（Name、cover_image_url、type_display、Phone、Add、Latitude、Longitude），列表/詳情改綁 Firestore。
* 準備資料匯入：從 Airtable 匯出 CSV，轉 JSON（pandas `to_json`）供 Firestore 匯入；以文字欄位 `cover_image_url` 為主，附件連結過期可忽略。

### 🗓️ 下一步（2025-12-23，詳見《Next Task Prompt Byob.md》）
1. 新建/複製 FlutterFlow 專案，只保留 Firebase 資料源。  
2. Firestore 匯入 `restaurants` 資料（CSV/JSON）。  
3. 列表：ListView 綁 Firestore，顯示 cover_image_url / Name / type_display，On Tap 傳 docId。  
4. 詳情：用 docId 取單筆，顯示欄位；導航開 `https://maps.google.com/?q=lat,lng`。  
5. 清除所有 Airtable API/綁定、Page Params、JSON Path；Run/Preview 確認無 Airtable 錯誤。

---

## 🌊 技術架構

```
資料收集層 → 表單驗證 → WordPress/ACF → 地圖 & UX → 餐廳聯絡 → 社群/Email
  • Yelp/Google 爬蟲          • 推薦/接管表單        • ACF 自訂欄位       • Google Maps JS API
  • 智能去重/去重標記        • Apps Script 自動化    • REST API / WP-CLI   • 自帶酒水地圖互動
  • 酒商/Email 搜尋工具      • Token 接管流程        • 榮譽/徽章系統       • SendGrid A/B + 社群節奏
```

---

## 📊 專案進度摘要

### 臺北 BYOB

* ✅ 核心模組（表單、審核、抽獎、重複檢查、推播）穩定運作。
* 🔄 持續：酒商合作邀約、社團推廣、KPI 追蹤。
* ⏳ 待辦：自動回覆、儀表板優化（詳情見各模組文件）。

### 費城 BYOB

* ✅ 完成：餐廳資料收集、雙表單整合、餐廳接管流程、WordPress 地圖/定位、驗證徽章、Email 搜尋/批次寄送。
* 🔄 進行：Softr 維持可預覽；行動端重建（FlutterFlow + Firebase）；SendGrid 第二封、社群節奏。
* ⏳ 後續：Firebase 版本 MVP 穩定後考慮上架；榮譽系統、Wine Shop 合作、創始成員計畫、其他城市擴張。

---

## 📂 核心文件與工具

* `doc/philly_byob_complete_plan.md`：賽城專案完整實施計畫。  
* `doc/Next Task Prompt Byob.md`：每日任務規劃（最新至 2025/12/23 Firebase 重建）。  
* `doc/ai_progress_byob.md`：進度日誌（最新至 2025/12/22 Firebase 決策與準備）。  
* `philly_yelp_crawler/update_1117_restaurants.py`：官網 / Yelp / 經緯度 / Email 批次補齊腳本（支援 `ONLY_LATLNG`）。  
* `philly_yelp_crawler/byob_schema_spec.md`：App 欄位規格表；`philly_yelp_crawler/scripts/byob_export.py`：資料匯出腳本。  
* `philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx` / `byob_restaurants.json`：資料來源（改供 Firestore 匯入）；Airtable 不再作為行動端來源。  
* `wordpress/archive-restaurant.php`、`byob_is_restaurant_complete()`：前端展示與完整性檢查邏輯（WordPress 版本）。  
* Softr 網站：`BYOB near you`（地圖 + 詳情，搜尋/篩選各自獨立，文案提示）。

---

## 🔭 即將聚焦（短期）

1. FlutterFlow + Firebase：重建列表→詳情 MVP，資料綁 Firestore，清除 Airtable 依賴。  
2. Softr：維持可預覽，搜尋/篩選分離並保留提示；必要時更新文案與資料。  
3. SendGrid 第二封 Email 與社群節奏；資料維運 SOP（Excel/JSON → Firestore，同步前端）。

---

## ⚙️ 持續挑戰與對策

* **餐廳聯絡資料蒐集**：善用兩階段 Email 搜尋與 takeover token，必要時人工補完，並以 issue log 管理缺欄位。  
* **地圖體驗與資料品質**：Softr 需自訂地圖 + CTA；透過 Airtable 欄位與資料清理維持體驗一致。  
* **社群/內容節奏**：維持固定社群貼文節奏與 CTA，並配合 SendGrid 節奏推送最新餐廳。

---

*最後更新：2025-12-22*  
*版本：v34.0*  
*下一步：FlutterFlow + Firebase 列表→詳情 MVP；匯入 Firestore，清除 Airtable 綁定；Softr 保持預覽*
