# BYOB Finder Philadelphia — Progress Handoff

最後更新：2026-06-21

---

## 1. 目前在哪裡停下來

**階段：Contract 2 完成 — `restaurantName` binding 已修復，列表應顯示真實餐廳名稱**

### Firebase 狀態

| 項目 | 狀態 |
|------|------|
| Firebase 專案 "BYOB APP" | ✅ 存在（byob-app-5e4db） |
| Firestore `restaurants` 集合 | ✅ 94 筆資料，欄位已驗證 |
| Service account JSON | ✅ 在 BYOB/ 根目錄，已加入 .gitignore |
| Firebase Storage | ✅ 94 張 placeholder 圖片已上傳 |
| cover_image_url | ✅ 94/94 筆已更新為 Firebase Storage URL |
| Firebase Storage CORS | ✅ 已設定，web preview 圖片正常顯示 |
| Firestore Security Rules | ✅ Read: Everyone, Create/Write/Delete: No One |

### FlutterFlow 狀態

| 項目 | 狀態 |
|------|------|
| FlutterFlow 專案名稱 | BYOB Philly |
| FlutterFlow 專案 ID | b-y-o-b-philly-a08xby |
| FlutterFlow workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |
| Firebase 連接 | ✅ byob-app-5e4db，package: com.smalltoolsstudio.byobphilly |
| Contract 2 DSL push | ✅ commit `YoJ3TDnUaQnEwa06UPGm`（Name field map-key 修復已推） |
| 列表頁預覽 | ✅ **待 Neil 確認** — binding 修復，0 errors，等待截圖確認 |

### 安全狀態

| 項目 | 狀態 |
|------|------|
| GitHub secret leak | ✅ 已處理：API 金鑰 4 已刪除，.env 從 git 歷史清除，force push 完成 |

---

## 2. 下一步工作（依序執行）

### ✅ Contract 2 修復（已完成 2026-06-21）

**根本原因（已診斷）：**
FlutterFlow proto 將 `Name` 欄位的 map key 存成 `"name"`（小寫），但 `identifier.name` 是 `"Name"`（大寫）。validator 用 map key 直接查找 `collection.fields["Name"]` → null → binding 失敗。

**修復方式：**
在 `dsl/edit.dart` 的 `app.raw()` 中，先將 map key 從 `"name"` 改回 `"Name"`，再進行 `findFieldId('Name')` 查找。Push commit: `YoJ3TDnUaQnEwa06UPGm`，dry-run 0 errors，push 成功。

**待確認：** Neil 在 FlutterFlow 預覽中確認餐廳名稱正確顯示。

---

### ⏳ Contract 3：餐廳詳情頁 + Google Maps 導航按鈕

**觸發條件：** Contract 2 完成後
**執行者：** Claude Code (Ralph)
**注意：** Contract 3 需要新的 Google Maps API Key（舊的已刪除）。建立新 key 時不放進 .env，直接在 FlutterFlow 設定裡填入。

---

### ⏳ Contract 4：篩選功能（依 philly_restaurant_type）

**觸發條件：** Contract 3 完成後
**執行者：** Claude Code (Ralph)

---

## 3. Contracts 完成摘要

| # | Contract | 狀態 | 完成日期 |
|---|----------|------|----------|
| 0 | 專案資料整理（刪除多餘檔案、移除 WordPress、確認 Firebase） | ✅ | 2026-06-14 |
| Pre-A | Firebase Storage 圖片上傳（94 張 placeholder webp） | ✅ | 2026-06-14 |
| Pre-B | 建立 FlutterFlow 專案 BYOB Philly + workspace init | ✅ | 2026-06-14 |
| 1 | FlutterFlow Theme 設定 + Firebase 連接 + 列表頁基礎綁定 | ✅ | 2026-06-15 |
| 2 | 餐廳卡片重新設計（左右分割，圖片 1.19:1） | ✅ 待 Neil 視覺確認 | 2026-06-21 |
| 3 | 餐廳詳情頁 + Google Maps 導航按鈕 | ⏳ | — |
| 4 | 篩選功能（依 philly_restaurant_type） | ⏳ | — |

---

## 4. P1 功能完成度

| P1 功能 | 狀態 |
|---------|------|
| 餐廳列表頁（Firestore 綁定） | ✅ |
| 開瓶費 badge 顯示邏輯（3 種） | ✅ |
| 卡片視覺設計（左右分割） | ✅ binding 修復，待 Neil 截圖確認 |
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
| App package name | com.smalltoolsstudio.byobphilly |
| Workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |
| 後端 | 純 Firebase（不需要 Render） |
| Firebase Storage bucket | byob-app-5e4db.firebasestorage.app |
| Google Maps API Key | ❌ 已刪除（洩漏）— Contract 3 前需重建，不放 .env |

### Firestore 欄位大小寫（注意！）

| 欄位 | 說明 |
|------|------|
| `Name` | 餐廳名稱（**大寫 N**）⚠️ FlutterFlow field key: `1n8bgxro`，binding 問題根源 |
| `Add` | 地址（大寫 A） |
| `Phone` | 電話（大寫 P） |
| `Latitude` | 緯度（大寫 L） |
| `Longitude` | 經度（大寫 L） |
| `cover_image_url` | Firebase Storage URL（小寫） |
| `philly_restaurant_type` | 料理類型（小寫） |
| `philly_corkage_fee` | 開瓶費類型：free / corkage_fee / other |
| `corkage_fee_amount` | 開瓶費金額（數字，部分為空） |

### 其他注意事項

- `philly_restaurant_type` 主要值：american, asian, italian, seafood, mediterranean, other（other 佔 57/94）
- 圖片 placeholder 在 `BYOB/Mid/Placeholder/IMAGE_CONVERT/*.webp`
- DSL push 指令：`dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`（用 FlutterFlow ID，不是 Firebase ID）
- `Name` field key 在 FlutterFlow DSL 中為 `1n8bgxro`

---

## 6. 暫緩項目

| 項目 | 暫緩原因 |
|------|----------|
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
