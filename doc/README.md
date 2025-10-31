# 🍷 BYOB 專案文檔

## 📋 專案概述

BYOB (Bring Your Own Bottle) 是一個自帶酒水餐廳推薦平台，目前運營兩個獨立專案：

### 🇹🇼 台北 BYOB（主要專案）
- **定位**：台北自帶酒水餐廳推薦平台
- **階段**：核心系統完成，推廣與酒商合作階段
- **策略**：抽獎激勵 + 多平台推廣

### 🇺🇸 費城 BYOB（新專案）
- **定位**：Yelp 的 BYOB 專業補充平台
- **階段**：自動化整合完成，進入網站英文化階段
- **策略**：多平台資料收集 + Reddit 社群驗證 + 榮譽系統驅動

---

## 🚀 最新進度（2025年10月30日）

### 今日完成：餐廳列表頁顯示邏輯與統一性修復

**🎯 關鍵修復**

* ✅ **移除 district 必填限制**
  * 問題：費城餐廳沒有 `district` 欄位，導致列表頁被隱藏
  * 修復：在 `restaurant-member-functions.php` 移除 district 必填檢查
  * 影響：費城餐廳現在可正常顯示在列表頁

* ✅ **修復 other 類型顯示邏輯**
  * 問題：列表頁只顯示 `other` 而非 `Other: [description]`
  * 修復：在 `archive-restaurant.php` 增加英文 'other' 支援（`strtolower($type) === 'other'`）
  * 影響：餐廳類型與設備正確顯示 `Other: [note]` 格式

* ✅ **Notes 欄位命名統一**
  * 修改：將 "Dining Experience" 改為 "Notes"
  * 修改檔案：`functions.php`（文章內容）、`Apps script - 費城推薦版.js`（Email）
  * 建議說明：**"Anything you'd like other BYOB diners to know"**
  * 需要手動：Google 表單、Google Sheets 欄位設定表、ACF 欄位標籤

* ✅ **表單 ↔ ACF 對齊修復**
  * 單選題映射：實作 label→key 安全映射，空值寫 ''
  * Other 與備註：equipment/type 的 'other' 正確寫入 other_note
  * 前台一致：單一餐廳頁與列表頁顯示格式統一

**🔧 修改檔案**
- `restaurant-member-functions.php`：移除 district 檢查
- `archive-restaurant.php`：增加 other 類型英文支援
- `functions.php`：Notes 顯示、label→key 映射
- `single_restaurant.php`：other 顯示格式
- `Apps script - 費城推薦版.js`：Notes Email、other 處理

**✅ 驗證結果**
- 列表頁：費城餐廳正確顯示
- ACF 後台：單選題值鍵正確、other 類型描述完整
- 前台頁面：Other 顯示為 `Other: [description]` 格式

**🗓️ 明日（10/31）**
- 設計「餐廳資料確認」Google 表單（費城）
- 建立欄位映射與 Apps Script 解析邏輯
- 實作端到端寫入流程

---

## 🏗️ 技術架構

### 台北專案技術棧
```
資料收集層
├── 顧客推薦表單 (Google Form + Apps Script)
├── 餐廳業者表單 (Google Form + Apps Script)
└── 酒商名單收集 (Python 爬蟲 + Email 提取器)
    ↓
WordPress 核心
├── REST API (/byob/v1/restaurant)
├── ACF Pro（自訂欄位管理）
├── 重複檢查系統（智能檢測）
├── 審核管理系統（後台介面）
├── 抽獎系統（Mersenne Twister 演算法）
└── 推薦通知系統（SendGrid Email）
    ↓
前端展示
├── 餐廳列表與篩選
├── 餐廳詳細頁面
└── SEO 優化（Rank Math）
```

### 費城專案技術架構
```
資料收集層（已完成）
├── Yelp Fusion API 爬蟲（官方 API，穩定可靠）
├── Google Places API 爬蟲（多平台整合）
└── 智能去重系統（保留來源資訊）
    ↓
社群驗證層（進行中）
├── Reddit 社群互動（u/findingBYOB）
├── Google 表單驗證（英文介面）
└── 互動追蹤系統（Excel + Markdown）
    ↓
自動化整合層（已完成）
├── 費城專用 Apps Script（表單處理）
├── WordPress API 端點（/byob/v1/philly-restaurant）
└── 自動文章生成（英文內容模板）
    ↓
網站展示層（進行中）
├── WordPress 程式碼英文化（已完成）
├── 列表頁顯示邏輯修復（已完成）
└── 手動內容英文化（待執行）
```

---

## 📊 專案進度概覽

