# BYOB Map — Progress Handoff

最後更新：2026-06-30（EOD）

---

## 1. 目前在哪裡停下來

**階段：P1 + P2 核心功能全部完成。品牌更名為 BYOB Map。明天第一件事：修 dark mode，然後準備上架。**

### 今日完成項目（2026-06-30）

| Contract | 內容 | 狀態 |
|----------|------|------|
| C15 | 地圖 markers 修復（call order 根本原因診斷） | ✅ |
| C16 | 地圖 markers 與 filter chips 同步 | ✅ |
| C17 | Fine Dining 篩選修復（philly_restaurant_type comma-split 比對） | ✅ |
| C18 | 品牌更名 BYOB Map + multi-city 架構（Firestore city 欄位 + HomePage query filter） | ✅ |
| C19 | 視覺 polish + DSL 鎖定（App Bar 標題、icon、字體、按鈕尺寸） | ✅ |
| 手動 | 搜尋欄 X 按鈕修復（Clear Text Fields action + SearchField Initial Value 綁 searchText） | ✅ |

### Firebase 狀態

| 項目 | 狀態 |
|------|------|
| Firebase 專案 "BYOB APP" | ✅ 存在（byob-app-5e4db） |
| Firestore `restaurants` 集合 | ✅ 94 筆資料，欄位已驗證 |
| Service account JSON | ✅ 在 BYOB/ 根目錄，已加入 .gitignore |
| Firebase Storage | ✅ 94 張 placeholder 圖片已上傳，cover_image_url 94/94 已更新 |
| Firebase Storage CORS | ✅ 已設定，web preview 圖片正常顯示 |
| Firestore Security Rules | ✅ Read: Everyone, Create/Write/Delete: No One |
| Google Maps API Key | ✅ Android key 已填入 FlutterFlow，Maps SDK for Android 已啟用 |

### FlutterFlow 狀態

| 項目 | 狀態 |
|------|------|
| FlutterFlow 專案名稱 | BYOB Philly |
| FlutterFlow 專案 ID | b-y-o-b-philly-a08xby |
| FlutterFlow workspace 路徑 | C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly |
| Firebase 連接 | ✅ byob-app-5e4db，package: com.smalltoolsstudio.byobphilly |
| HomePage | ✅ 列表視角正常；搜尋列在 filter chips 下方；地圖視角顯示費城地圖，底部 nearest-3 卡片正常 |
| RestaurantCard | ✅ 左右分割，Free BYOB（綠）/ Corkage Fee（酒紅）/ Ask Us（橘） |
| RestaurantDetailPage | ✅ 名稱、料理、badge、地址（可點導航）、電話（可點撥打）、Get Directions 按鈕，全在第一屏 |
| Filter chips | ✅ 12 個 chip，多選 OR 邏輯，選中不移位 |
| 搜尋列 | ✅ filter chips 下方，real-time 名稱搜尋，AND 邏輯，× 清除 |
| 地圖視角 toggle | ✅ App bar 右上角 icon，切換 isMapView boolean |
| 底部 nearest-3 卡片 | ✅ 顯示最近 3 家，chip + 搜尋篩選同步更新，點擊進詳情頁 |
| Google Maps widget | ✅ 費城地圖 + 位置權限 + zoom 15 + Rose 色 markers，APK 驗收通過 |
| Web test mode | ⚠️ 地圖顯示 Google 錯誤（Android key 不支援 web，預期行為，不需處理） |
| Firestore GeoPoint | ✅ 94 家餐廳 location GeoPoint 欄位已寫入，FlutterFlow schema 已更新 |

### Custom Functions 狀態

