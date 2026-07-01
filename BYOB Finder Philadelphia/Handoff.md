# BYOB Map — Progress Handoff

最後更新：2026-07-01（EOD）

---

## 1. 目前在哪裡停下來

**階段：App 功能與視覺全部完成，Google Play 上架資料填寫進行到最後一哩路。應用程式內容聲明（廣告/內容分級/目標對象/資料安全性/政府/金融/健康/廣告ID）全部完成。剩「應用程式類別與聯絡資料」「商店資訊（含截圖）」兩項，明天從這兩項開始，做完就可以送審。**

### 👉 明天第一件事

打開 Google Play Console → BYOB Map → 資訊主頁，下滑到「管理應用程式的設定和呈現方式」，兩個連結都可以點（跟已完成項目不同，這兩項本來就還沒填，是有連結的）：
1. 「選取應用程式類別並提供詳細聯絡資料」— 類別選 **Food & Drink**（`store-listing/play_store_listing.md` 已經寫好），聯絡資料填 Email（`byobmap.tw@gmail.com`，其他欄位視 Play 要求填）
2. 「設定商店資訊」— 標題/簡短說明/完整說明直接複製 `store-listing/play_store_listing.md`，圖示已經有（`icon/icon-legacy-1024.png`），還缺手機截圖（至少 2 張，建議用 FlutterFlow 預覽或實機截 HomePage + RestaurantDetailPage）和 Feature graphic（1024×500，目前還沒做，這個可能需要跟 Neil 討論怎麼生成）

兩項都填完後，回「發布總覽」把 Internal Testing 的變更送交審查。

### 今日完成項目（2026-07-01）

| Contract | 內容 | 狀態 |
|----------|------|------|
| C20 | Dark Mode 修復（FlutterFlow Theme → Colors → Dark Mode Theme 手動填值，非原計畫的 force Light） | ✅ |
| 手動 | 料理 tag 背景寫死 #F8F4EF（light/dark 都用同一色，不跟 Secondary Background 走） | ✅ |
| 手動 | 料理 tag 字體 16px、badge 字體 14px（列表卡片 + 詳情頁一致） | ✅ |
| 文件 | Google Play 上架文案草稿（標題/簡短說明/完整說明） → `store-listing/play_store_listing.md` | ✅ |
| Play Console | 廣告聲明、內容分級、目標對象、資料安全性、政府/金融/健康聲明、廣告 ID 全部完成 | ✅ |
| 修復 | 發現並補上 Firebase Performance Monitoring 未揭露問題（隱私政策 + 資料安全性表單） | ✅ |

### 昨日完成項目（2026-06-30）

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

## 1.3.5 ✅ App Icon（2026-07-01 完成）

- 定案圖：地圖釘融合酒杯剪影，酒紅漸層 + 米白負空間杯身，無金色（在鎖定設計系統色系內）。原始檔存在 `icon/` 資料夾（ChatGPT 產出，1254×1254）
- 已產出三個版本：
  - `icon/icon-legacy-1024.png` — 完整方形、含背景色，給一般 Launcher Icon / Play Store / 未來 iOS 用
  - `icon/icon-adaptive-foreground.png` — 去背透明，圖案縮到畫布 55% 置中（安全區內），給 Android Adaptive Icon 用
  - `icon/icon-adaptive-background.png` — 純 #F8F4EF 色塊，備用（FlutterFlow 這版是用 Background Type 色票直接選色，不用這個檔案）
- FlutterFlow 設定位置：Settings → App Assets
  - Launcher Icon → App Launcher Icon：已上傳 `icon-legacy-1024.png` ✅
  - Android Adaptive Icon → Foreground Icon：已上傳 `icon-adaptive-foreground.png` ✅
  - Background Type：Color，`#F8F4EF` ✅
  - Preview（方形 + 圓形遮罩）確認圖案完整無裁切 ✅
