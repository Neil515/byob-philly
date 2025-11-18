## BYOB 餐廳接管 Token 批次產生備忘

此檔記錄如何在 Cloudways/WordPress 主機上，依餐廳清單一次產生 takeover token 並輸出 CSV。後續可依此流程重做，或依清單清除所有新增檔案。

---

### 1. 必要檔案與位置

| 目的 | 檔案/路徑 | 備註 |
| --- | --- | --- |
| 餐廳清單 (輸入) | `wp-content/uploads/token_generating.json` | 格式：`post_id,餐廳名稱,地址` |
| CLI 腳本 | `wp-content/mu-plugins/byob-takeover-cli.php` | 內含 `wp byob-takeovers` 指令 |
| 產出 CSV (輸出) | `wp-content/uploads/takeover_tokens_YYYYMMDD.csv` | 欄位：Post ID / Restaurant / Address / Token / Takeover Link / Expires / Status |

> **還原到初始狀態**：刪除上述輸入、輸出、以及 `mu-plugins/byob-takeover-cli.php` 即可。

---

### 2. 連線與進入 WordPress 根目錄

1. 在 Cloudways 後台 → **Launch SSH Terminal**（或自行 SSH 到主機）。
2. 登入後切換到 WordPress 目錄：
   ```bash
   cd ~/applications/<APP_ID>/public_html
   ```
   （此專案 APP_ID 為 `eakvpjqczj`）
3. 確認 `wp` 指令可用：`wp --info`

---

### 3. 執行批次產生指令

```bash
wp byob-takeovers batch \
  --input="wp-content/uploads/token_generating.json" \
  --output=takeover_tokens_YYYYMMDD.csv
```

- `--input` 指向餐廳清單（路徑含空白請用引號）。
- `--output` 為輸出檔名，會建立在 `wp-content/uploads/`。
- 路徑有問題時，可改用絕對路徑： `/home/<APP_ID>.cloudwaysapps.com/<APP_ID>/public_html/wp-content/uploads/token_generating.json`

執行過程會列出每個 Post ID 與 token，結尾看到 `Success: CSV 已輸出：...` 代表完成。

---

### 4. 下載 / 清理

1. **下載輸出檔**：用 FTP/SFTP 連到 `public_html/wp-content/uploads/`，取得 `takeover_tokens_YYYYMMDD.csv`。
2. **寄送摘要**：腳本會自動寄一封 email 到 `byobmap.tw@gmail.com`，內含檔案路徑與各餐廳的 link。
3. **回復到原狀（如僅臨時使用）**：
   - 刪除 `wp-content/uploads/token_generating.json`
   - 刪除 `wp-content/uploads/takeover_tokens_YYYYMMDD.csv`
   - 若暫時不需要 CLI 指令，刪除 `wp-content/mu-plugins/byob-takeover-cli.php`（或整個 `mu-plugins` 目錄）

完成上述步驟後，主機就會回到批次前的狀態；下次需要時只要依此備忘重新上傳清單與 CLI 腳本即可。 