| Function | 狀態 | 說明 |
|----------|------|------|
| `formatCuisineType` | ✅ 留存不刪（C14 後 UI 不再使用，刪除會造成 DSL error） | split comma → replace "other" → join " · " |
| `filterRestaurantsByType` | ✅ | multi-select chip OR logic，comma-split |
| `getMapsUrl` | ✅ | 回傳 Google Maps URL |
| `getPhoneUrl` | ✅ | 回傳 tel: URL |
| `haversineDistance` | ✅ | 純算術近似（無 dart:math），squared-km proxy |
| `getNearestThree` | ✅ | 排序後 take(3).cast<RestaurantsRecord>() |
| `searchRestaurantsByName` | ✅ Contract 12 完成 | 名稱搜尋，case-insensitive |
| `getCuisineDisplayList` | ✅ Contract 14 完成 | 回傳 List<String>，取代 formatCuisineType 的顯示用途 |
| `filterRestaurantsByTypeOrNote` | ✅ Contract 14 完成 | 同時查 philly_restaurant_type 和 other_note |

### 已知 DSL 問題

- **updateCustomFunction**：必須用 `app.raw()` + `updateCustomFunction`，不是 `ensureCustomFunction`（後者只建不更新）
- **OneDrive file lock**：每次 DSL push 後 `generated_code/` rename 會失敗（Windows/OneDrive lock），但不影響雲端 push。用以下指令可避免：
  `Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`

---

## 2. 下一步工作

### 🔴 明天第一項：Dark Mode 修復（C20）

**問題：** App 完全為淺色背景設計（#F8F4EF 米白），用戶手機若開啟深色模式，畫面背景變黑，所有文字、卡片、badge 顯示全部破版。

**截圖已確認：** RestaurantDetailPage 黑底，料理 tag、Free BYOB badge 可見但背景失控；HomePage 黑底，卡片背景消失。

**最快修法（推薦，2 分鐘，不需要 Ralph）：**
在 FlutterFlow → Settings → App Details → Theme Mode → 選 **"Light"**（強制淺色模式，不跟隨系統）。這是上架前最穩妥的做法，避免為深色模式單獨設計一套顏色。

**如果要真正支援深色模式（需要 Ralph contract）：**
替每個 Widget 指定 dark mode color variants（背景、文字、badge、卡片等），工作量較大，不建議在上架前做。

→ **執行：FlutterFlow → Settings → App Details → Theme Mode = Light。驗收：深色手機重新下載 APK，確認版面正常。**

---

### 上架準備（Dark Mode 修復後）

以下項目按順序處理：

1. **App icon 設計** — 目前是預設 icon，需要 BYOB Map 專屬 icon（1024×1024 PNG）
2. **Splash screen** — 目前是 placeholder，需替換
3. **Google Play Store 資料填寫** — 標題、描述、截圖、分類
4. **APK → AAB 打包** — Google Play 要求 .aab 格式
5. **Internal testing track** → Closed testing → Production

---

---

## ⚠️ 地圖 Markers 技術記錄（不要刪除，留給以後參考）

### 問題時間軸

Contract 9（2026-06-24）：地圖 markers 正常，APK 驗收通過。
Contract 14（2026-06-29）：push 後 markers 完全消失。
Contract 15（2026-06-30）：找到根本原因，markers 重新出現，但失去 filter 同步。
Contract 16（2026-06-30）：markers 與 filter chips 同步恢復，APK 驗收通過。

### 失敗的修復嘗試（不要重複）

| 嘗試 | 做了什麼 | 為何無效 |
|------|----------|----------|
| 嘗試 1 | 將 `docMarkers` 從 `filteredRestaurantsId` 改為 `restaurantsId` | Guard condition 仍檢查 `filteredRestaurantsId`，callback 仍 early return |
| 嘗試 2 | Git diff 找到 C14 改了 guard condition，revert 回 pre-C14 | 同時把 `docMarkers` 也 revert 回 `filteredRestaurantsId`，問題復發 |
| 嘗試 3 | 完整還原 `buildByobContract9` 至 pre-C14 exact text | C14 的 page load 邏輯（在 callback 外部）仍使 `filteredRestaurants` 為 null，callback 仍 early return |
| 嘗試 4 | `docMarkers → restaurantsId`，guard → `if (restaurantsId == null) return` | markers 仍未出現，代表問題不在 data source，在更深層 |

