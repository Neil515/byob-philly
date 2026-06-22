# BYOB Finder Philadelphia — Progress Handoff

最後更新：2026-06-22

---

## 1. 目前在哪裡停下來

**階段：Contract 8 進行中 — 地圖視角 toggle 已完成，Google Maps 畫面空白待修**

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
| HomePage | ✅ 列表視角正常；地圖視角 toggle 有效，底部 3 張最近餐廳卡片顯示正常，但地圖畫面空白 |
| RestaurantCard | ✅ 左右分割，Free BYOB（綠）/ Corkage Fee（酒紅）/ Ask Us（橘） |
| RestaurantDetailPage | ✅ 名稱、料理、badge、地址（可點導航）、電話（可點撥打）、Get Directions 按鈕，全在第一屏 |
| Filter chips | ✅ 12 個 chip，多選 OR 邏輯，選中不移位 |
| 地圖視角 toggle | ✅ App bar 右上角 icon，切換 isMapView boolean |
| 底部 nearest-3 卡片 | ✅ 顯示最近 3 家，chip 篩選同步更新，點擊進詳情頁 |
| Google Maps widget | ⚠️ 畫面空白 — 疑似 API key 未啟用 Maps JavaScript API |

### Custom Functions 狀態

| Function | 狀態 | 說明 |
|----------|------|------|
| `formatCuisineType` | ✅ | split comma → replace "other" → join " · " |
| `filterRestaurantsByType` | ✅ | multi-select OR logic，comma-split |
| `getMapsUrl` | ✅ | body-only，回傳 Google Maps URL |
| `getPhoneUrl` | ✅ | body-only，回傳 tel: URL |
| `haversineDistance` | ✅ | 純算術近似（無 dart:math），squared-km proxy |
| `getNearestThree` | ✅ | 排序後 take(3).cast<RestaurantsRecord>() |

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

### 🔴 明天第一件事：Contract 8 debug — Google Maps 空白

**背景：** 地圖視角 toggle 正常，底部 nearest-3 卡片正常，但 Google Maps widget 畫面完全空白。疑似原因：現有 API key 是 Android key，web preview 需要 Maps JavaScript API 也啟用在同一個 key 上。

#### 給 Ralph 的 prompt（直接複製貼上）

```
Read CLAUDE.md and PRODUCT_BRIEF.md before starting.
State your planned approach in 3–4 lines first.

Active contract:
Contract 8 debug: Google Maps widget blank in web preview / test mode

Context:
Map view toggle works. Bottom 3 nearest restaurant cards display correctly.
But the Google Maps widget area is completely blank (white) in FlutterFlow
web test mode.

FlutterFlow workspace folder:
C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly

Diagnose and fix:

1. The current Google Maps API key is an Android key (set in FlutterFlow
   Settings → Integrations → Google Maps). Web preview requires Maps
   JavaScript API to be enabled on the same key.

   Check: is Maps JavaScript API enabled on the key in Google Cloud Console
   (APIs & Services → Enabled APIs)?

   If not: instruct Neil exactly how to enable it — go to
   console.cloud.google.com → project byob-app-5e4db → APIs & Services →
   Library → search "Maps JavaScript API" → Enable.
   No new key needed, same key works for both Android and web.

2. Also check: does the Google Maps widget have showLocationValue set to true
   and the container has correct dimensions (Expanded or fixed height)?
   A zero-height container would also cause a blank map.

3. Confirm whether map blank is web-only or would also affect Android device.
   If it's web-only AND Maps JavaScript API is already enabled, the blank map
   in web preview may be a FlutterFlow web rendering limitation and acceptable
   — the real validation is on a physical Android device.

Report findings before making any DSL changes.
Do not change RestaurantCard, filter logic, list view, or detail page.

Constraints:
- All user-facing text in English
- DSL push command:
  Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby

Permanent UX constraints:
- Corkage fee visible on list cards
- Navigation button on detail page first screen
- Free BYOB visually distinct from Corkage Fee
- No login wall
- All user-facing text in English
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
| 7 | Android 打包 + 手機實機測試 | ⏳ 待執行（Neil 手動） | — |
| 8 | 地圖視角（HomePage toggle + nearest-3 卡片） | 🔴 進行中（地圖空白待修） | — |

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
| 地圖視角（toggle + nearest-3 卡片） | P2 | 🔴 地圖空白待修 |
| Firestore GeoPoint 欄位（地圖 markers 用） | P2 | ⏳ 待執行（路線 A） |
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
| Google Maps API Key | ✅ Android key 已填入 FlutterFlow → Maps SDK for Android 已啟用，Maps JavaScript API 狀態未確認 |

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

- Filter chips 順序（固定）：All · Italian · Japanese · Mediterranean · Asian · Seafood · Mexican · Thai · French · American · Indian · Other
- "Other" chip 邏輯：不含 italian / japanese / mediterranean / asian / seafood / mexican / thai / french / american / indian
- Token 數量（含複合）：Italian 36、other 57、Mediterranean 12、Seafood 10、Japanese 10、Asian 6、Mexican 5、Thai 4、French 3、American 2、Indian 2
- `formatCuisineType(typeString, otherNote)`：split comma → replace "other" with note → join " · "
- `haversineDistance`：純算術，無 dart:math（dLat*111, dLng*85, squared proxy）
- `getNearestThree`：回傳 `copy.take(3).toList().cast<RestaurantsRecord>()`
- DSL push 指令：`Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`
- `Name` field key 在 FlutterFlow DSL 中為 `1n8bgxro`
- Ralph 給 Cowork 的 prompt 一律英文（從 Contract 5 起）
- **重要**：Ralph 更新 custom function 必須用 `app.raw()` + `updateCustomFunction`，不是 `ensureCustomFunction`（後者只建不更新）

---

## 6. 暫緩項目

| 項目 | 暫緩原因 |
|------|----------|
| Firestore GeoPoint migration | 地圖 markers 需要，等地圖空白問題確認後執行 |
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
