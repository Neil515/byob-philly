# BYOB Finder Philadelphia — Progress Handoff

最後更新：2026-06-14

---

## 1. 目前在哪裡停下來

**階段：Pre-A + Pre-B 完成，明天執行 Contract 1**

### Firebase 狀態

| 項目 | 狀態 |
|------|------|
| Firebase 專案 "BYOB APP" | ✅ 存在（byob-app-5e4db） |
| Firestore `restaurants` 集合 | ✅ 94 筆資料，欄位已驗證 |
| Service account JSON | ✅ 在 BYOB/ 根目錄，已加入 .gitignore |
| Firebase Storage | ✅ 94 張 placeholder 圖片已上傳 |
| cover_image_url | ✅ 94/94 筆已更新為 Firebase Storage URL |

### FlutterFlow 狀態

| 項目 | 狀態 |
|------|------|
| FlutterFlow 專案名稱 | BYOB Philly |
| FlutterFlow 專案 ID | b-y-o-b-philly-a08xby |
| FlutterFlow workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |

---

## 2. 下一步工作（依序執行）

### ⏳ Pre-A：Firebase Storage 圖片遷移

**觸發條件：** Claude Cowork 協助寫腳本 + 執行
**阻擋：** Pre-A 完成前，FlutterFlow 列表頁圖片無法正常顯示

圖片來源：`Mid/Placeholder/IMAGE_CONVERT/*.webp`（本機已有）

步驟：
1. ✅ 腳本已寫好：`BYOB Finder Philadelphia/upload_images_to_firebase.py`
2. 執行腳本：
   ```
   cd C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB
   pip install firebase-admin
   python "BYOB Finder Philadelphia/upload_images_to_firebase.py"
   ```
3. 確認 94 筆 cover_image_url 全部更新為 Firebase Storage URL

**風險：** byobmap.com 下線後，90% 列表圖片全部失效。上架前必須完成。

---

### ⏳ Pre-B：建立 FlutterFlow 專案

**觸發條件：** Pre-A 完成後，或與 Pre-A 並行
**執行者：** Neil 手動操作