### 根本原因（C15 確認）

問題不在 `buildByobContract9` 的程式碼本身——pre-C14 和 HEAD 的程式碼幾乎完全一致。

**真正的原因是 call order（執行順序）：**

- C12 的 `editPage` 重建了整個 HomePage body，產生了一個帶**新 widget keys** 的 `MapWidget`
- C9 的 `raw()` callback 在 call-position 3 就先註冊（C12 在 position 8）
- 結果：C9 的 markers callback 綁在舊的 MapWidget 上，新的 MapWidget 沒有 markers

### 最終解法（C15 + C16）

**C15**：將 `buildByobContract9` 的執行順序移到 `buildByobContract12`（和 `buildByobContract14`）之後。`docMarkers` 暫時改綁 `restaurantsId`（全部 94 筆）確保不為 null。Markers 重新出現，但不隨 filter 變化。

**C16**：
1. 確認 `filteredRestaurants` 在 page load 時永遠不為 null（`filterRestaurantsByTypeOrNote` 在 typeValue 為空時直接回傳完整列表）
2. 將 `docMarkers` 改回綁定 `filteredRestaurantsId`
3. Guard condition：`if (filteredRestaurantsId == null) return;`
4. Call-position 保持 C15 的順序不變

結果：markers 與 chip 篩選、搜尋、incomingCuisineFilter 全部同步。

### DSL 架構注意事項

- `buildByobContract9` **必須** 在 `buildByobContract12` 和 `buildByobContract14` 之後執行（在 `buildByobPhilly` 內的順序）
- `docMarkers` **必須** 綁定 `filteredRestaurantsId`（不是 `restaurantsId`）
- `filteredRestaurants` 在 page load 時必須初始化為完整列表（不可為 null）
- 這三個條件缺一不可，任何一個變動都可能讓 markers 再次消失

---

## 3. 舊的 Contract 14 prompt（已執行，僅存檔）

### 背景與設計決策（為何這樣做，不要跳過）

**問題起點：** 目前餐廳卡片和詳情頁的料理類型是一個 Text widget，`formatCuisineType()` 把 `philly_restaurant_type`（逗號分隔）和 `philly_restaurant_type_other_note` 合成一個字串（如 "Italian · Vietnamese"）。這個字串不可點，也無法分辨用戶點的是哪一個類型。

**為什麼 "other" 類型必須一起處理：**
- "other" 的來源：當初讓網友推薦時，表單只列主要類型，以外一律歸 other + 說明。後來從 Yelp 抓類型，不在主要清單的也歸 other。未來店家自行加入也一樣會有 other + note 的情境，這是結構性的，不是暫時現象。
- 不能用 "Other" chip 近似：Other chip 顯示約 40 家餐廳，但點 "Vietnamese" 的用戶只想看越南菜（可能 1-2 家）。用 Other chip 結果落差太大，比沒反應更糟。
- 結論：other_note 類型直接比對 `philly_restaurant_type_other_note` 欄位，不走 chip 系統。

**為什麼廢掉 formatCuisineType() 的合成字串：**
- 要做到「點哪個類型觸發哪個篩選」，必須讓每個類型各自獨立可點。
- 現在是合成後再在卡片層切開，等於合了又拆，多此一舉。
- 正確做法：直接從 Firestore 原始值出發，切開後每個值渲染成獨立 widget。

**其他已決定的細節：**
- 每家餐廳最多 3 種類型（資料設定時就限制了），Ralph 可寫死 3 個 tag 的寬度
- 每個 tag 寬度固定，超長字串一律 `...` 截斷
- 卡片層和詳情頁一起做（同一邏輯，兩個入口）
- 點任何類型標籤（無論有無對應 chip）：push 新的 HomePage 並帶篩選參數，chip 區域不動（不高亮任何 chip）
- 點 other_note 類型（如 Vietnamese）只篩列表，不高亮 Other chip——因為高亮 Other chip 但只出現 1-2 家會讓用戶困惑

