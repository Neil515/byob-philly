# BYOB Finder Philadelphia — Progress Handoff

最後更新：2026-06-24

---

## 1. 目前在哪裡停下來

**階段：Contract 13 ✅ — chip 更新完成（Pizza/Sushi/Ramen）。Contract 12 待執行：搜尋列**

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
| Google Maps API Key | ✅ Android key 已填入 FlutterFlow，Maps SDK for Android 已啟用並加入 allowed API list |

### FlutterFlow 狀態

| 項目 | 狀態 |
|------|------|
| FlutterFlow 專案名稱 | BYOB Philly |
| FlutterFlow 專案 ID | b-y-o-b-philly-a08xby |
| FlutterFlow workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |
| Firebase 連接 | ✅ byob-app-5e4db，package: com.smalltoolsstudio.byobphilly |
| HomePage | ✅ 列表視角正常；地圖視角顯示費城地圖，底部 nearest-3 卡片正常 |
| RestaurantCard | ✅ 左右分割，Free BYOB（綠）/ Corkage Fee（酒紅）/ Ask Us（橘） |
| RestaurantDetailPage | ✅ 名稱、料理、badge、地址（可點導航）、電話（可點撥打）、Get Directions 按鈕，全在第一屏 |
| Filter chips | ✅ 12 個 chip，多選 OR 邏輯，選中不移位 |
| 地圖視角 toggle | ✅ App bar 右上角 icon，切換 isMapView boolean |
| 底部 nearest-3 卡片 | ✅ 顯示最近 3 家，chip 篩選同步更新，點擊進詳情頁 |
| Google Maps widget | ✅ 費城地圖 + 位置權限 + zoom 15 + Rose 色 markers，APK 驗收通過 |
| Web test mode | ⚠️ 地圖顯示 Google 錯誤（Android key 不支援 web，預期行為，不需處理） |
| Firestore GeoPoint | ✅ 94 家餐廳 location GeoPoint 欄位已寫入，FlutterFlow schema 已更新 |

### Custom Functions 狀態

| Function | 狀態 | 說明 |
|----------|------|------|
| `formatCuisineType` | ✅ | split comma → replace "other" → join " · " |
| `filterRestaurantsByType` | ✅ | multi-select OR logic，comma-split |
| `getMapsUrl` | ✅ | body-only，回傳 Google Maps URL |
| `getPhoneUrl` | ✅ | body-only，回傳 tel: URL |
| `haversineDistance` | ✅ | 純算術近似（無 dart:math），squared-km proxy |
| `getNearestThree` | ✅ | 排序後 take(3).cast<RestaurantsRecord>() |
| `searchRestaurantsByName` | ⏳ Contract 12 | 名稱搜尋，case-insensitive |

### 已知 DSL 問題

- **updateCustomFunction 歷史問題**：Ralph 之前用 `ensureCustomFunction`（只建不更），改用 `app.raw()` + `updateCustomFunction` 後才能正確覆蓋雲端。現在已修正。
- **OneDrive file lock**：每次 DSL push 後 `generated_code/` rename 會失敗（Windows/OneDrive lock），但不影響雲端 push，只是本地 stub 是舊的。用以下指令可避免：
  `Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`

### 安全狀態

| 項目 | 狀態 |
|------|------|
| GitHub secret leak | ✅ 已處理：API 金鑰 4 已刪除，.env 從 git 歷史清除，force push 完成 |

---

## 2. 下一步工作（依序執行）

### 🔴 明天第一件事：Contract 12 — 搜尋列（Ralph prompt 已備妥，直接複製執行）

**範圍：**
- 搜尋列位置：filter chips 下方
- 搜尋邏輯：餐廳名稱，real-time（邊打邊篩）
- 與 filter chips 關係：AND 邏輯（搜尋套用在已篩選結果上）
- 清除按鈕：搜尋列右側 ×

#### 給 Ralph 的 prompt（直接複製貼上）