步驟：
1. 進 [FlutterFlow Dashboard](https://app.flutterflow.io) 建立新專案，命名 **"BYOB Finder"**
2. 記下 Project ID（URL `/project/` 後面那段）
3. 在 Terminal 執行：`flutterflow ai init byob-finder --project [project ID]`
4. 把 `CLAUDE.md`、`PRODUCT_BRIEF.md`、`CLAUDE_PROMPT_TEMPLATE.md`、`Handoff.md` 複製進 workspace 資料夾
5. 把 FlutterFlow Project ID 填回這份 Handoff 的技術詳情欄位

---

### ⏳ Contract 1：FlutterFlow Theme + Firebase 連接 + 列表頁基礎

**觸發條件：** Pre-A + Pre-B 都完成 ✅
**執行者：** Claude Code (Ralph)
**明天第一項工作：把下面的 build prompt 貼給 Ralph，workspace 路徑：`C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly`**

---

#### Contract 1 Build Prompt（複製貼給 Ralph）

```
先讀 CLAUDE.md、PRODUCT_BRIEF.md，再開始。

Active contract:
Contract 1: FlutterFlow Theme 設定 + Firebase 連接 + 列表頁基礎綁定

Goal:
現在 App 是空的。用戶打開後看不到任何餐廳。
完成後用戶打開 App 能看到一個有品牌感的餐廳列表，
每張卡片顯示圖片、餐廳名稱、料理類型、開瓶費 badge，
視覺上立刻知道這是 BYOB 專屬工具，不是普通餐廳 App。

FlutterFlow workspace folder:
C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly

Before making any changes, state your planned approach in 3–4 lines.
Then implement.

Scope（可以改的）:
- FlutterFlow Theme（顏色、字型、圓角）
- Firebase / Firestore 連接設定
- 首頁列表頁（RestaurantListPage 或等效頁面）
- 餐廳卡片元件

Allowed changes inside scope:
- 重寫整個列表頁
- 重建餐廳卡片元件
- 設定 Theme 顏色、字型、圓角
- 建立 Firestore query

Constraints（不能改或加的）:
- 不加搜尋、篩選、排序（Contract 4）
- 不建詳情頁（Contract 3）
- 不加登入功能

Permanent UX constraints（所有 contract 都適用）:
- 開瓶費資訊必須在列表卡片上可見
- 導航按鈕必須在詳情頁第一屏可見（本 contract 尚無詳情頁，跳過）
- "Free BYOB" 視覺上必須與 "Corkage Fee" 有明顯區別
- 沒有登入牆
- 所有用戶看到的文字是英文

Success Criteria:
- [ ] Theme 設定完成：主色 #8B2635、背景色 #F8F4EF、字型 Inter、圓角 12px
- [ ] Firestore 連接 Firebase 專案 byob-app-5e4db，集合 restaurants
- [ ] 列表頁載入 Firestore restaurants 集合，顯示真實資料（94 筆）
- [ ] 每張卡片顯示：cover_image_url 圖片 + name + philly_restaurant_type + 開瓶費 badge
- [ ] 開瓶費 badge 三種：
    - philly_corkage_fee = "free" → 綠色 badge "Free BYOB"
    - philly_corkage_fee = "corkage_fee" → 灰色 badge "$[corkage_fee_amount]"（無金額則顯示 "Corkage Fee"）
    - philly_corkage_fee = "other" → 橘色 badge "Ask Us"
- [ ] 列表載入時間 ≤ 2 秒
- [ ] App 視覺上有 BYOB 身份識別，不像普通餐廳 App

Firestore 欄位注意事項：
- name（餐廳名稱）
- cover_image_url（圖片 URL，Firebase Storage）
- philly_restaurant_type（料理類型，comma-separated string）
- philly_corkage_fee（"free" / "corkage_fee" / "other"）
- corkage_fee_amount（數字，部分為空）
- Add（地址，大寫 A）
- Phone（電話，大寫 P）
- Latitude / Longitude（大寫 L）

Required self-audit before DONE:
Before outputting <promise>DONE</promise>, verify:
1. 所有 success criteria 都達到了
2. FlutterFlow 預覽可以正常開啟列表頁，無 error
3. Firestore 資料正確載入，卡片重複顯示多筆真實餐廳
4. 三種開瓶費 badge 顏色正確
5. 沒有動到 scope 外的頁面或元件
6. 所有用戶看到的文字是英文
7. 截圖列表頁等 Neil 確認

Completion Signal:
Output <promise>DONE</promise> only when all success criteria are met.
Then take a screenshot of the restaurant list page in FlutterFlow preview.
```

---

## 3. Contracts 完成摘要

| # | Contract | 狀態 | 完成日期 |
|---|----------|------|----------|
| 0 | 專案資料整理（刪除多餘檔案、移除 WordPress、確認 Firebase） | ✅ | 2026-06-14 |
| Pre-A | Firebase Storage 圖片上傳（94 張 placeholder webp） | ✅ | 2026-06-14 |
| Pre-B | 建立 FlutterFlow 專案 BYOB Philly + workspace init | ✅ | 2026-06-14 |

---

## 4. P1 功能完成度

| P1 功能 | 狀態 |
|---------|------|
| 餐廳列表頁（Firestore 綁定） | ⏳ |
| 開瓶費 badge 顯示邏輯（3 種） | ⏳ |
| 篩選（依 philly_restaurant_type） | ⏳ |
| 餐廳詳情頁 | ⏳ |
| Google Maps 導航按鈕 | ⏳ |

---

## 5. 技術詳情

| 項目 | 值 |
|------|-----|
| Firebase 專案 ID | byob-app-5e4db |
| Firestore 集合 | `restaurants`（94 筆，Document ID = WP_Post_ID） |
| Service account | `byob-app-5e4db-firebase-adminsdk-fbsvc-c8314f2fe3.json`（不進 git，在 BYOB/ 根目錄） |
| FlutterFlow 專案 ID | b-y-o-b-philly-a08xby |
| Workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |
| 後端 | 純 Firebase（不需要 Render） |

### 注意事項

- Firestore 欄位大小寫：`Add`（大寫 A）、`Phone`（大寫 P）、`Latitude`/`Longitude`（大寫 L）
- Service account JSON 有 Firestore 完整讀寫權限，不能 commit 進 git（已在 .gitignore）
- `philly_restaurant_type` 主要值：american, asian, italian, seafood, mediterranean, other（other 佔 57/94）
- 圖片 placeholder 在 `BYOB/Mid/Placeholder/IMAGE_CONVERT/*.webp`

---

## 6. 暫緩項目

| 項目 | 暫緩原因 |
|------|----------|
| Firebase Storage 圖片遷移 | Pre-A，優先執行 |
| 地圖視圖 | P2，MVP 後 |
| Near me 排序 | P2，需要 GPS 權限 |
| 用戶驗證功能 | P3 |
| Wine Shop 推薦 | P3 |
| 多城市 | P3 |

---

## 7. 不可變的產品原則

1. 從打開 App 到開始導航，最多 3 次 tap
2. 開瓶費在列表卡片層可見，不需要進詳情頁
3. 沒有登入牆
4. UI 語言全英文
5. 只做費城（MVP 階段）