---

### 給 Ralph 的 prompt（已執行，僅存檔）

```
Read CLAUDE.md before starting.
State your planned approach in 3–4 lines first.

Active contract:
Contract 14: Tappable cuisine type tags on RestaurantCard and RestaurantDetailPage

Background:
- Currently cuisine types are displayed via formatCuisineType() which combines
  philly_restaurant_type (comma-separated, e.g. "italian,other") and
  philly_restaurant_type_other_note (e.g. "Vietnamese") into a single Text
  widget ("Italian · Vietnamese"). This Text is not tappable.
- This contract replaces that single Text with individual tappable tag widgets,
  rendered directly from raw Firestore values — no combining step.
- Tapping a tag navigates to a filtered HomePage showing restaurants of that type.
- Works for both chip-mapped types (Italian) and other_note types (Vietnamese).

FlutterFlow workspace folder:
C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\byob-philly

Data model:
- philly_restaurant_type: comma-separated string, max 3 values per restaurant.
  Known values: italian, mediterranean, japanese, seafood, sushi, pizza, asian,
  mexican, thai, ramen, french, other, american, indian
- philly_restaurant_type_other_note: plain string, actual cuisine name when
  type contains "other" (e.g. "Vietnamese", "Ethiopian")

STEP 1 — Add custom function: getCuisineDisplayList
  Parameters:
    - typeString (String) — philly_restaurant_type raw value
    - otherNote (String) — philly_restaurant_type_other_note value
  Returns: List<String>
  Body: split typeString by comma, trim each value, replace "other" with
    otherNote (if otherNote is not empty, else keep "Other"), capitalize
    first letter of each value, return as list.
  Example: "italian,other", "Vietnamese" → ["Italian", "Vietnamese"]
  Use app.raw() + updateCustomFunction

STEP 2 — Add custom function: filterRestaurantsByTypeOrNote
  Parameters:
    - restaurants (List<RestaurantsRecord>)
    - typeValue (String) — the display value tapped (e.g. "Italian" or "Vietnamese")
  Returns: List<RestaurantsRecord>
  Body:
    - Known chip types (lowercase): italian, mediterranean, japanese, seafood,
      sushi, pizza, asian, mexican, thai, ramen, french, other, american, indian
    - If typeValue.toLowerCase() matches a known chip type:
        return restaurants where philly_restaurant_type contains
        typeValue.toLowerCase()
    - Else (it is an other_note value like "Vietnamese"):
        return restaurants where philly_restaurant_type_other_note
        contains typeValue (case-insensitive, use toLowerCase() on both sides)
  Use app.raw() + updateCustomFunction

STEP 3 — Add page parameter to HomePage:
  - incomingCuisineFilter (String, default "")

STEP 4 — On HomePage page load / initState:
  If incomingCuisineFilter is not empty:
    - Set filteredRestaurants = filterRestaurantsByTypeOrNote(
        allRestaurants, incomingCuisineFilter)
  Do NOT modify chip state — leave chips showing "All" regardless.

STEP 5 — Replace cuisine Text on RestaurantCard:
  - Remove the current single Text widget that uses formatCuisineType()
  - Replace with a Row containing tags built from getCuisineDisplayList()
  - Each tag style:
      background: #F8F4EF
      border: 1px solid #8B2635
      border radius: 8px
      horizontal padding: 6px, vertical padding: 2px
      font size: 11px, color: #8B2635
      max width: fixed to fit ~12 characters, overflow: ellipsis
  - Tags separated by 4px horizontal gap
  - Each tag wrapped in GestureDetector onTap:
      navigate to HomePage passing incomingCuisineFilter = that tag's display value
      (e.g. "Italian" or "Vietnamese")

STEP 6 — Replace cuisine Text on RestaurantDetailPage:
  Same tag rendering as Step 5.
  Each tag onTap: navigate to HomePage passing
    incomingCuisineFilter = that tag's display value.

STEP 7 — Leave formatCuisineType() in place (do not delete it).
  It is no longer used in the UI after Steps 5 and 6, but deleting it
  may cause DSL errors. Leave it as an unused function.

Constraints:
- Do not change filter chip logic or chip highlight state
- No chip gets highlighted when incomingCuisineFilter is applied
- All user-facing text in English
- Use app.raw() + updateCustomFunction for all new custom functions

DSL push command:
Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby

After push, confirm:
- 0 FlutterFlow errors
- RestaurantCard shows individual tappable cuisine tags (not one combined string)
- RestaurantDetailPage shows individual tappable cuisine tags
- Tapping "Italian" tag → new HomePage with Italian restaurants, no chip highlighted
- Tapping "Vietnamese" tag → new HomePage with Vietnamese restaurant(s), no chip highlighted
- Tags with long names truncate with "..."
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
| 12 | 搜尋列（名稱搜尋，AND 邏輯，real-time） | ✅ | 2026-06-25 |
| 13 | Chip 更新（Pizza/Sushi/Ramen 新增，American/Indian 移除） | ✅ | 2026-06-24 |
| 14 | 料理類型標籤可點篩選（卡片 + 詳情頁） | ✅ | 2026-06-29 |
| 15 | 地圖 markers 修復（call order 根本原因診斷） | ✅ | 2026-06-30 |
| 16 | 地圖 markers 與 filter chips 同步 | ✅ | 2026-06-30 |
| 17 | Fine Dining 篩選修復（philly_restaurant_type comma-split 比對） | ✅ | 2026-06-30 |
| 18 | 品牌更名 BYOB Map + multi-city 架構（Firestore city 欄位 migration + HomePage WHERE city=="philadelphia"） | ✅ | 2026-06-30 |
| 19 | 視覺 polish + DSL 鎖定（AppBar 標題、mapMarkedAlt icon、字體、Get Directions 按鈕高度） | ✅ | 2026-06-30 |

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
| 搜尋列（名稱搜尋） | P2 | ✅ |
| 料理類型標籤可點篩選（tag 顯示 + chip 高亮） | P2 | ✅ |
| 地圖 Rose pin markers（與 filter 同步） | P2 | ✅ C15 + C16 修復 |
| Fine Dining 篩選修復 | P2 | ✅ C17 完成 |
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
| `philly_restaurant_type` | 料理類型（逗號分隔，最多 3 個值） |
| `philly_restaurant_type_other_note` | "other" 的實際類型說明（如 "Vietnamese"） |
| `philly_corkage_fee` | 開瓶費類型：free / corkage_fee / other |
| `corkage_fee_amount` | 開瓶費金額（數字，部分為空） |

### ⚠️ 手動改動保護清單（Ralph 不可覆蓋）

以下項目已在 DSL（C19）鎖定，Ralph 的任何 contract 觸碰 HomePage 時必須確保以下值：

| 項目 | 值 | DSL 狀態 |
|------|-----|------|
| App Bar 標題 | `BYOB Map` | ✅ C19 已鎖入 DSL |
| 地圖 toggle icon | `FontAwesome.mapMarkedAlt`，顏色 #FFFFFF，右 padding 8 | ✅ C19 已鎖入 DSL |
| RestaurantCard NameText | maxLines=1，overflow=ellipsis | ✅ C19 已鎖入 DSL |
| Project / Display Name | `BYOB Map` | FlutterFlow Settings → App Details |
| Package Name | `com.smalltoolsstudio.byobmap` | FlutterFlow Settings → App Details |

**⚠️ Font Awesome icon 正確名稱：`FontAwesome.mapMarkedAlt`（camelCase）**
錯誤寫法：`map_marked_alt`、`mapmarkedalt` → FlutterFlow validator 會 reject。

**每個 Ralph prompt 的 Constraints 區塊必須加上：**
```
Do not change the App Bar title (must be "BYOB Map").
Do not change the map toggle icon (must be FontAwesome.mapMarkedAlt, color #FFFFFF, right padding 8).
Do not change the package name (com.smalltoolsstudio.byobmap) or display name (BYOB Map).
```

### C18 Multi-city 架構紀錄

- Firestore `restaurants` 集合：所有 95 筆文件已加入 `city: "philadelphia"` 欄位（Node.js firebase-admin migration）
- HomePage Firestore query：`WHERE city == "philadelphia"`（field key: `ulfeay3v`）
- 未來新城市：新增文件時帶 `city: "chicago"` 等，query 改為對應 city 即可
- 為何單 App 不換殼：App Store 4.3 policy 禁止同一 binary 換 metadata 重複上架；multi-city 在同一 App 用 filter 切換是合規做法

### 搜尋欄 X 按鈕修復紀錄（手動，非 DSL）

- Action 1: Update Page State → searchText → [Empty String] + Rebuild Current Page
- Action 2: Update Page State → filteredRestaurants → filterRestaurantsByTypes(a,b) + Rebuild
- Action 3: Update Page State → nearestThree → getNearestThree(a,b,c) + Rebuild
- Action 4: **Clear Text Fields**（Widget Action，target = SearchField）← 這個清除 TextField controller 顯示文字
- SearchField Initial Value 綁 `searchText`（確保頁面初次載入時搜尋欄為空）

---

### 其他注意事項

- Filter chips 順序（固定）：All · Italian · Mediterranean · Japanese · Seafood · Sushi · Pizza · Asian · Mexican · Thai · Ramen · French · Other
- "Other" chip 邏輯：不含 italian / japanese / mediterranean / asian / seafood / mexican / thai / french / american / indian / pizza / sushi / ramen
- Token 數量：Italian 36、other ~40、Mediterranean 12、Sushi ~10、Japanese 10、Seafood 10、Pizza ~9、Asian 6、Mexican 6、Thai 4、Ramen ~4、French 3
- `haversineDistance`：純算術，無 dart:math（dLat*111, dLng*85, squared proxy）
- `getNearestThree`：回傳 `copy.take(3).toList().cast<RestaurantsRecord>()`
- DSL push 指令：`Remove-Item -Recurse -Force generated_code -ErrorAction SilentlyContinue; dart run dsl/edit.dart --project-id b-y-o-b-philly-a08xby`
- `Name` field key 在 FlutterFlow DSL 中為 `1n8bgxro`
- Ralph 給 Cowork 的 prompt 一律英文（從 Contract 5 起）
- **重要**：Ralph 更新 custom function 必須用 `app.raw()` + `updateCustomFunction`，不是 `ensureCustomFunction`（後者只建不更新）
- **重要**：showLocation 綁定 `hasLocationPermission ?? false`（valueOrDefault<bool>），不可 hardcode true（會導致 SecurityException）
- **重要（2026-06-30 最終確認）**：地圖 markers 的三個必要條件（缺一不可）：(1) `buildByobContract9` 必須在 `buildByobContract12` 和 `buildByobContract14` 之後執行；(2) `docMarkers` 綁定 `filteredRestaurantsId`；(3) `filteredRestaurants` 在 page load 時不為 null。詳見「地圖 Markers 技術記錄」章節。

---

## 6. 暫緩項目

| 項目 | 暫緩原因 |
|------|----------|
| marker tap → 詳情頁導航 | FlutterFlow 系統性封鎖，三條路全部確認不可行：(1) ON_MARKER_TAP GENERATOR_VARIABLE 封鎖，(2) InfoWindow FlutterFlow 不支援，(3) Camera callbacks 不在 DSL。等 FlutterFlow 原生支援再做。 |
| Near me 排序 | P2，排序依據（距離/名稱/開瓶費優先）尚未決定，暫緩 |
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
