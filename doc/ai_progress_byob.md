# BYOB 專案開發進度記錄（更新版）

## 📅 專案概覽

* **專案名稱**：BYOB (Bring Your Own Bottle) 餐廳平台
* **目前階段**：顧客推薦表單系統建立完成，進入欄位映射修正階段
* **核心功能**：餐廳資料管理、BYOB 服務設定、前台展示、SEO、社群與短影音行銷、自動化資料收集、Email/通知系統、顧客推薦表單系統
* **技術架構**：WordPress + ACF + WooCommerce + Rank Math SEO + Google Places API + SendGrid + Google Apps Script + 爬蟲（Grid Search）+ 顧客推薦表單系統

---

## ✅ 2025年9月30日 — 顧客推薦表單系統建立完成

### 🎯 今日目標
建立完整的顧客推薦表單系統，實現「雙軌制」策略中的顧客端收集功能。

### 已完成項目

* [x] **建立顧客推薦 Google 表單**
  * 設計 11 個欄位的表單結構
  * 包含餐廳資訊、開瓶費條件、推薦者聯絡方式
  * 支援條件式顯示邏輯（開瓶費相關欄位）
  * 餐廳類型多選，支援「其他」選項

* [x] **建立 Google Apps Script 處理程式**
  * 基於純淨版結構，確保穩定性
  * 支援「欄位設定表」工作表動態映射
  * 實現條件式欄位處理（開瓶費邏輯）
  * 餐廳類型「排除法」識別「其他」內容
  * 全形轉半形函數處理

* [x] **建立「欄位設定表」工作表**
  * 實現 WordPress ACF 欄位與表單欄位的動態映射
  * 支援欄位名稱變更而不需修改程式碼
  * 包含所有必要欄位的對應關係

* [x] **整合 WordPress REST API**
  * 成功建立餐廳草稿文章
  * 修正 API 端點問題（從 `/customer-recommendation` 改為 `/restaurant`）
  * 補上必填欄位預設值（`contact_person`、`email`、`district`、`phone`）

* [x] **新增 ACF 欄位**
  * `customer_recommender_name`：推薦者姓名
  * `customer_recommender_email`：推薦者 Email
  * 確保顧客推薦資料與餐廳業者資料分離

* [x] **修正必填欄位問題**
  * 補上 WordPress API 需要的必填欄位
  * 使用預設值處理顧客推薦時無法提供的資訊
  * 確保表單提交不會因缺少必填欄位而失敗

### 技術實現細節

**表單欄位設計：**
```
1. 餐廳名稱（必填）
2. 餐廳類型（選填，多選）
3. 餐廳地址（必填）
4. 餐廳電話（選填）
5. 開瓶費（必填，條件式顯示）
6. 開瓶費金額（條件顯示）
7. 開瓶費說明（條件顯示）
8. 酒器設備（選填，多選）
9. 餐廳特色（選填）
10. 您的姓名或暱稱（選填）
11. 聯絡Email（選填）
```

**Apps Script 程式架構：**
```javascript
// 主要函數
function onCustomerFormSubmit(e) { ... }

// 解析函數（基於純淨版結構）
function parseCustomerFormData() { ... }

// 全形轉半形函數
function toHalfWidth(str) { ... }

// WordPress API 發送函數
function sendToCustomerWordPress(data) { ... }

// 通知函數
function sendCustomerNotificationEmail(data, result) { ... }
function sendErrorNotification(error) { ... }
```

**欄位設定表結構：**
```
WordPress 欄位 | 表單欄位名稱
restaurant_type | 餐廳類型 (選填)
address | 餐廳地址(必填)
phone | 餐廳電話(選填)
is_charged | 開瓶費(必填)
corkage_fee_amount | 開瓶費金額
corkage_fee_note | 開瓶費說明
equipment | 酒器設備(選填)
notes | 餐廳特色(選填)
customer_recommender_name | 您的姓名或暱稱
customer_recommender_email | 聯絡Email
```

### 待修正問題

**欄位映射錯誤：**
* 推薦人 Email 出現在原有 `email` 欄位，而 `customer_recommender_email` 欄位空白
* 推薦人名稱出現在 `contact_person`，而 `customer_recommender_name` 欄位空白
* 需要修正 Apps Script 資料發送邏輯，確保正確的欄位對應

### 測試結果

* ✅ 表單提交成功，建立餐廳草稿文章
* ✅ 欄位解析正確，支援條件式邏輯
* ✅ 餐廳類型「排除法」處理正常
* ✅ 必填欄位檢查通過
* ❌ ACF 欄位映射錯誤，需要修正

---

## ✅ 2025年9月29日 — 實作日成果（短影音製作 + 串燒餐廳爬取）

