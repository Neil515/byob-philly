# BYOB Finder Philadelphia — Progress Handoff

最後更新：2026-06-14

---

## 1. 目前在哪裡停下來

**階段：Phase 0 完成，等待 Pre-A（圖片遷移）和 Pre-B（FlutterFlow 建立）**

### Firebase 狀態

| 項目 | 狀態 |
|------|------|
| Firebase 專案 "BYOB APP" | ✅ 存在（byob-app-5e4db） |
| Firestore `restaurants` 集合 | ✅ 94 筆資料，欄位已驗證 |
| Service account JSON | ✅ 在 BYOB/ 根目錄，已加入 .gitignore |
| Firebase Storage | ⚠️ 尚未上傳任何圖片 |
| cover_image_url | ⚠️ 90/94 筆指向 byobmap.com（舊 WordPress），需遷移 |

### FlutterFlow 狀態

| 項目 | 狀態 |
|------|------|
| FlutterFlow 專案 ID | **尚未建立** |
| FlutterFlow workspace 路徑 | **尚未建立** |

**注意：** 2025-12-24 有舊的 ListView 綁定進度，但 FlutterFlow 專案已不再繼續使用。這次從零開始，按照 Utility Studio 標準流程建立。

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

**觸發條件：** Pre-A + Pre-B 都完成
**執行者：** Claude Code (Ralph)，使用 CLAUDE_PROMPT_TEMPLATE.md 的 Build Prompt Template

目標：設定 Theme（#8B2635 / #F8F4EF / Inter / 12px），連接 Firebase，建立 ListView 並綁定 Firestore `restaurants` 集合。

---

## 3. Contracts 完成摘要

| # | Contract | 狀態 | 完成日期 |
|---|----------|------|----------|
| 0 | 專案資料整理（刪除多餘檔案、移除 WordPress、確認 Firebase） | ✅ | 2026-06-14 |

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
| Workspace 路徑 | 尚未建立（待 flutterflow ai init） |
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
