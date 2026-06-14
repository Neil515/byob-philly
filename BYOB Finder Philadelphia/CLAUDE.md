# BYOB Finder Philadelphia — Claude Rules

產品大原則：
就算是第一次來費城的外地人，也必須能在 30 秒內找到一家可以帶酒的餐廳並開始導航。

UI 語言：English
平台：Android first（Google Play），Apple 觀察 60 天訊號後決定

---

## 設計系統（開發前定稿，不再更動）

| 項目 | 值 |
|------|-----|
| 主色 | #8B2635（酒紅） |
| 背景色 | #F8F4EF（米白） |
| 字型 | Inter |
| 圓角 | 12px |
| 風格 | Urban、Practical、Warm |

FlutterFlow Theme 設定必須在第一個頁面開始前完成。

---

## 這個產品是什麼

BYOB Finder Philadelphia 是讓費城外出用餐者快速找到可以自帶酒水的餐廳、確認開瓶費政策、並直接導航過去的工具。

它不是評論平台，不是訂位工具，不是社群 App。

核心任務只有一個：找到目的地、確認可以帶酒、出發。

---

## 鎖定的產品模型

瀏覽餐廳列表 → 餐廳卡片（名稱 + 類型 + 開瓶費）→ 詳情頁（地址 + 電話 + 政策）→ 導航按鈕 → 出發

不要脫離這個模型，除非 contract 明確要求。

---

## 核心建構規則

1. **一次一個 contract** — 不加 scope 外的功能
2. **解決根本問題** — 不用表面修補應付 contract
3. **每個頁面一個主要工作**
4. **語言優先** — 所有用戶看到的文字必須是英文

允許在 scope 內做的大改動：重寫受影響頁面、移除重複 CTA、簡化層級、替換弱措辭。

---

## 不可妥協的 UX 規則

- 開瓶費資訊必須在列表卡片上可見，不能只在詳情頁
- 導航按鈕必須在詳情頁第一屏，不需要滾動才找得到
- "Free BYOB" 視覺上必須與 "Corkage Fee" 有明顯區別（綠色 vs 灰色 badge）
- 沒有登入牆：未登入可以完整瀏覽所有餐廳

---

## 這些情況算作失敗

- 用戶需要超過 3 次 tap 才能開始導航
- 開瓶費資訊在列表頁不可見
- 列表載入超過 2 秒
- App 看起來像普通餐廳 App，沒有 BYOB 身份識別
- 餐廳詳情頁需要滾動才能找到電話或地址

---

## DONE 前的自我審查

在輸出 DONE 之前，確認：

**A. Contract 成功**
- [ ] 做了要求的改動
- [ ] App 還能運行
- [ ] 核心行為保留了

**B. UX 品質**
- [ ] 主要動作顯而易見
- [ ] 沒有重複的 CTA
- [ ] 開瓶費在卡片上清楚顯示
- [ ] 導航按鈕在第一屏
- [ ] 所有用戶看到的文字是英文

---

## 驗證規則

不跑 Python。驗證方式：
- 在 FlutterFlow 預覽畫面點擊修改後的頁面與流程
- 確認 Firestore 資料正確載入到列表
- 截圖給 Neil 在 Cowork 確認視覺正確後才算完成

---

## 技術架構

- App：FlutterFlow
- 後端：Firebase Firestore（不需要 Render）
- 圖片：Firebase Storage
- 資料：94 家費城 BYOB 餐廳，Firestore `restaurants` 集合
- Firebase 專案 ID：byob-app-5e4db
- Document ID：WordPress WP_Post_ID（1374–1794）

### Firestore 欄位對照（注意大小寫）

| Firestore 欄位 | 說明 |
|---------------|------|
| `name` | 餐廳名稱 |
| `Add` | 地址（大寫 A） |
| `Phone` | 電話（大寫 P） |
| `Latitude` | 緯度（大寫 L） |
| `Longitude` | 經度（大寫 L） |
| `cover_image_url` | Firebase Storage URL |
| `philly_restaurant_type` | 料理類型（american / asian / italian / other 等） |
| `philly_corkage_fee` | 開瓶費類型（free / corkage_fee / other） |
| `corkage_fee_amount` | 開瓶費金額（數字） |

---

## 預設非目標

除非 contract 明確要求，不加：
- 用戶帳號 / 登入系統
- 用戶評論或評分
- 餐廳訂位
- Wine shop 推薦
- 社群功能（收藏清單、分享）
- 多城市支援
- 推播通知