### 🎬 短影音製作（完成）

**今日目標**：完成 1–2 支 30–45 秒素材，用於後續社群發佈與顧客招募引流（9/30 版將加入顧客爆料 CTA 的改版）。

**已完成項目**

* [x] 最終腳本確認（從既有 6 個腳本挑選 1–2 個，今日版不含顧客爆料 CTA）
* [x] 拍攝準備（場地、道具、設備、燈光、服裝）
* [x] 主鏡頭拍攝（多角度/多 take）
* [x] B-roll 補拍（菜色、環境、酒杯手部特寫）
* [x] 初剪完成（節奏與段落）
* [x] 字幕與版面設計（大字標、重點詞）
* [x] 音樂挑選與音量平衡
* [x] 色彩/亮度微調

**輸出規格**

* 解析度：1080x1920（9:16）
* 時長：30–45 秒
* 格式：MP4（H.264 + AAC）
* 版本：V1（不含 CTA）、V1.1（留白位可插入 CTA 字卡）

**待上架素材**（供 9/30 改版與上線）

* 🎥 影片母檔（V1/V1.1）
* 🎞 封面 3 款草案（含標題版）
* 📝 字幕 .srt / 文案草案（標題、Hashtag、Description）

---

### 🍢 串燒餐廳爬取（完成）

**今日目標**：擴充台北市「串燒/燒烤/居酒屋/日式燒烤」店家名單，為後續先收錄與通知打基礎。

**已完成項目**

* [x] 關鍵字設定：串燒、燒烤、居酒屋、日式燒烤
* [x] 搜尋範圍：台北市多中心點網格搜尋已啟用
* [x] 參數與過濾：restaurant/food/establishment；去重規則（name+address+phone）
* [x] 執行與監控：紀錄查詢配額與錯誤重試
* [x] 資料清理：移除重複/關閉店家/資訊不足者
* [x] 欄位補強：嘗試補齊電話、營業時段、類型標籤
* [x] 匯出：Excel/CSV（供「先收錄」流程使用）

**欄位結構（爬取表）**

```
place_id, name, address, district, lat, lng,
phone, opening_hours, rating, user_ratings_total,
categories, price_level, website, google_maps_url,
source=places_api, dedupe_key, fetched_at
```

**品質控制**

* 去重策略：`lower(name) + normalized(address) + digits(phone)`
* 狀態標記：`status = valid | duplicate | closed | incomplete`
* 補充任務：對 `incomplete` 逐步補齊電話/時段/類型

**產出**

* 📦 `exports/2025-09-29_taipei_yakitori.xlsx`
* 🗒 `logs/2025-09-29_search_log.json`（查詢記錄與斷點）

> 備註：實際筆數與錯誤碼統計留存於 logs；若需我可明日回填統計表與可視化（直方圖/熱點地圖）。

---

## 🔄 策略調整 — 「顧客/餐廳雙軌制」

**決議日期**：2025-09-29（實施自 2025-09-30 起）

### 為何調整

* 僅從餐廳端推進回覆慢、摩擦高；Email 觸及與 CTA 點擊受限。
* 顧客端具天然動機：分享與索取名單；形成社群內容與口碑循環。

### 雙軌描述

* **顧客軌（收集驅動）**：

  * 「爆料 BYOB 餐廳」表單 + 抽獎誘因 + 社群排行榜
  * 來源標記：`source=customer_tip`；驗證流程：`verify_status=pending/verified/declined`
  * 每週公布新增名單，建立參與回饋
* **餐廳軌（低摩擦轉化）**：

  * 先收錄精簡卡片 → 通知店家「已免費上架」→ 引導一鍵補資料
  * 來源標記：`source=auto_listed | partner_signup`
  * 追蹤：補資料轉化率、回覆時長、同意政策紀錄

### 9/30 啟動清單（摘要）

* 建立顧客表單（問題：店名/地點/BYOB條件/特色/暱稱/聯絡方式；圖片可選）
* 啟用自動回覆（感謝信＋抽獎規則）並串接到 Google Sheet/Airtable
* 發布社群素材（Reels/貼文/Story 投票）與表單入口
* 依 9/29 爬取結果建立 30 家「先收錄」並寄出通知

### 成功指標（首週）

* 顧客端：提交 ≥ 50 筆、表單完成率 ≥ 60%、社群 CTR ≥ 2%
* 餐廳端：先收錄 100 家覆核完成、通知開啟率 ≥ 25%、補資料轉化 ≥ 15%

---

## 📊 專案整體進度（截至 2025-09-30）

### 新增已完成里程碑（今日）

