# BYOB Finder Philadelphia — Prompt Templates

這個檔案是給 Claude Code (Ralph) 使用的 build prompt 模板，也是 Neil 寫 contract prompt 的標準格式。

---

## 每次 contract 前必讀的檔案

- `CLAUDE.md`
- `PRODUCT_BRIEF.md`

---

## 1. Build Prompt Template

```
先讀 CLAUDE.md、PRODUCT_BRIEF.md，再開始。

Active contract:
[Contract 編號 + 名稱，例如 Contract 1: Theme Setup + ListView Binding]

Goal:
[一段話說明用戶現在遇到什麼問題，以及修復後用戶感受到什麼。]

FlutterFlow workspace folder:
[本地 FlutterFlow workspace 路徑]

Before making any changes, state your planned approach in 3–4 lines.
Then implement.

Scope（可以改的）:
[頁面名稱、元件名稱]

Allowed changes inside scope:
- 重寫整個受影響頁面
- 重建整個受影響元件
- 調整 Theme 設定（顏色、字體、圓角）
- 重整頁面導航流程

Constraints（不能改或加的）:
[明確列出]

Permanent UX constraints（所有 contract 都適用）:
- 開瓶費資訊必須在列表卡片上可見
- 導航按鈕必須在詳情頁第一屏可見
- "Free BYOB" 視覺上必須與 "Corkage Fee" 有明顯區別
- 沒有登入牆
- 所有用戶看到的文字是英文

Success Criteria:
[精確的可測量結果，例如：]
- [ ] ListView 顯示 Firestore restaurants 集合，卡片重複多筆
- [ ] 卡片顯示：cover_image_url 圖片 + name + philly_restaurant_type + corkage badge
- [ ] 開瓶費 badge：free=綠色 "Free BYOB"，corkage_fee=灰色 "$X"，other=橘色 "Ask Us"

Required self-audit before DONE:
Before outputting <promise>DONE</promise>, verify:
1. 所有 success criteria 都達到了
2. FlutterFlow 預覽可以正常開啟修改後的頁面，無 error
3. Firestore 資料正確載入到列表
4. 修改後的完整流程端對端跑通
5. 沒有動到 scope 外的頁面或元件
6. 沒有明顯的差劣 UX 殘留
7. 所有用戶看到的文字是英文
8. 截圖修改後的頁面等 Neil 確認

Completion Signal:
Output <promise>DONE</promise> only when all success criteria are met.
Then take a screenshot of the changed page in FlutterFlow preview.
```

---

## 2. Self-Validate Prompt Template

```
先讀 CLAUDE.md，再開始。

Contract just built:
[Contract 編號 + 名稱]

Success criteria to validate:
[完整複製 build prompt 的 success criteria]

Also validate these UX checks:
- 每個改動頁面的主要動作顯而易見
- 開瓶費資訊在卡片層可見
- 導航按鈕在詳情頁第一屏
- 沒有登入牆
- 手機可用性（375px 寬可以正常使用）
- 所有用戶看到的文字是英文

Validate by:
- 在 FlutterFlow 預覽畫面點擊修改後的頁面與流程
- 確認 Firestore 欄位名稱與型別對應正確
- 確認沒有加入不相關的功能
- 誠實說明任何剩餘的 UX 不確定性

Output <validation>PASS</validation> only if function and UX both pass.
Output <validation>FAIL</validation> with a brief reason if anything fails.
```

---

## 3. Contract 完成流程

1. Ralph 輸出 `<promise>DONE</promise>` + 截圖
2. Neil 把截圖給 Claude Cowork 確認視覺
3. Cowork 審查：pass 或提出修正
4. Pass 後更新 `Handoff.md` 的完成狀態
5. `/clear` → 開新對話 → 下一個 contract

---

## Contract 清單

| # | Contract 名稱 | 狀態 |
|---|--------------|------|
| Pre-A | Firebase Storage 圖片遷移（Python script） | ⏳ |
| Pre-B | 建立 FlutterFlow 專案 + `flutterflow ai init` | ⏳ |
| 1 | FlutterFlow Theme 設定 + Firestore 連接 + 列表頁基礎綁定 | ⏳ |
| 2 | 餐廳卡片設計 + 開瓶費 badge（3 種顯示邏輯） | ⏳ |
| 3 | 餐廳詳情頁 + Google Maps 導航按鈕 | ⏳ |
| 4 | 篩選功能（依 philly_restaurant_type） | ⏳ |
| 5 | 地圖視圖（P2） | ⏳ |
| 6 | Near me 排序（P2，需要 GPS 權限） | ⏳ |

---

## 常用 Firestore 欄位快查

```
name                    → 餐廳名稱（String）
Add                     → 地址（String，注意大寫 A）
Phone                   → 電話（String，注意大寫 P）
Latitude                → 緯度（String/Double，注意大寫 L）
Longitude               → 經度（String/Double，注意大寫 L）
cover_image_url         → Firebase Storage URL（String）
philly_restaurant_type  → 料理類型（american / asian / italian / seafood / mediterranean / other）
philly_corkage_fee      → 開瓶費類型（free / corkage_fee / other）
corkage_fee_amount      → 開瓶費金額（Number，部分有值）
philly_equipment_other_note → 酒具說明
wine_service_equipment  → 酒具清單
byob_service_level      → 服務等級
```