- ⚠️ **重要坑**：FlutterFlow 明確提示「Foreground Icon 這張圖也會被拿去當 iOS/Web 版的圖示」。目前這張是透明去背、圖案縮小置中的版本，iOS 不支援透明圖示，直接用會破版。**等以後真的要做 iOS 版，Foreground Icon 欄位要換成 `icon-legacy-1024.png`（完整方形有背景那張），不能沿用 Android adaptive 用的透明版。**
- 備選圖（未採用，留底）：金色外框版本，同樣存在 `icon/` 資料夾原始檔中（ChatGPT Image ...02_16_23.png）

---

## 1.3.6 ✅ Splash Screen（2026-07-01 完成）

FlutterFlow → Settings → App Assets → Splash：
- Initial Splash Image：`icon-adaptive-foreground.png`（透明去背版）
- Image Fit：Contain（等比例不變形）
- Width：240px（Height 留空自動等比例）
- Background Color：`#F8F4EF`（跟 HomePage 背景一致，切換無色差）
- Pre-loading Color：`#8B2635`（讀取指示顏色，品牌酒紅）
- Min Duration：留空（不人為拖慢啟動，符合「30 秒內開始導航」原則）

---

## 1.3.7 ✅ AAB 打包（2026-07-01 完成）

- Flutter 專案本機路徑：`C:\Launch\byob-map\b_y_o_b_map\`（FlutterFlow Download Code 解壓出來的，內層資料夾名稱 `b_y_o_b_map` 是自動產生的 snake_case 專案名）
- 簽名設定：`android\key.properties`（storeFile 指向 `C:/Launch/byobmap.jks`，alias `byobmap`）+ `android\app\build.gradle` 的 `buildTypes.release.signingConfig` 改成 `signingConfigs.release`
- Build 指令：`flutter build appbundle --release`
- 產出：`C:\Launch\byob-map\b_y_o_b_map\build\app\outputs\bundle\release\app-release.aab`（44.3 MB，正式簽名，非 debug）
- ⚠️ **下次 Download Code 後這兩個設定會被洗掉**（FlutterFlow 重新產生 android 資料夾），要重新做一次 Step 1 + Step 2（key.properties 建立 + build.gradle 那一行改簽名），才能再 build 出正式簽名的 AAB

---

## 1.3.7b AAB 打包 — 從房仲 App 專案學到的坑（背景資訊）

參考了另一個專案（Realtors Follow-up，`Realtors_Followup_Progress_Handoff.md`）已經走過的 AAB 上架流程，記錄下通用的風險點：

- **Download Code 每次都會重新產生 `android/app/build.gradle`**，任何手動加的 signingConfig 會被洗掉，代表簽名設定不是設一次就好，是**每次重新 Download Code 後都要重新加回去**。
- **Download Code 也會把 `pubspec.yaml` 版本號還原**（房仲那專案是還原成 `1.0.0+1`），每次要手動改回正確版本號。
- **google-services.json 可能也會被 Download Code 覆蓋**——BYOB Map 有沒有這個問題待確認（下次 Download Code 後檢查 `android/app/google-services.json` 是否還是正確的 Firebase 設定，不是的話要找備份或從 Firebase Console 重新下載）。
- 房仲專案用 **Android Studio 內建 keytool**：`C:\Program Files\Android\Android Studio\jbr\bin\keytool.exe`（代表這台電腦有裝 Android Studio，BYOB Map 產生金鑰也可以用這個路徑）
- 房仲專案有一行 `flutter pub remove package_info_plus`，是**該專案專屬的依賴問題**，不確定 BYOB Map 是否也有，不要照抄，先看 build 有沒有報錯再說
- ⚠️ 房仲專案筆記提到「`dart run` 在這台機器被 Windows Application Control Policy 封鎖」——但 BYOB Map 這邊 `dart run dsl/edit.dart` 目前為止都跑得動（見上方 Contract 紀錄），兩邊經驗不一致，暫時不確定原因，如果哪天 DSL push 突然失敗且錯誤跟 Application Control Policy 有關，記得回頭查這條

**BYOB Map 專屬簽名金鑰**：✅ 已產生（2026-07-01）
- 路徑：`C:\Launch\byobmap.jks`（跟其他 App 的 keystore 放在同一個資料夾，不在任何 git repo 底下）
- Alias：`byobmap`
- 密碼：`neil7878`（跟 realtors-followup 共用同一組密碼，非最佳實務但為 Neil 已知取捨）
- ⚠️ 務必額外備份這個 .jks 檔案到密碼管理器或雲端加密硬碟，弄丟或忘記密碼會導致這個 App 以後無法更新
- 原本暫存在 `BYOB Finder Philadelphia` 專案資料夾裡的那份已搬到 `C:\Launch\`，`.gitignore` 的 `*.jks` 規則保留當作雙重防護

---

## 1.3.8 Google Play Console 上架流程進行中（最後更新 2026-07-01）

- App 條目已建立：BYOB Map，`com.smalltoolsstudio.byobmap`，帳戶：Small Tools Studio
- 內部測試已發布（app-release.aab 上傳成功，簽名正確，無 debug 簽名錯誤）
- **⚠️ 內容分級問卷重要教訓**：「應用是否重點推廣或銷售通常有年齡限制的物品或活動（如菸酒）」這題**務必選「否」**。選「是」的話，即使後面所有子問題都誠實填「否」（不專注推銷酒精等），系統還是會把整個 App 判定為「著重在針對年齡限制商品促銷」，導致巴西/北美/歐洲/德國/南韓幾乎全部跳到 18+ 或 Mature 分級，完全不合理。改選「否」之後分級才正常（ESRB E、PEGI 3、USK 0、其他地區 3+）。以後如果要重填這份問卷，直接選否，不要被「App 內容確實提到酒精」這件事誤導去選是。

### 應用程式內容聲明進度（截至 2026-07-01 EOD）

| 項目 | 狀態 |
|------|------|
| 廣告聲明 | ✅ 完成（否，不投放廣告） |
| 內容分級 | ✅ 完成（全年齡，ESRB E / PEGI 3 / USK 0） |
| 目標對象和內容 | ✅ 完成 |
| 資料安全性 | ✅ 完成（含地點/大概位置、應用程式活動、裝置ID、應用程式資訊與效能四類） |
| 政府應用程式 | ✅ 完成（否） |
| 金融功能 | ✅ 完成（否） |
| 健康應用程式 | ✅ 完成（我的應用程式沒有任何健康功能） |
| 廣告 ID | ✅ 完成（否，manifest 確認無 AD_ID 權限） |
| **應用程式類別與聯絡資料** | ⏳ **待辦，明天從這裡開始** |
| **商店資訊（含截圖）** | ⏳ **待辦，明天第二項** |

### 重要發現：Firebase Performance Monitoring 未揭露問題（2026-07-01 修復）

叫 Ralph 查 AD_ID 權限時意外發現 `pubspec.yaml` 有 `firebase_performance: 0.10.1+7`，是真實引入的 SDK，不是 build cache 殘留（用 manifest 裡 ComponentRegistrar 已 merge 進最終版本確認）。這跟隱私政策原本寫的「沒有使用 Crashlytics 或其他當機回報工具」沒有直接衝突（Performance Monitoring ≠ Crashlytics），但確實會收集 App 啟動時間、畫面渲染、網路延遲等診斷資料，原本沒揭露。已修復：

1. `store-listing/privacy_policy.md` 和 `public/byob-map/privacy/index.html` 都補上一段說明 Firebase Performance Monitoring 用途，已用 `firebase deploy --only hosting` 重新部署上線
2. Google Play Console 資料安全性表單補上「應用程式資訊與效能 → 診斷資料」：收集／非暫時性／需要收集資料（不可關閉）／用途數據分析

### 操作筆記：資料安全性表單編輯入口

「資訊主頁」檢查清單裡已完成的項目（打勾的）**點不動**，這是正常的，不是被鎖住。正確編輯路徑：左側選單「監控及改善 → 政策與計畫 → 應用程式內容」→「已處理」分頁 → 每一項右邊有「管理」連結，點進去就能重新編輯並儲存。廣告 ID、資料安全性等所有已完成聲明都是走這條路徑修改的。

---

## 1.4 資料夾整理（2026-07-01）

專案資料夾重新分類，路徑異動如下：
- `scripts/` — 四支 Firebase 資料處理 Python 腳本（`add_city_field.py`、`add_new_restaurants.py`、`migrate_restaurant_types.py`、`upload_images_to_firebase.py`）
- `store-listing/` — 上架文案草稿（`play_store_listing.md`、`privacy_policy.md`）
- `philly_byob_complete_plan.md`（2025-10-13 舊版規劃文件，內容已被 CLAUDE.md / PRODUCT_BRIEF.md / Handoff.md 取代）已刪除
- 根目錄留：`CLAUDE.md`、`PRODUCT_BRIEF.md`、`CLAUDE_PROMPT_TEMPLATE.md`、`Handoff.md`（常用核心文件）+ `firebase.json`、`.firebaserc`、`.gitignore`、`.firebase/`、`public/`（Firebase CLI 必須留根目錄，動了會讓 `firebase deploy` 失效）

---

## 1.5 ✅ byobmap.com 接上 Firebase Hosting（2026-07-01 完成）

- Namecheap DNS：刪除舊 A record（`172.238.6.220`，預設停放頁），新增 A record `199.36.158.100` + TXT record `hosting-site=byob-app-5e4db`。Namecheap 之後只有網域續費/DNS 異動才需要回去動，日常不用管
- Firebase Console custom domain 驗證通過，SSL 已生效，https://byobmap.com 可正常訪問
- **網站結構（為未來多 App 共用同一網域預留）**：
  - `byobmap.com`（根目錄）→ 極簡佔位頁「App pages coming soon」，之後可做成多 App 入口頁
  - `byobmap.com/byob-map/privacy` → BYOB Map 正式隱私政策網址（已驗收，畫面正常）
  - 之後其他 App 用同一個 pattern：`byobmap.com/<app-slug>/privacy`
- 隱私政策原始檔：`public/byob-map/privacy/index.html`；根頁面：`public/index.html`
- **已填入 `store-listing/play_store_listing.md` 的 Privacy Policy URL**：https://byobmap.com/byob-map/privacy
- ⚠️ 頁面上「Small Tools Studio」開發者名稱仍是推測值，未確認，公開頁面上已可見，Neil 確認後改 `public/byob-map/privacy/index.html` 重新 `firebase deploy --only hosting`

---

## 2. 下一步工作

### ✅ Dark Mode 修復（C20）— 2026-07-01 完成

**問題：** App 完全為淺色背景設計（#F8F4EF 米白），用戶手機若開啟深色模式，畫面背景變黑，所有文字、卡片、badge 顯示全部破版。

**截圖已確認（修復前）：** RestaurantDetailPage 黑底，料理 tag、Free BYOB badge 可見但背景失控；HomePage 黑底，卡片背景消失。

**實際採用的修法（比原計畫的 force Light 更好，保留系統深色模式支援）：**
FlutterFlow Studio → Theme → Colors → Dark Mode Theme 開關，直接把 unset 欄位填值。因為大部分 widget 是綁定 theme token（不是寫死 hex），填完這幾格就直接修好了，完全不需要 Ralph 動 DSL。

Dark Mode Theme 最終數值（Neil 手動填入，Tertiary / Accent 1-4 / Info 沿用既有值未變動）：

| 欄位 | 值 |
|------|-----|
| Primary | #8B2635 |
| Secondary | #8B2635 |
| Alternate | #3A3330 |
| Primary Text | #F5F0EA |
| Secondary Text | #B0A69D |
| Primary Background | #1B1613 |
| Secondary Background | #2A2320 |
| Success | #4CAF50 |
| Error | #E5484D |
| Warning | #FFA726 |

**額外手動覆寫（Colors 面板填值後仍不夠，Neil 另外手動處理）：**
- 料理 tag（RestaurantCard + RestaurantDetailPage，Contract 14 的 tag 元件）背景寫死 `#F8F4EF`，不綁 Secondary Background token，light/dark 模式都用這個值。原因：如果 tag 背景跟著 Secondary Background 變深色，tag 的酒紅邊框文字在深色底上可讀性會不夠。
- 料理 tag 字體放大到 16px，badge 字體放大到 14px，列表卡片與詳情頁一致（原本大小未記錄）。
- 上方 filter chips（All / Italian / Mediterranean 等）本次未變動，維持既有樣式。