* ✅ 顧客推薦表單系統建立完成
* ✅ Google Apps Script 處理程式完成
* ✅ 「欄位設定表」工作表建立
* ✅ WordPress REST API 整合完成
* ✅ ACF 欄位新增完成
* ✅ 必填欄位問題修正完成

### 進行中

* 🔄 顧客推薦表單欄位映射修正
* 🔄 表單使用者體驗優化
* 🔄 自動回覆系統設定

### 待辦（明日優先）

* ⏳ 修正 Apps Script 資料發送邏輯
* ⏳ 驗證 ACF 欄位正確填入
* ⏳ 測試表單提交完整流程
* ⏳ 建立資料驗證機制

---

## 📝 今日技術/流程筆記（9/30）

### 顧客推薦表單系統技術筆記

* **Apps Script 架構**：基於純淨版結構，確保穩定性與一致性
* **欄位映射機制**：使用「欄位設定表」工作表實現動態映射，支援欄位名稱變更
* **條件式欄位處理**：開瓶費邏輯支援「不收費」、「酌收」、「其他」三種情況
* **餐廳類型處理**：使用「排除法」識別「其他」內容，自動分類已知/未知類型
* **全形轉半形**：確保表單標題與程式碼查找的一致性
* **必填欄位處理**：補上 WordPress API 需要的必填欄位，使用預設值

### 待修正技術問題

* **欄位映射錯誤**：推薦人資料出現在錯誤的 ACF 欄位
* **資料發送邏輯**：需要修正 Apps Script 發送到 WordPress 的資料格式
* **ACF 欄位對應**：確保 `customer_recommender_name` 和 `customer_recommender_email` 正確填入

### 9/29 技術筆記（保留）

* 影片字幕字體：中文黑體粗 72pt（行高 120%），關鍵字加背景條
* 音量準則：背景樂 -18 LUFS，語音 -14 至 -16 LUFS（避免社群壓縮爆音）
* 版型保留 1.0s CTA 空窗，便於 9/30 插入「爆料表單」連結字卡
* 爬蟲重試策略：`429/5xx` 指數退避（最大 5 次），記錄 `backoff_ms`
* 去重鍵正規化規則：

  * `name`: 移除空白/全半形/特殊符號；
  * `address`: 中文數字統一、路段與巷弄格式化；
  * `phone`: 僅保留數字與區碼

---

## 📁 成果檔案（今日）

### 9/30 顧客推薦表單系統檔案

* `wordpress/Apps script - 顧客推薦版.js`：顧客推薦表單處理程式
* `wordpress/functions.php`：WordPress REST API 端點和 ACF 欄位處理
* 顧客推薦 Google 表單：包含 11 個欄位的表單結構
* 「欄位設定表」工作表：動態欄位映射設定

### 9/29 成果檔案（保留）

* `exports/2025-09-29_taipei_yakitori.xlsx`
* `logs/2025-09-29_search_log.json`
* `videos/V1_main.mp4`, `videos/V1_1_with-cta-slot.mp4`
* `videos/captions/beta_zh-TW.srt`
* `videos/covers/cover_a.jpg`, `cover_b.jpg`, `cover_c.jpg`
* `social/copy/reels_caption_draft.txt`

> 如需我將以上檔名對應你實際資料夾結構，明日可同步一次並補上連結與版本號。

---

## 📌 風險與對策（短期）

### 顧客推薦表單系統風險

* **欄位映射錯誤** → 修正 Apps Script 資料發送邏輯，確保正確的 ACF 欄位對應
* **表單冷啟動量不足** → 以短影音 + Story 投票多入口引流；投放小額廣告測試 CTA
* **名單品質參差** → 驗證欄位與上傳截圖；設 `verify_status` 與審核備註
* **餐廳端回覆慢** → 先收錄降低摩擦；通知模板清楚說明好處與 1 分鐘補資料流程

---

## 🧭 明日（10/1）行動清單（Sprint）

### 優先級 1：修正顧客推薦表單欄位映射

1. **分析 WordPress API 處理邏輯**
   * 檢查 `byob_create_restaurant_post` 函數的參數處理
   * 分析 ACF 欄位更新邏輯
   * 確認 `customer_recommender_name` 和 `customer_recommender_email` 欄位處理

2. **修正 Apps Script 資料發送**
   * 檢查 `sendToCustomerWordPress` 函數的資料格式
   * 修正欄位映射，確保正確的欄位名稱傳送
   * 測試修正後的資料格式

3. **測試與驗證**
   * 提交測試表單資料
   * 檢查 WordPress 後台草稿文章
   * 驗證 ACF 欄位是否正確填入

### 優先級 2：表單優化準備

4. 優化表單使用者體驗（問題順序、說明文字）
5. 建立資料驗證機制（重複餐廳檢查）
6. 準備社群推廣素材