### 🍷 台北 BYOB 專案
- ✅ **核心系統完成**：餐廳表單、推薦通知、重複檢查、抽獎系統
- ✅ **多平台推廣**：LinkedIn、Instagram 推廣執行
- 🔄 **進行中**：酒商合作邀約、Facebook 社團推廣
- ⏳ **待執行**：自動回覆系統、KPI 儀表板

### 🍷 費城 BYOB 專案
- ✅ **資料收集完成**：269 家候選餐廳（Yelp + Google Places）
- ✅ **Reddit 帳號建立**：u/findingBYOB 準備就緒
- ✅ **互動追蹤系統**：完整的管理工具建立
- ✅ **Google 表單建立**：費城 BYOB 餐廳驗證表單完成（含 Reddit 用戶名顯示偏好欄位）
- ✅ **自動化整合完成**：Google Apps Script + WordPress API 整合
- ✅ **WordPress 程式碼英文化完成**：所有 PHP 檔案前台顯示文字已改為英文
- ✅ **ACF 系統優化完成**：欄位群組顯示問題修復，空值處理優化
- ✅ **列表頁顯示修復完成**：district 限制移除，other 類型顯示修復
- ✅ **表單 ↔ ACF 對齊完成**：label→key 映射、空值策略、資料一致性
- 🔄 **餐廳資料確認表單設計**：準備建立費城專用確認表單
- ⏳ **待執行**：Reddit 社群互動、手動內容英文化、英文網站上線、用戶招募、榮譽系統實作

---

## 📁 核心文檔

### 專案規劃文檔
- `doc/philly_byob_complete_plan.md`：費城 BYOB 完整專案計畫
- `doc/Next Task Prompt Byob.md`：工作規劃與任務追蹤
- `doc/ai_progress_byob.md`：開發進度記錄

### 社群互動文檔
- `doc/reddit_interaction_tracker.md`：Reddit 貼文記錄
- `reddit_tracker/Reddit_Interaction_Tracker.xlsx`：Reddit 互動追蹤 Excel 檔案

### 技術文檔
- `philly_yelp_crawler/`：多平台爬蟲系統
- `wordpress/`：WordPress 整合檔案
- `restaurant_crawler/`：台北專案爬蟲工具

---

## 🎯 下一步計畫

### 短期（本週）
1. **費城專案**：
   - ✅ 列表頁顯示邏輯修復完成（district 移除、other 支援）
   - ✅ Notes 欄位命名統一完成
   - ✅ 表單 ↔ ACF 對齊修復完成
   - 🔄 設計餐廳資料確認表單（費城）
   - ⏳ Reddit 社群互動（發布第一則詢問貼文）
   - ⏳ 手動內容英文化（首頁、About Us、餐廳加入頁面）

2. **台北專案**：
   - 🔄 酒商合作邀約 Email
   - 🔄 Facebook 社團推廣

### 中期（未來 1 個月）
1. **費城專案**：
   - 完成手動內容英文化改造
   - 開始 Reddit 社群互動和信譽建立
   - 建立英文版 WordPress 網站
   - 招募創始成員和種子用戶

2. **台北專案**：
   - 建立酒商合作關係
   - 優化推廣策略和 KPI 追蹤

### 長期（未來 3-6 個月）
1. **費城專案**：
   - 實作榮譽系統和遊戲化功能
   - Wine Shop 合作分潤
   - 建立可持續的商業模式

2. **多城市擴展**：
   - 評估其他城市的可行性（紐約、波士頓、舊金山）
   - 建立可複製的擴展模式

---

## 💡 關鍵策略差異

### 台北模式 vs 費城模式

**台北模式：**
- 抽獎激勵用戶推薦餐廳
- 物質獎勵驅動（酒商禮券、酒杯）
- 一次性參與為主

**費城模式升級：**
- 榮譽系統取代物質抽獎
- 專業認同和社群歸屬驅動
- 長期持續參與機制
- 創始成員特殊身份
- 更低成本、更可持續

**調整原因：**
1. **成本考量**：海外專案初期無法負擔持續的物質獎勵
2. **文化差異**：美國用戶更重視專業認同和社群地位
3. **可擴展性**：榮譽系統可以無限擴展，物質獎勵不行
4. **長期價值**：建立真正的專家社群而非獎品獵人

---

## 🚨 當前挑戰與解決方案

### Reddit 社群互動挑戰
- **挑戰**：新帳號可能被視為推廣或 spam
- **解決方案**：先建立信譽，提供有價值的建議，使用追蹤系統記錄所有互動，24 小時內回覆評論

### 內容創作挑戰
- **挑戰**：缺乏實際用餐經驗
- **解決方案**：標明資料來源，邀請用戶補充，建立內容品質檢查機制

---

*最後更新：2025年10月30日*
*版本：v7.0*