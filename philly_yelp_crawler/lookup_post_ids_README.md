# lookup_post_ids.py 使用說明

這支腳本會把餐廳的 WordPress 文章 ID 寫進 Excel 的 A 欄（`WP_Post_ID`），方便後續做 API 更新。

## 邏輯（白話版）
1. 讀取指定的 Excel。
2. 若有 `Slug` 欄，就直接拿來配對；如果沒有，就把 `Name` 轉成小寫＋連字號做 slug。
3. 呼叫 `https://byobmap.com/wp-json/wp/v2/restaurant?slug=...` 找對應文章。
4. 把回傳的 `id` 寫進 A 欄 `WP_Post_ID`，找不到就留空。

## 指令
```powershell
cd C:\Users\slow3\OneDrive\桌面\GitHubProjects\BYOB
python philly_yelp_crawler/lookup_post_ids.py "data/Philly BYOB Restaurant_with_websites_merged.xlsx"

# 其他檔案一樣，換成相對路徑即可
python philly_yelp_crawler/lookup_post_ids.py "data/geocode_full/Philly BYOB Restaurant google form_with_latlng.xlsx"
```
> 也可以在 `philly_yelp_crawler` 裡跑：`python lookup_post_ids.py "<檔名>"`.

## 遇到的問題 & 解法
- **一開始找不到檔案**  
  已在腳本內支援「相對於 `philly_yelp_crawler`」與「絕對路徑」。指令只要帶對路徑即可。
- **檔案沒有 `Slug` 欄**  
  腳本會自動用餐廳名稱轉 slug（全部小寫、空白改 `-`、移除符號），不需手動建立欄位。
- **要插在 A 欄**  
  已把 `WP_Post_ID` 固定插入第一欄，其餘欄位自動往後移。