```
Read CLAUDE.md before starting.
State your planned approach in 3–4 lines first.

Active contract:
Contract 12: Add search bar to HomePage — filter by restaurant name

Context:
- HomePage list view has filter chips (cuisine type, multi-select OR logic)
- filteredRestaurants is the current page state holding the chip-filtered list
- Goal: add a search bar below the filter chips that further filters
  filteredRestaurants by restaurant name (AND logic with chips)
- Real-time: list updates as user types, no submit button needed

FlutterFlow workspace folder:
C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly

Changes to make:

STEP 1 — Add page state field:
  - searchText (String, default "")

STEP 2 — Add custom function searchRestaurantsByName:
  Parameters:
    - restaurants (List<RestaurantsRecord>)
    - query (String)
  Returns: List<RestaurantsRecord>
  Body: if query is empty or blank, return restaurants unchanged.
    Otherwise return restaurants where restaurant.name contains
    query (case-insensitive). Use toLowerCase() on both sides.
  Use app.raw() + updateCustomFunction (not ensureCustomFunction)

STEP 3 — Add search bar widget below filter chips:
  - TextField with hint text "Search restaurants..."
  - Right side: show × IconButton when searchText is not empty,
    tapping × clears searchText and resets the text field
  - Style: white background, border radius 12px, border color #8B2635,
    height 44px, horizontal padding 16px
  - On change: SetState searchText = typed value

STEP 4 — Wire search into the list:
  - The restaurant list currently binds to filteredRestaurants
  - Change the list binding to:
    searchRestaurantsByName(filteredRestaurants, searchText)
  - This gives AND logic: chip filter runs first, search runs on top

STEP 5 — Apply same search to nearest-3 (map view):
  - The nearest-3 computation currently uses filteredRestaurants
  - Also apply searchRestaurantsByName there so map view and list
    view stay in sync when search is active

Constraints:
- Search bar appears in list view only — do not show in map view
- Do not change filter chip logic, map widget, or detail page
- All user-facing text in English
- Name field in Firestore is capital N — accessor is .name in DSL
  (FlutterFlow field key: 1n8bgxro)
- Use app.raw() + updateCustomFunction for the new custom function

DSL push command:
Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby

After push, confirm:
- 0 FlutterFlow errors
- searchRestaurantsByName function added
- Search bar visible below filter chips in list view
- Typing filters the list in real-time
- × button clears search
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
| 4 | 篩選功能（cuisine type 顯示 + comma-separated 解析） | ✅ | 2026-06-22 |
| 5 | Filter chips 多選 + OR 邏輯 + 新增 American / Indian chip | ✅ | 2026-06-22 |
| 6 | 電話可點撥打 + 地址可點開導航（RestaurantDetailPage） | ✅ | 2026-06-22 |
| 7 | Android 打包 + 手機實機測試 | ✅ | 2026-06-23 |
| 8 | 地圖視角（HomePage toggle + 位置權限 + GPS 座標） | ✅ | 2026-06-24 |
| 9 | 地圖 markers（GeoPoint migration + Rose pin + zoom 15） | ✅ | 2026-06-24 |
| 10 | Marker tap 互動（已研究，放棄） | ⛔ FlutterFlow 系統性封鎖，暫緩 | — |
| 11 | nearest-3 UI 優化（標題 + 料理類型 + padding） | ✅ | 2026-06-24 |
| 12 | 搜尋列（名稱搜尋，AND 邏輯，real-time） | ⏳ 待執行 | — |
| 13 | Chip 更新（Pizza/Sushi/Ramen 新增，American/Indian 移除） | ✅ | 2026-06-24 |

**Contract 8 完成項目：**
- ✅ 地圖視角 toggle（AppBar icon）
- ✅ 費城地圖在 Android 實機正常顯示
- ✅ 底部 nearest-3 卡片（chip 篩選同步）
- ✅ Google Maps API key 設定（Maps SDK for Android 啟用並加入 allowed list）
- ✅ SecurityException 修復（showLocation 綁定 hasLocationPermission state）
- ✅ 使用 FlutterFlow 原生 location action（移除 geolocator custom code）
- ❌ 位置權限對話框未觸發（manifest 問題，下次優先修）
- ❌ 餐廳 markers 未顯示（GeoPoint migration 需要）

---

## 4. P1 + P2 功能完成度

| 功能 | 優先級 | 狀態 |
|------|--------|------|
| 餐廳列表頁（Firestore 綁定） | P1 | ✅ |
| 開瓶費 badge 顯示邏輯（3 種） | P1 | ✅ |
| 卡片視覺設計（左右分割） | P1 | ✅ |
| 餐廳詳情頁 | P1 | ✅ |
| Google Maps 導航按鈕 | P1 | ✅ |
| 篩選（依 philly_restaurant_type，多選 OR） | P1 | ✅ |
| 料理類型正確顯示（含 other_note） | P1 | ✅ |
| 電話號碼可點撥打 | P1 | ✅ |
| 地址可點開 Google Maps 導航 | P1 | ✅ |
| 地圖視角（toggle + 費城地圖 + 位置權限 + GPS） | P2 | ✅ |
| Firestore GeoPoint + 地圖 markers（Rose pin） | P2 | ✅ |
| nearest-3 UI 優化（標題 + 料理類型） | P2 | ✅ |
| Chip 更新（Pizza/Sushi/Ramen） | P2 | ✅ |
| 搜尋列（名稱搜尋） | P2 | ⏳ Contract 12 |
| Near me 排序 | P2 | ⏳ 暫緩 |

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
| Google Maps API Key | ✅ Android key 已填入 FlutterFlow → Maps SDK for Android 已啟用並加入 allowed API list |

### Firestore 欄位大小寫（注意！）

| 欄位 | 說明 |
|------|------|
| `Name` | 餐廳名稱（**大寫 N**）⚠️ FlutterFlow field key: `1n8bgxro` |
| `Add` | 地址（大寫 A） |
| `Phone` | 電話（大寫 P） |
| `Latitude` | 緯度（大寫 L）→ FlutterFlow model accessor: `.latitude` |
| `Longitude` | 經度（大寫 L）→ FlutterFlow model accessor: `.longitude` |
| `cover_image_url` | Firebase Storage URL（小寫） |
| `philly_restaurant_type` | 料理類型（逗號分隔複合值） |
| `philly_restaurant_type_other_note` | "other" 的實際類型說明 |
| `philly_corkage_fee` | 開瓶費類型：free / corkage_fee / other |
| `corkage_fee_amount` | 開瓶費金額（數字，部分為空） |

### 其他注意事項

- Filter chips 順序（固定）：All · Italian · Mediterranean · Japanese · Seafood · Sushi · Pizza · Asian · Mexican · Thai · Ramen · French · Other
- "Other" chip 邏輯：不含 italian / japanese / mediterranean / asian / seafood / mexican / thai / french / american / indian / pizza / sushi / ramen
- Token 數量（migration 後）：Italian 36、other ~40、Mediterranean 12、Sushi ~10、Japanese 10、Seafood 10、Pizza ~9、Asian 6、Mexican 6、Thai 4、Ramen ~4、French 3
- `formatCuisineType(typeString, otherNote)`：split comma → replace "other" with note → join " · "
- `haversineDistance`：純算術，無 dart:math（dLat*111, dLng*85, squared proxy）
- `getNearestThree`：回傳 `copy.take(3).toList().cast<RestaurantsRecord>()`
- DSL push 指令：`Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`
- `Name` field key 在 FlutterFlow DSL 中為 `1n8bgxro`
- Ralph 給 Cowork 的 prompt 一律英文（從 Contract 5 起）
- **重要**：Ralph 更新 custom function 必須用 `app.raw()` + `updateCustomFunction`，不是 `ensureCustomFunction`（後者只建不更新）
- **重要**：showLocation 綁定 `hasLocationPermission ?? false`（valueOrDefault<bool>），不可 hardcode true（會導致 SecurityException）

---

## 6. 暫緩項目

| 項目 | 暫緩原因 |
|------|----------|
| marker tap → 詳情頁導航 | FlutterFlow 系統性封鎖，三條路全部確認不可行：(1) ON_MARKER_TAP GENERATOR_VARIABLE 封鎖，(2) InfoWindow FlutterFlow 不支援，(3) Camera callbacks 不在 DSL。等 FlutterFlow 原生支援再做。 |
| Near me 排序 | P2，暫緩 |
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