**驗收（截圖比對通過）：**
HomePage 列表：背景轉暖黑、卡片背景正確變深、料理 tag 保持米白背景清楚可讀、Free BYOB 綠色 badge 對比足夠。RestaurantDetailPage：背景、文字、badge、Get Directions 紅色按鈕在第一屏全部清晰。唯一未跟著切換的是搜尋欄背景（仍為白色），不影響任何失敗標準，可之後再補。

---

### 上架準備現況（2026-07-01 EOD）

1. ✅ App icon 設計 — 完成
2. ✅ Splash screen — 完成
3. ✅ APK → AAB 打包（正式簽名） — 完成
4. ✅ Internal testing track 已發布
5. ✅ 應用程式內容聲明（廣告/內容分級/目標對象/資料安全性/政府/金融/健康/廣告ID）全部完成
6. ⏳ **應用程式類別與聯絡資料** — 待辦，明天第一項，類別選 Food & Drink
7. ⏳ **商店資訊（含截圖 + feature graphic 1024×500）** — 待辦，明天第二項，截圖和 feature graphic 都還沒做
8. 待這兩項填完 → 送交審查 → 通過後才能考慮 Closed testing（需 12 位測試者、14 天）→ Production

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
| 20 | Dark Mode 修復（FlutterFlow Theme → Colors → Dark Mode Theme 手動填值 + 料理 tag/badge 手動覆寫） | ✅ | 2026-07-01 |

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
| Dark Mode 支援（Theme Colors + 料理 tag/badge 手動覆寫） | P2 | ✅ C20 完成 |
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
| Dark Mode Theme colors | 見上方 C20 表格（Primary #8B2635 … Warning #FFA726） | ⚠️ 手動填在 FlutterFlow Theme → Colors，未確認是否已同步進 DSL，Ralph push 前務必先確認沒有被覆蓋 |
| 料理 tag 背景色 | 寫死 `#F8F4EF`（不綁 theme token），light/dark 都用這個值 | ⚠️ 手動改動，未鎖入 DSL |
| 料理 tag 字體大小 | 16px | ⚠️ 手動改動，未鎖入 DSL |
| Badge 字體大小 | 14px | ⚠️ 手動改動，未鎖入 DSL |

