# SendGrid 批次排程寄信流程（白話版）

以下流程適用於以 `takeover_tokens_20251118_copy.csv` 類型的資料表為來源，透過 SendGrid Email API 批次寄送並排程送出邀請信。  
每次寄信前照著步驟檢查即可，避免漏項。

---

## 1. 準備 CSV（資料來源）
1. 開啟 `philly_yelp_crawler/takeover_tokens_20251118_copy.csv`。
2. **檢查欄位**：需包含 `Restaurant`, `Restaurant URL`, `Takeover Link`, `Email_1~Email_3`。
3. **修正格式**：確保每列逗號正確、Email 不要黏在一起（可用 Excel 找 `"`/`,` 判斷）。
4. 另存新檔（建議複製一份備份），方便之後回溯。

---

## 2. 產出 JSON（`testmail.json`）
1. 在 `philly_yelp_crawler/` 下建立或更新 `testmail.json`。
2. 結構範例：
   ```json
   {
     "from": { "email": "join-us@byobmap.com", "name": "BYOB Philly Team" },
     "personalizations": [
       {
         "to": [
           { "email": "info@restaurant.com" },
           { "email": "owner@restaurant.com" }
         ],
         "subject": "Your Restaurant Was Recommended by the Philly BYOB Community — Help Us Keep It Accurate",
         "dynamic_template_data": {
           "restaurant_name": "A Mano",
           "takeover_link": "https://byobmap.com/takeover-restaurant?token=xxxx",
           "listing_url": "https://byobmap.com/byob-restaurant/a-mano/"
         }
       }
     ],
     "template_id": "d-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
     "send_at": 1763555280,
     "batch_id": "N2QzNDk0ZDkt..."
   }
   ```
3. **personalizations**：依 CSV 逐列展開，共 31 筆；同餐廳多個 Email 全部放在 `to` 陣列。

---

## 3. 建立 `batch_id`
1. 在 PowerShell（專案根目錄）執行：
   ```powershell
   curl.exe -X POST https://api.sendgrid.com/v3/mail/batch `
     -H "Authorization: Bearer SG.xxxxxx" `
     -H "Content-Type: application/json"
   ```
2. 記下回傳的 `batch_id`，寫入 `testmail.json`，方便日後取消排程。

---

## 4. 設定排程時間 `send_at`
1. 需求時間（台北） → 轉 UTC → 再轉 Unix timestamp。  
   例如台北 `2025-11-19 20:28` → UTC `12:28` → `1763555280`。
2. PowerShell 換算範例：
   ```powershell
   [int](Get-Date "2025-11-19T20:28:00+08:00" -AsUTC | Get-Date -UFormat %s)
   ```
3. 將結果寫入 `testmail.json` 的 `send_at`。

---

## 5. 排程寄信
1. 確認 `testmail.json` 已包含：
   - `from`
   - 31 筆 `personalizations`
   - `template_id`
   - `send_at`
   - `batch_id`
2. 執行：
   ```powershell
   curl.exe -X POST https://api.sendgrid.com/v3/mail/send `
     -H "Authorization: Bearer SG.xxxxxx" `
     -H "Content-Type: application/json" `
     --data "@C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB\philly_yelp_crawler\testmail.json"
   ```
3. 出現 `202 Accepted` 表示 SendGrid 已接受排程。

---

## 6. 驗證排程
1. 登入 SendGrid → 左側 `Activity → Email Activity`。
2. 搜尋任一收件人，狀態應為 `Scheduled`；寄出後會變 `Processed/Delivered`。
3. 建議把帳號時區調成 `Asia/Taipei`，比較好對時間。

---

## 7. 取消排程（若需要）
1. 在排程尚未觸發前執行：
   ```powershell
   curl.exe -X DELETE https://api.sendgrid.com/v3/user/scheduled_sends/<batch_id> `
     -H "Authorization: Bearer SG.xxxxxx" `
     -H "Content-Type: application/json"
   ```
2. 回傳 204 表示取消成功；Activity 會看到 `Cancelled`。

---

## 8. 常見檢查清單
- API Key 是否具備 Mail Send 權限？
- Template ID 是否正確、信件主旨是否已設定？
- CSV 是否有人為修改導致欄位對齊錯誤？
- 是否確實加入 `batch_id` 以便後續控制？
- Activity 中是否有 `Bounced` / `Blocked` 需要後續處理？

照此流程即可快速複製同樣的 SendGrid 排程寄信作業。下次只要更新 CSV、timestamp 與 `personalizations` 內容即可。  


