# BYOB Finder Philadelphia — Progress Handoff

最後更新：2026-06-21

---

## 1. 目前在哪裡停下來

**階段：Contract 2 ✅、Contract 3 ✅、Contract 4 進行中（cuisine type 顯示邏輯待修）**

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
| Google Maps API Key | ✅ Android key（Firebase 自動建立）填入 FlutterFlow Settings，Maps SDK for Android 已啟用 |

### FlutterFlow 狀態

| 項目 | 狀態 |
|------|------|
| FlutterFlow 專案名稱 | BYOB Philly |
| FlutterFlow 專案 ID | b-y-o-b-philly-a08xby |
| FlutterFlow workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |
| Firebase 連接 | ✅ byob-app-5e4db，package: com.smalltoolsstudio.byobphilly |
| HomePage | ✅ 餐廳列表、卡片（左右分割）、3 種 badge、Filter chips |
| RestaurantCard | ✅ 左右分割，Free BYOB（綠）/ Corkage Fee（酒紅）/ Ask Us（橘） |
| RestaurantDetailPage | ✅ 名稱、料理、badge、地址、電話、Get Directions 按鈕，全在第一屏 |
| Filter chips | ⚠️ 部分完成：chips 兩列顯示正常，但篩選邏輯未正確處理 comma-separated 類型 |

### 安全狀態

| 項目 | 狀態 |
|------|------|
| GitHub secret leak | ✅ 已處理：API 金鑰 4 已刪除，.env 從 git 歷史清除，force push 完成 |

---

## 2. 下一步工作（依序執行）

### 🔴 Contract 4 修復（明天第一件事）

**背景：** `philly_restaurant_type` 是逗號分隔的複合值（如 "Italian, other"、"Japanese, other"）。現有篩選做完全比對，導致大多數餐廳找不到。另有 `philly_restaurant_type_other_note` 說明 "other" 的實際類型（如 note = "Ramen"）。

**94 間餐廳中 47 間有複合類型。** 這影響：
1. 列表卡片顯示（如 "Asian, other" 直接顯示很醜）
2. 篩選邏輯（exact match 失敗）
3. 詳情頁顯示

#### 明天給 Ralph 的 prompt（直接複製貼上）

```
Read CLAUDE.md before starting. State your approach in 3-4 lines first.

This prompt covers fixes across RestaurantCard, HomePage filter, 
and RestaurantDetailPage. All changes relate to how philly_restaurant_type 
is parsed and displayed.

---

BACKGROUND: THE DATA PROBLEM

philly_restaurant_type is a comma-separated string (e.g. "Italian, other", 
"Japanese, other", "Italian, Mediterranean, Seafood").

philly_restaurant_type_other_note contains the real name when "other" 
appears (e.g. type = "Asian, other", note = "Ramen" → actual = "Asian · Ramen").

---

FIX 1: New custom function — formatCuisineType

Create a Dart custom function:
  String formatCuisineType(String typeString, String otherNote)

Logic:
1. Split typeString by comma, trim each part
2. Replace any part that equals "other" (case-insensitive) with otherNote
   (only if otherNote is non-empty; otherwise drop "other" entirely)
3. Join remaining parts with " · "
4. Return result

Examples:
  "Italian, other", "Mediterranean" → "Italian · Mediterranean"
  "Asian, other", "Ramen"           → "Asian · Ramen"
  "Italian, other", ""              → "Italian"
  "Italian, Mediterranean, Seafood", "" → "Italian · Mediterranean · Seafood"

---

FIX 2: RestaurantCard — add cuisineTypeNote param, use formatCuisineType

- Add a new String parameter: cuisineTypeNote
- Display cuisine using formatCuisineType(cuisineType, cuisineTypeNote)
  instead of raw cuisineType

---

FIX 3: HomePage — pass cuisineTypeNote to RestaurantCard

- Pass philly_restaurant_type_other_note as cuisineTypeNote to each 
  RestaurantCard in the ListView

---

FIX 4: Filter function — fix to handle comma-separated types

Update filterRestaurantsByType logic:
- Split each restaurant's philly_restaurant_type by comma, trim, lowercase
- Return restaurants where the selected type (lowercase) is in that list
- "Other" chip: return restaurants where NONE of these types appear 
  in their type list: italian, japanese, mediterranean, asian, 
  mexican, seafood, thai, french

---

FIX 5: Filter chips — update chip list

New chip order (sorted by count in data):
All · Italian · Japanese · Mediterranean · Asian · Seafood · Mexican · Thai · French · Other

---

FIX 6: RestaurantDetailPage — use formatCuisineType

Apply the same formatCuisineType function to display cuisine type 
on the detail page (pass both philly_restaurant_type and 
philly_restaurant_type_other_note from the page parameters).

---

CONSTRAINTS:
- Push with project ID b-y-o-b-philly-a08xby
- Do not change card layout, badge logic, or navigation
- All user-facing text in English

DONE criteria:
- Screenshot list page with "Japanese" chip selected showing 
  multiple restaurants
- Screenshot one card showing formatted cuisine (e.g. "Asian · Ramen")
- Wait for Neil to confirm before outputting DONE
```

---

## 3. Contracts 完成摘要

| # | Contract | 狀態 | 完成日期 |
|---|----------|------|----------|
| 0 | 專案資料整理（刪除多餘檔案、移除 WordPress、確認 Firebase） | ✅ | 2026-06-14 |
| Pre-A | Firebase Storage 圖片上傳（94 張 placeholder webp） | ✅ | 2026-06-14 |
| Pre-B | 建立 FlutterFlow 專案 BYOB Philly + workspace init | ✅ | 2026-06-14 |
| 1 | FlutterFlow Theme 設定 + Firebase 連接 + 列表頁基礎綁定 | ✅ | 2026-06-15 |
| 2 | 餐廳卡片重新設計（左右分割，圖片 1.19:1） | ✅ | 2026-06-21 |
| 3 | 餐廳詳情頁 + Google Maps 導航按鈕 | ✅ | 2026-06-21 |
| 4 | 篩選功能（依 philly_restaurant_type） | 🔴 修復中 | — |

---

## 4. P1 功能完成度

| P1 功能 | 狀態 |
|---------|------|
| 餐廳列表頁（Firestore 綁定） | ✅ |
| 開瓶費 badge 顯示邏輯（3 種） | ✅ |
| 卡片視覺設計（左右分割） | ✅ |
| 餐廳詳情頁 | ✅ |
| Google Maps 導航按鈕 | ✅ |
| 篩選（依 philly_restaurant_type） | 🔴 chips 顯示正常，篩選邏輯未處理 comma-separated 類型 |
| 料理類型正確顯示（含 note） | 🔴 待修復 |

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
| Google Maps API Key | ✅ Android key（Firebase 自動建立）已填入 FlutterFlow Settings → Integrations → Google Maps |

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

- `philly_restaurant_type` 是**逗號分隔的複合值**，94 間中 47 間有多種類型
- `philly_restaurant_type_other_note` 說明 "other" 的實際類型（如 "Ramen"、"Laotian"）
- 實際出現的主要類型及數量（含複合）：Italian ~38、Japanese ~13、Mediterranean ~12、Asian ~7、Seafood ~7、Mexican ~6、Thai ~4、French ~3
- DSL push 正確指令：`Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`（需先刪除 generated_code 以避免 OneDrive rename 鎖定問題）
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
