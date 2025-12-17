# Airtable 隨機填入 placeholder 圖片腳本

目的：依餐廳類型在附件欄位填入對應的 placeholder，且同類型內隨機挑一張。僅針對**目前沒有圖片**的紀錄，不覆蓋已有人為上傳的圖片。

## 前置欄位
- 餐廳類型欄位（Single/Multi select/文字皆可）：預設 `philly_restaurant_type`
- 圖片附件欄位：預設 `cover_image`
- 資料表：預設 `Restaurants`
- placeholder 來源：建議用 WordPress 媒體庫的公開 URL

## 餐廳類型清單（拆解後僅保留單一類型）
- American
- Asian
- Chinese
- French
- Fine dining
- Indian
- Italian
- Japanese
- Mediterranean
- Mexican
- Seafood
- Thai
- Vegan
- other

## Airtable Scripting 腳本
1. 進入 Airtable Base → Extensions → Scripting。
2. 貼上腳本後，依實際欄位名稱調整 `tableName`、`typeField`、`imageField`，並把 `placeholders` 內的 example URL 換成你的 WP 圖片網址（同類型可放 1-3 張，會隨機挑一張）。
3. 按 Run，即可批次填入空白圖片。

```javascript
/**
 * 依餐廳類型填入對應 placeholder（隨機），只處理空白圖片。
 */
const tableName = "Restaurants";              // TODO: 換成你的表名
const typeField = "philly_restaurant_type";   // TODO: 餐廳類型欄位
const imageField = "cover_image";             // TODO: 附件欄位

// 依單一餐廳類型對應的 placeholder URL（example，請自行替換為 WP 公開圖）
// 每個類型可放多張，Airtable 會隨機挑一張
const placeholders = {
  "American": [
    "https://byobmap.com/wp-content/uploads/2025/11/byob-steak-medium-rare.webp",
  ],
  "Asian": [
    "https://byobmap.com/wp-content/uploads/2025/12/placeholder-japanese-ramen-byob-restaurant-philadelphia.webp",
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_chinese_byob_hotpot.webp"
  ],
  "Chinese": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_chinese_byob_hotpot.webp"
  ],
  "French": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_french_byob_finedining-2.webp",
  ],
  "Fine dining": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_french_byob_finedining-2.webp"
  ],
  "Indian": [
    "https://byobmap.com/wp-content/uploads/2025/12/placeholder-indian-byob-restaurant-philadelphia.webp",
    "https://byobmap.com/wp-content/uploads/2025/12/placeholder-indian-curry-naan-byob-restaurant-philadelphia.webp"
  ],
  "Italian": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_italian_byob-1.webp",
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_italian_byob-2.webp",
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_italian_byob-3.webp",
    "https://byobmap.com/wp-content/uploads/2025/11/italian-restaurant-logo-placeholder-3.webp",
    "https://byobmap.com/wp-content/uploads/2025/12/placeholder-italian-byob-restaurant-philadelphia.webp",
	"https://byobmap.com/wp-content/uploads/2025/12/placeholder-italian-carbonara-byob-restaurant-philadelphia.webp"
  ],
  "Japanese": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_japanese_byob_sushibar-2.webp",
	"https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_japanese_byob_sushibar-1.webp",
	"https://byobmap.com/wp-content/uploads/2025/11/omakase-chef-preparing-sushi-logo.webp",
	"https://byobmap.com/wp-content/uploads/2025/11/omakase-official-logo-coming-soon.webp",
	"https://byobmap.com/wp-content/uploads/2025/11/omakase-placeholder-logo-text-overlay.webp"
  ],
  "Mediterranean": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_mediterranean_byob_meze.webp",
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_mediterranean_byob_fishplate.webp"
  ],
  "Mexican": [
    "https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_mexican_byob-1.webp",
    "https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_mexican_byob-2.webp"
  ],
  "Seafood": [
    "https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_seafood_byob-2.webp",
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_mediterranean_byob_fishplate.webp"
  ],
  "Thai": [
    "https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_thai_byob_assorteddishes.webp",
  ],
  "Vegan": [
    "https://example.com/placeholder/vegan-1.webp"
  ],
    "pizza": [
    "https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_italian_pizza_byob-1.webp",
    "https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_italian_pizza_byob-2.webp"
  ],
    "ramen": [
	"https://byobmap.com/wp-content/uploads/2025/12/placeholder-tonkotsu-ramen-byob-japanese-restaurant-philadelphia.webp",
	"https://byobmap.com/wp-content/uploads/2025/12/placeholder-japanese-ramen-byob-restaurant-philadelphia.webp"
  ],  
  "other": [
	"https://byobmap.com/wp-content/uploads/2025/10/placeholder_logo_georgian_byob_khachapuri-2.webp",
	"https://byobmap.com/wp-content/uploads/2025/11/placeholder_logo_georgian_byob_khachapuri-1.webp",
  ]
};

// 正規化：支援 single/multi select 或純文字，拆成單一類型，取第一個非 other
const normalizeType = (value) => {
  if (!value) return "other";
  const asString = Array.isArray(value)
    ? value.map((v) => (v?.name ?? v ?? "")).filter(Boolean).join(",")
    : (value?.name ?? value ?? "");
  const cleaned = String(asString).replace(/\u00a0/g, " ");
  const parts = cleaned
    .split(",")
    .map((s) => s.trim())
    .filter(Boolean);
  const primary = parts.find((p) => p.toLowerCase() !== "other");
  return primary || "other";
};

const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];

const table = base.getTable(tableName);
const query = await table.selectRecordsAsync({ fields: [typeField, imageField] });

const updates = [];
for (const record of query.records) {
  const alreadyHasImage = (record.getCellValue(imageField) || []).length > 0;
  if (alreadyHasImage) continue;

  const typeKey = normalizeType(record.getCellValue(typeField));
  const pool = placeholders[typeKey] || placeholders["other"];
  updates.push({
    id: record.id,
    fields: { [imageField]: [{ url: pick(pool) }] }
  });
}

const total = updates.length;
let processed = 0;
while (updates.length) {
  const batch = updates.splice(0, 50); // Airtable 限制：一次最多 50 筆
  await table.updateRecordsAsync(batch);
  processed += batch.length;
}

output.markdown(`完成填入 ${processed} 筆（僅處理原本沒有圖片的紀錄）。`);
```

