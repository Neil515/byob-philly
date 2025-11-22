# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-22

---

## ✅ 今日摘要（2025-11-22）

### 📨 SendGrid 寄送稽核指引
- 彙整 11/19 兩批餐廳 email 的追蹤重點：於 SendGrid Activity 以 `batch_id`、日期範圍與主旨搜尋，並列出 API `messages/search` 查詢範例，供快速檢視送達／退信狀態。
- 檢視 `philly_yelp_crawler/testmail.json`：確認共 31 家餐廳、8 家含多組 email、合計 40 個收件地址，作為後續寄送與報表對照基準。

### 👥 社群輕量經營藍圖
- 建立 `philly_yelp_crawler/social_channel_playbook.md`，整合 Reddit + Facebook 的貼文節奏、CTA 樣板、素材共享與 KPI 表格（聚焦推薦、接管、Email 回覆數）。
- 明確建議 Facebook 私密社團為主陣地、Reddit 固定週貼文為觸達管道，降低營運成本並確保持續導流到表單／takeover 流程。

---

## 🗓️ 明日（2025-11-23）待辦

### 1. 第二封餐廳 Email 排程
- **目標**：針對 11/19 已寄第一封但尚未回覆的餐廳，安排跟進信件，以 SendGrid 排程第二封提醒。
- **內容確認**：完成英文主體 + 中文註記，段落包含：感謝推薦、說明為何需要他們確認、快速帶入 takeover link、若已完成可回覆告知。
- **資料準備**：整理需跟進的餐廳清單（含 Email_1~n、takeover link、第一封寄送時間），並決定此次是否全量或分批寄送與排程時間。
- **技術步驟**：複用第一封寄送腳本／JSON 範本，新增 template id 或 subject、增加 `second_follow_up` 標記，將 `batch_id` 記錄在追蹤表中以便 Activity 搜尋。
- **驗收**：寄出前發測試 mail、寄出後下載 SendGrid Activity 備份，並在 `ai_progress_byob.md` 更新該批排程紀錄。

### 2. Reddit + Facebook 發文規律與固定內容
- **基礎設置**：根據 `social_channel_playbook.md` 建立每週節奏（Reddit 1 則、Facebook 1 則 + 日常互動），並在 Google Sheet/Notion 建立貼文排程表。
- **Reddit**：挑選下週主題（收集串/討論串/地圖進度/回顧），預先準備標題、內文範本與 CTA（留言或 DM 填表／takeover）。
- **Facebook**：選定社團名稱（建議 `Philly BYOB Club`），規劃每週 Spotlight 或民調貼文、置頂 `#填表入口` + `#本週任務板`，並整理好可複製的圖片或截圖。
- **CTA 與素材共享**：確認兩平台皆導向相同的推薦表單與 takeover link，並將成功案例、Email 統計等素材存入共享資料夾，方便快速貼文。
- **追蹤**：在 KPI 表中新增 11/23 週次欄位，明確記錄「貼文主題」「新增推薦數」「新接管數」「Email 回覆數」，作為後續優化依據。

---

*最後更新：2025-11-22*
