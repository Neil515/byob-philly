# BYOB 專案開發進度記錄

## 📅 專案概覽（更新：2025-12-16）
* Airtable 為唯一來源；Softr 網站保留預覽。  
* 行動端改用 Adalo（Mobile Only + Restaurant 範本），目標上架 App Store/Play。

---

## ✅ 2025-12-16 — 今日進度摘要（Adalo）
1) **Airtable 類別合併顯示**  
   * 新增公式欄位 `type_display = ARRAYJOIN({philly_restaurant_type}, " · ")`，多選類別可一次顯示；保持原多選欄位供篩選。  
2) **列表文字與圖片綁定**  
   * Magic Text 一律用 Current Restaurant’s（避免整集合）。  
   * 描述可在同一 Text 以空格/符號分隔；需換行則按 Enter。  
   * 圖片：Image Source 選 URL → `fields > cover_image > url`（或 thumbnails.large.url）；缺圖可用 placeholder。  
3) **地圖設定（使用經緯度）**  
   * Marker Collection: Restaurants；Number of markers: Multiple。  
   * Marker Address 僅一行輸入 `[Latitude], [Longitude]`（Magic Text 插入，逗號空格分隔），確保欄位為數字且無空值。  
   * 可設定 Click Action → Open Link `https://maps.google.com/?q=[Latitude],[Longitude]`。  
4) **欄位同步與快取**  
   * `cover_image`（附件）與 `type_display` 已同步到 External Collection；若看不到新欄位，Refresh Schema 即可。

---

## 🔭 下一步（詳見《Next Task Prompt Byob.md》12/17）
* 地圖：確認所有筆數有標記，點擊開導航。  
* 圖片：列表全面顯示封面，無圖用預設。  
* 篩選：類別 chips / 搜尋（用 `type_display` 或原多選，含 All 清空）。
