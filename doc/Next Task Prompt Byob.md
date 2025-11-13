# 🍷 BYOB 專案工作規劃

## 📅 當前日期：2025-11-13

---

## ✅ 今日摘要
- 取得有效的 Google Places / Geocoding API key，`geocode_restaurant_locations.py` 成功批次產出兩份清單的 `Latitude` / `Longitude`。
- 撰寫 `add_ids.py` 腳本，依餐廳名稱＋地址比對 WordPress 匯出結果，為 `Philly BYOB Restaurant.xlsx` 與 `Philly BYOB Restaurant google form.xlsx` 補上 `ID` 欄位。
- 初步檢視 ID 比對成果（主清單 19/43、表單 16/20），保留缺漏清單待後續人工確認。

---

## 🗓️ 明日（2025-11-14）待辦

1. **餐廳排序優化**  
   - 盤點目前列表頁排序規則（預設、驗證狀態、經緯度）並確認需求。  
   - 依資料完整度（ID、經緯度）規劃 fallback 策略，預先擬定距離排序整合方式。  
   - 調整 WordPress 查詢或前台元件，完成排序實作與測試案例。

2. **餐廳聯絡 Email 轉寫**  
   - 彙整現有發給餐廳的通知模板，確認資訊與 tone。  
   - 草擬新版英文/中文信件內容，納入排序/經緯度進度提示與 CTA。  
   - 與相關腳本（Apps Script／WP 發送流程）對照欄位，準備後續替換作業。

---

*最後更新：2025-11-13*  

