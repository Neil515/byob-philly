# SendGrid 第二封 Email 排程（模板2 & 模板3）

> **提醒**：SendGrid 不接受超過 _72 小時_ 之後的 `send_at`。請確認現在時間距離寄送時間不到 72 小時，再依照下列步驟操作。

## 0. 前置條件
- 已在 `philly_yelp_crawler/` 內生成 `mail_11.30_template2.json`、`mail_11.30_template3.json`（兩檔 `send_at` 已設定為台北時間 11/30 22:45，即 epoch `1764513900`）。
- 使用 PowerShell，路徑位於 `C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB`。

## 1. 匯入 SendGrid API Key（僅當前視窗有效）
```powershell
$env:SENDGRID_API_KEY = '<<你的 SendGrid Key>>'
```

## 2. 建立共用 Header
```powershell
$headers = @{
  "Authorization" = "Bearer $env:SENDGRID_API_KEY"
  "Content-Type"  = "application/json"
}
```

## 3. 送出模板 2（A 組 16 間餐廳）
```powershell
$body = Get-Content -Raw ".\philly_yelp_crawler\mail_11.30_template2.json"
Invoke-RestMethod -Method Post `
  -Uri "https://api.sendgrid.com/v3/mail/send" `
  -Headers $headers `
  -Body $body
```
- 成功會回傳 HTTP 202；若顯示 `send_at` 超過 72 小時，表示還沒到可排程時間。

## 4. 送出模板 3（B 組 16 間餐廳）
```powershell
$body = Get-Content -Raw ".\philly_yelp_crawler\mail_11.30_template3.json"
Invoke-RestMethod -Method Post `
  -Uri "https://api.sendgrid.com/v3/mail/send" `
  -Headers $headers `
  -Body $body
```

## 5. 驗證
- 打開 SendGrid → Email Activity，使用 `Subject` 或 `send_at` 檢查兩批是否排程成功。
- 如需取消，可在 SendGrid 後台搜尋該批 mail，或保留 API 回應 Header 的 `X-Message-Id` 做管理。

## 6. 清除 API Key（避免留在終端）
```powershell
Remove-Item Env:SENDGRID_API_KEY
```

完成以上步驟即可完成第二封 AB 測試寄送。若要改時間，記得更新兩個 JSON 裡的 `send_at` 後再重新送出。***

