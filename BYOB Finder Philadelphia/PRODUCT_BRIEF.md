# BYOB Finder Philadelphia — Product Brief

## 這個產品是什麼

給費城外出用餐者使用的省時間工具。

**核心任務只有一個：**
找到可以自帶酒水的餐廳、確認開瓶費、導航過去。

| 項目 | 值 |
|------|-----|
| UI 語言 | English |
| 平台 | Android first（Google Play），Apple 觀察後決定 |
| 架構 | FlutterFlow + Firebase Firestore + Firebase Storage |
| 後端 | 純 Firebase（不需要 Render） |
| 資料 | 94 家費城 BYOB 餐廳（Firestore `restaurants` 集合） |

---

## 鎖定的產品模型

瀏覽列表 → 餐廳卡片（名稱 + 類型 + 開瓶費）→ 詳情頁（地址 + 電話 + 政策）→ 導航

不要脫離這個模型。

---

## MVP 必填欄位（最少 3 個）

1. 餐廳名稱（`name`）
2. 地址（`Add`）
3. 開瓶費類型（`philly_corkage_fee`：free / corkage_fee / other）

選填（已有資料）：電話、料理類型、Yelp URL、開瓶費金額、酒具、服務等級

---

## P1 核心功能（MVP 必須完成）

1. **餐廳列表頁** — Firestore 資料，卡片顯示：圖片 + 名稱 + 料理類型 + 開瓶費 badge
2. **篩選** — 依料理類型篩選列表
3. **餐廳詳情頁** — 名稱、地址、電話、開瓶費政策、酒具列表
4. **Google Maps 導航按鈕** — 直接開地圖 App，傳入 Latitude/Longitude
5. **開瓶費視覺識別** — Free（綠）/ $X（灰）/ Special policy（橘）三種 badge

## P2（MVP 穩定後）

1. 地圖視圖（所有餐廳在 Google Maps 上顯示）
2. 依距離排序（GPS near me）
3. 餐廳名稱搜尋

## P3（後續版本）

1. 用戶驗證（確認 BYOB 政策仍然有效，帶 timestamp）
2. 附近 Wine Shop 推薦
3. 第二個城市擴張

---

## 明確的非目標

MVP 不做以下任何一項（除非 contract 明確要求）：

- 用戶帳號 / 登入系統
- 用戶評論或評分
- 餐廳訂位或外送
- 社群功能（收藏、分享清單）
- Wine pairing 建議
- 多城市（MVP 階段）
- 推播通知
- 榮譽 / 徽章系統

---

## UX 硬規則

- 開瓶費在卡片層可見，不需要進詳情頁才知道
- 導航按鈕在詳情頁第一屏，不需要滾動
- "Free BYOB" 視覺上比 "Corkage Fee" 更突出
- 沒有登入牆
- 列表載入 ≤ 2 秒

---

## 失敗標準

- 從打開 App 到開始導航超過 3 次 tap
- 開瓶費在列表頁不可見
- 看起來像普通餐廳 App，沒有 BYOB 個性
- 詳情頁需要滾動才能找到導航按鈕

---

## 成功標準

用戶說出以下任何一句話就算成功：

- "This is way easier than searching on Yelp."
- "I didn't know there were this many free BYOB spots in Philly."
- "I use this every time I'm picking a restaurant."
- "Finally an app that tells me the corkage fee upfront."

---

## 已鎖定的產品決策

| 決策 | 結論 |
|------|------|
| 後端 | 純 Firebase（不需要 Render） |
| 資料來源 | Firestore（從 JSON 匯入，WP_Post_ID 作為 document ID） |
| 圖片 | Firebase Storage（從 Mid/IMAGE_CONVERT/ 上傳） |
| 帳號系統 | MVP 不做 |
| 第一平台 | Google Play |