**⚠️ Font Awesome icon 正確名稱：`FontAwesome.mapMarkedAlt`（camelCase）**
錯誤寫法：`map_marked_alt`、`mapmarkedalt` → FlutterFlow validator 會 reject。

**每個 Ralph prompt 的 Constraints 區塊必須加上：**
```
Do not change the App Bar title (must be "BYOB Map").
Do not change the map toggle icon (must be FontAwesome.mapMarkedAlt, color #FFFFFF, right padding 8).
Do not change the package name (com.smalltoolsstudio.byobmap) or display name (BYOB Map).
Do not modify the Dark Mode Theme colors in FlutterFlow Theme → Colors
  (Primary #8B2635, Secondary #8B2635, Alternate #3A3330, Primary Text #F5F0EA,
  Secondary Text #B0A69D, Primary Background #1B1613, Secondary Background #2A2320,
  Success #4CAF50, Error #E5484D, Warning #FFA726).
Do not change the cuisine tag background color on RestaurantCard or
  RestaurantDetailPage — must stay hardcoded #F8F4EF in both light and dark mode,
  not bound to a theme background token.
Do not change the cuisine tag font size (16px) or badge font size (14px) on
  RestaurantCard or RestaurantDetailPage.
```

**⚠️ 這幾項是 Neil 在 FlutterFlow Studio 手動改的，尚未確認是否已寫進 DSL（`generated_code/`）。下一個 contract push 前，先跑一次 DSL diff 確認這些值沒有被 Ralph 的 `editPage` / `updateCustomFunction` 呼叫覆蓋掉，比在 prompt 裡寫 constraint 更保險。**

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
