# BYOB App 資料欄位規格（2025-11-27）

## 資料來源
- 原始檔案：`philly_yelp_crawler/data/Philly BYOB Restaurant.xlsx`
- 後續將同步輸出 `byob_restaurants.csv` 與 `byob_restaurants.json`，欄位均依下列表格為準。

## 欄位定義
| App 欄位 | 來源欄位 | 型別 | 必填狀態 | 說明 | 範例 |
| --- | --- | --- | --- | --- | --- |
| `restaurant_id` | `WP_Post_ID` | string | 必填 | 餐廳唯一識別碼，沿用 WP ID 或未來自訂 ID。 | `1374` |
| `name` | `Name` | string | 必填 | 餐廳顯示名稱。 | `Burrata` |
| `address` | `Add` | string | 必填 | 完整地址（含城市、州、郵遞區號）。 | `1247 S 13th St, Philadelphia, PA 19147` |
| `latitude` | `Latitude` | float | 必填 | 地圖定位；需為 WGS84。 | `39.9339343` |
| `longitude` | `Longitude` | float | 必填 | 地圖定位；需為 WGS84。 | `-75.1652492` |
| `phone` | `Phone` | string | 必填 | 主要聯絡電話，統一為 `+1-xxx-xxx-xxxx` 或 `(xxx) xxx-xxxx`。 | `(215) 465-2200` |
| `website_url` | `Google_Website` | string (URL) | 必填 | 官方網站或 Google 商家網址。 | `http://www.burrataphilly.com/` |
| `yelp_url` | `Yelp_URL` | string (URL) | 必填 | Yelp 參考頁面。 | `https://www.yelp.com/biz/burrata-philadelphia` |
| `restaurant_types` | `philly_restaurant_type` | array\<enum\> | 必填 | 料理／定位標籤，可多選；用逗號拆分後轉成陣列。 | `["Italian","Seafood"]` |
| `type_other_note` | `philly_restaurant_type_other_note` | string | 條件式 | 當 `restaurant_types` 含 `other` 時需填入補充說明。 | `Tibetan` |
| `corkage_fee_type` | `philly_corkage_fee` | enum | 必填 | 開瓶費類型：`free` / `corkage_fee` / `other`。 | `corkage_fee` |
| `corkage_fee_amount` | `corkage_fee_amount` | number | 條件式 | `corkage_fee_type = "corkage_fee"` 時必填（美元，整數或小數）。 | `15` |
| `other_corkage_policy` | `other_corkage_policy` | string | 條件式 | `corkage_fee_type = "other"` 時必填，用文字描述政策。 | `首瓶免費，第二瓶 $10` |
| `wine_service_equipment` | `wine_service_equipment` | array\<enum\> | 必填 | 餐廳提供的酒具，無資料時以 `[]` 表示。 | `["wine_glasses","opener_corkscrew","ice_bucket"]` |
| `equipment_other_note` | `philly_equipment_other_note` | string | 條件式 | 當 `wine_service_equipment` 含 `other` 時補充具體說明。 | `只提供塑膠杯` |
| `byob_service_level` | `byob_service_level` | enum | 必填 | 服務等級：`self_service` / `basic_service` / `full_service` / `no_service` / `unknown`。 | `basic_service` |
| `last_verified_at` | `Date` | ISO date | 選填 | 最近一次確認日期；無值則留空。 | `2025-11-26` |
| `emails` | `Email_1~3` | array\<string\> | 選填 | 將三個欄位整合成電子郵件陣列，移除空值。 | `["contact@sansoxygen.com","burrataphilly@gmail.com"]` |

> **命名慣例**：App 端與 JSON/CSV 一律使用 `snake_case`；CSV 欄位順序依上表排列。

## 枚舉值與映射
### 料理類型 `restaurant_types`
允許值（依現有資料＋未來擴充）：`italian`, `thai`, `asian`, `mediterranean`, `french`, `fine_dining`, `seafood`, `japanese`, `mexican`, `american`, `other`。  
CSV 以 `;` 分隔，JSON 為字串陣列。若選 `other`，`type_other_note` 必填。

### 開瓶費 `corkage_fee_type`
- `free`：完全免費。  
- `corkage_fee`：固定金額，需填 `corkage_fee_amount`（純數值，單位 USD）。  
- `other`：特殊條件，如「限指定酒款」「首瓶免費」，需填 `other_corkage_policy`。

### 酒具 `wine_service_equipment`
標準值：`wine_glasses`, `opener_corkscrew`, `ice_bucket`, `shot_glasses`, `wine_storage_locker_service`, `none`, `other`。  
若欄位為空表示未知，建議填 `[]`；若為 `none`，代表確定不提供。含 `other` 時補 `equipment_other_note`。

### 服務等級 `byob_service_level`
- `self_service`：餐廳提供杯具但不主動服務，客人自取。  
- `basic_service`：人員可協助開瓶、補酒，但無冰桶等進階服務。  
- `full_service`：有完善侍酒服務。  
- `no_service`：允許 BYOB 但不提供任何協助。  
- `unknown`：資料為 `: -- 待確認 --` 之類 placeholder 時使用。

## CSV / JSON 轉換規則
1. **欄位順序與命名**：依本文件定義；CSV 第一列為 header，JSON 以物件陣列輸出。  
2. **多值欄位**：`restaurant_types`, `wine_service_equipment`, `emails` 在 CSV 以 `;` 分隔，在 JSON 以陣列呈現。  
3. **字串清理**：移除多餘空白、`\xa0`、HTML entity；電話去除尾端雙空格。  
4. **缺值處理**：  
   - 必填欄位缺值則標示為錯誤並回報。  
   - 條件式欄位若條件成立但缺值，也列入錯誤報告。  
   - 選填欄位可為空字串或 `null`（JSON）。  
5. **日期格式**：`last_verified_at` 轉為 `YYYY-MM-DD`。  
6. **數值格式**：`corkage_fee_amount` 轉為數字（保留一位小數，若輸入為整數則不顯示小數）。

## 待確認事項
- 若未來要脫離 WordPress，`restaurant_id` 是否改為獨立 UUID？目前以 WP ID 為主。  
- `restaurant_types` 是否需要拆成主類別／次類別，以利排序？目前維持平lat list。  
- `byob_service_level` 為人工標記，後續是否需加上資料來源欄位（例如 `data_source`）？可在第二階段新增。

> 如需調整欄位或枚舉值，請先更新此文件，再進行 CSV/JSON 轉換。


