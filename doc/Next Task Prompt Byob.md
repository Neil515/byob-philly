# 🍷 BYOB 專案工作規劃與進度追蹤

## 📅 當前日期：2025年10月7日

## 🚀 已完成任務

**10月4日完成工作：** ⭐ 重複檢查系統完整實作

* [x] **重複檢查核心功能** ⭐ 核心功能
  * 實作 `byob_check_duplicate_restaurant` 主檢查函數
  * 實作 `byob_calculate_name_similarity` 名稱相似度計算
  * 實作 `byob_calculate_address_similarity` 地址相似度計算
  * 實作 `byob_extract_road_name` 和 `byob_extract_house_number` 輔助函數
  * 相似度閾值設定為 80%

* [x] **自動觸發機制** ⭐ 系統整合
  * 在 `byob_create_restaurant_article` 中自動檢查重複
  * 發現重複時設為 `pending` 狀態，標記為 `pending_duplicate_review`
  * 未重複時設為 `draft` 狀態，標記為 `pending_general_review`
  * 儲存重複檢查資訊到 post meta

* [x] **後台管理介面** ⭐ 使用者介面
  * 新增審核管理頁面（`餐廳 > 審核管理`）
  * 重複檢查標籤頁，視覺化顯示相似餐廳資訊
  * 顯示相似度百分比和相似餐廳詳細資訊
  * 一鍵操作按鈕（確認重複/不重複）

* [x] **審核處理機制** ⭐ 業務邏輯
  * 實作 `byob_handle_review_confirmation` AJAX 處理函數
  * 確認重複：直接拒絕，移到垃圾桶
  * 確認不重複：立即發布並觸發通知
  * 審核通過：立即發布並觸發通知

* [x] **發布機制優化** ⭐ 流程簡化
  * 改為審核通過即立刻發布
  * 與 WordPress 文章管理頁面發布機制一致
  * 觸發 `transition_post_status` hook
  * 自動發送推薦成功通知

* [x] **抽獎活動規劃** ⭐ 行銷準備
  * 完成抽獎活動完整規劃文檔
  * 設計活動規則、獎品配置、技術實作
  * 建立行銷素材規劃和預算分析

**10月5日完成工作：** ⭐ 抽獎系統完整實作

* [x] **抽獎系統核心功能** ⭐ 核心功能
  * 實作 `byob_record_lottery_participant` 記錄推薦者參與抽獎
  * 實作 `byob_execute_lottery` 執行隨機抽獎
  * 實作 `byob_send_winner_notification` 發送中獎通知
  * 實作 `byob_generate_winner_notification_html` 生成中獎通知 HTML

* [x] **Post Type 註冊** ⭐ 資料結構
  * 註冊 `lottery_participant` Post Type（抽獎參與者）
  * 註冊 `lottery_result` Post Type（抽獎結果）
  * 設定適當的標籤和選單位置

* [x] **後台管理介面** ⭐ 使用者介面
  * 新增抽獎管理頁面（`餐廳 > 抽獎管理`）
  * 參與者統計和清單顯示
  * 社群分享機會管理功能
  * 歷史抽獎結果查看

* [x] **Email 模板整合** ⭐ 行銷整合
  * 修改推薦成功 Email 模板，加入抽獎說明
  * 加入社群分享獎勵說明
  * 統一 CTA 按鈕樣式

* [x] **自動記錄機制** ⭐ 系統整合
  * 餐廳審核通過時自動記錄推薦者
  * 避免重複記錄機制
  * 自動計算基本抽獎機會

**10月6日完成工作：** ⭐ 抽獎系統測試與優化

* [x] **抽獎系統測試與修正** ⭐ 核心功能
  * 修正抽獎參與者欄位名稱統一問題（participant_name → customer_recommender_name）
  * 修正推薦者姓名空白問題（byob_auto_send_invitation_on_publish 函數中缺少 $recommender_name 變數）
  * 實作動態月份選擇功能（JavaScript AJAX）
  * 完成抽獎系統完整測試

* [x] **未中獎通知系統** ⭐ 新增功能
  * 實作 `byob_send_non_winner_notifications` 未中獎通知函數
  * 實作 `byob_send_non_winner_notification` 單一通知函數
  * 實作 `byob_generate_non_winner_notification_html` HTML 模板生成
  * 智能去重機制：同 Email 只發送一封未中獎通知
  * 整合到抽獎執行流程中自動觸發

* [x] **獎項配置優化** ⭐ 業務邏輯
  * 修改獎項名稱：一等獎→一獎、二等獎→二獎
  * 移除三等獎配置
  * 更新獎品內容：一獎→進口酒商電子禮券、二獎→高級進口紅白酒杯
  * 移除所有金額標示
  * 更新所有相關 Email 模板

* [x] **額外抽獎機會優化** ⭐ 用戶體驗
  * 修改分享方式：使用短網址 https://reurl.cc/4N01nL
  * 簡化操作步驟：從4步簡化為3步
  * 移除重複的標記和回覆步驟
  * 優化社群帳號標記說明

---

## 🔴 今日工作重點（10月7日）→ 抽獎活動文章與測試

### 🚨 優先級 1：抽獎活動文章製作

**目標：**
完成抽獎活動的社群媒體宣傳文章，並重新測試推薦餐廳成功通知功能。

**需求描述：**
1. 製作抽獎活動宣傳文章
2. 測試推薦成功通知 Email 功能
3. 驗證所有 Email 模板內容正確性
4. 確保抽獎系統完整運作

**預估時間：** 3-4 小時

---

### **階段 1：抽獎活動文章製作（90 分鐘）**

* [ ] 設計抽獎活動宣傳文章
  * 撰寫吸引人的標題和內容
  * 包含獎品資訊（一獎：進口酒商電子禮券、二獎：高級進口紅白酒杯）
  * 說明參與方式（推薦餐廳獲得抽獎機會）
  * 加入公平性說明（Mersenne Twister 演算法）
  * 設計視覺元素和排版

* [ ] 準備社群媒體素材
  * 製作 Facebook 貼文版本
  * 製作 Instagram 貼文版本
  * 準備相關圖片或視覺素材
  * 設定適當的標籤和標記

* [ ] 建立活動連結
  * 建立抽獎活動的專屬連結
  * 更新 Email 中的分享連結
  * 測試連結功能正常

---

### **階段 2：推薦成功通知重新測試（60 分鐘）**

* [ ] 準備測試資料
  * 提交新的餐廳推薦表單
  * 確保填寫推薦者姓名和 Email
  * 使用不同的測試資料避免重複

* [ ] 測試推薦成功通知
  * 在後台審核通過餐廳
  * 檢查推薦成功 Email 是否正確發送
  * 驗證 Email 內容包含：
    * 正確的獎品資訊（一獎、二獎）
    * 額外抽獎機會說明（3步驟版本）
    * 正確的分享連結（https://reurl.cc/4N01nL）
    * 社群帳號標記說明

* [ ] 測試抽獎參與者記錄
  * 檢查「抽獎參與者」頁面
  * 確認推薦者姓名正確顯示
  * 驗證 Email 和餐廳資訊正確

---

### **階段 3：Email 模板完整驗證（60 分鐘）**

* [ ] 驗證推薦成功通知 Email
  * 檢查所有文字內容正確性
  * 確認獎品資訊更新（移除三等獎）
  * 驗證額外抽獎機會步驟簡化
  * 測試 Email 樣式和格式

* [ ] 驗證未中獎通知 Email
  * 測試未中獎通知功能
  * 檢查 Email 內容包含公平性說明
  * 確認獎品資訊正確
  * 驗證去重機制正常運作

* [ ] 驗證中獎通知 Email
  * 測試中獎通知功能
  * 檢查獎品詳情正確
  * 確認領獎說明完整
  * 驗證 Email 樣式統一

---

### **階段 4：系統整合測試（30 分鐘）**

* [ ] 完整流程測試
  * 從表單提交到抽獎執行的完整流程
  * 測試所有 Email 通知功能
  * 驗證資料一致性

* [ ] 錯誤處理測試
  * 測試邊界條件
  * 驗證錯誤訊息正確顯示
  * 確認系統穩定性

---

## 🎯 今日成功標準（10月7日）

* [ ] ✅ 抽獎活動宣傳文章完成
* [ ] ✅ 社群媒體素材準備完成
* [ ] ✅ 活動連結建立並測試
* [ ] ✅ 推薦成功通知 Email 測試通過
* [ ] ✅ 所有 Email 模板內容驗證完成
* [ ] ✅ 抽獎參與者記錄功能正常
* [ ] ✅ 未中獎和中獎通知功能正常
* [ ] ✅ 系統整合測試通過

---

## 🟡 後續工作重點（10月8日）— 自動回覆系統實作

### A. 顧客推薦表單自動回覆系統

* [ ] 設計自動回覆 Email 模板
* [ ] 修改 Apps Script 加入自動回覆功能
* [ ] 設定抽獎活動說明
* [ ] 測試自動回覆流程

### B. 社群推廣素材製作

* [ ] 準備 IG Reels 腳本和素材
* [ ] 設計 IG Story 投票內容
* [ ] 製作推薦表單推廣貼文
* [ ] 建立內容日曆和發布時程

### C. 抽獎活動推廣準備

* [ ] 設計抽獎活動宣傳素材
* [ ] 準備社群媒體推廣內容
* [ ] 建立抽獎活動頁面
* [ ] 設計推廣活動流程

---

## ✅ 後續成功標準（10月8日）

* [ ] 自動回覆系統實作完成
* [ ] 社群推廣素材準備完成
* [ ] 抽獎活動推廣準備完成
* [ ] 推廣活動準備就緒

---

## 📝 技術筆記更新

### 重複檢查系統（已完成）

**功能架構：**
* 自動觸發：在 `byob_create_restaurant_article` 中檢查重複
* 相似度計算：名稱相似度 + 地址相似度 ÷ 2
* 閾值設定：≥ 80% 視為可能重複
* 狀態管理：重複設為 `pending`，不重複設為 `draft`

**核心函數：**
* `byob_check_duplicate_restaurant`：主檢查函數
* `byob_calculate_name_similarity`：名稱相似度計算
* `byob_calculate_address_similarity`：地址相似度計算
* `byob_extract_road_name`：路名提取
* `byob_extract_house_number`：門牌號碼提取

**相似度計算邏輯：**
* 餐廳名稱：移除常見詞彙後計算字元相似度
* 地址：同一條路 + 門牌號碼相差 ≤ 2號 = 90% 相似度
* 綜合評分：名稱相似度 + 地址相似度 ÷ 2
* 閾值：≥ 80% 視為可能重複

**後台管理：**
* 審核管理頁面：`餐廳 > 審核管理`
* 重複檢查標籤頁：顯示待審核的重複餐廳
* 操作按鈕：確認重複（直接拒絕）/ 確認不重複（立即發布）
* 視覺化設計：紅色邊框標示重複檢查項目

**處理流程：**
* 確認重複：直接拒絕，移到垃圾桶，標記為 `rejected`
* 確認不重複：立即發布，觸發通知，標記為 `published`
* 審核通過：立即發布，觸發通知，標記為 `published`

### 推薦成功通知系統（已完成）

**功能架構：**
* 觸發機制：`transition_post_status` hook
* 判別邏輯：`source === 'customer_recommendation'` + `contact_person` 檢查
* 防重複機制：`_byob_recommender_notified` post meta
* Email 模板：內嵌 HTML，響應式設計

**核心函數：**
* `byob_auto_send_invitation_on_publish`：主要觸發函數
* `byob_send_recommender_notification`：發送推薦者通知
* `byob_get_restaurant_display_data`：取得餐廳資料
* `byob_generate_recommender_notification_html`：生成 HTML 內容
* `byob_format_corkage_fee`：格式化開瓶費
* `byob_format_equipment`：格式化酒器設備
* `byob_format_contact_info`：格式化聯絡資訊

**Email 模板特色：**
* 移除餐廳資訊區塊，版面更簡潔
* 按鈕樣式：`rgba(139, 38, 53, 0.7)` 背景，`#f8f9fa` 字體
* 按鈕大小：`padding: 16px 32px`，`font-size: 16px`
* 移除追蹤我們區塊，聚焦主要行動
* 推薦表單連結：`https://forms.gle/jAnvmwh2BKyVXq5M8`

### 抽獎系統優化（10月6日完成）

**欄位名稱統一修正：**
* 統一使用 `customer_recommender_name` 和 `customer_recommender_email` 作為 ACF 欄位名稱
* 修正抽獎參與者記錄時的欄位映射問題
* 確保推薦者姓名正確顯示在抽獎參與者頁面

**未中獎通知系統：**
* 實作 `byob_send_non_winner_notifications` 主要處理函數
* 實作 `byob_send_non_winner_notification` 單一通知函數
* 實作 `byob_generate_non_winner_notification_html` HTML 模板生成
* 智能去重機制：同 Email 只發送一封未中獎通知
* 自動整合到抽獎執行流程中

**獎項配置更新：**
* 獎項名稱：一等獎→一獎、二等獎→二獎、移除三等獎
* 獎品內容：一獎→進口酒商電子禮券、二獎→高級進口紅白酒杯
* 移除所有金額標示
* 更新所有相關 Email 模板

**額外抽獎機會優化：**
* 分享方式：使用短網址 https://reurl.cc/4N01nL
* 操作步驟：從4步簡化為3步，移除重複步驟
* 社群標記：明確指定 FB: @BYOB自帶酒水餐廳平台
* 驗證方式：改為回覆Email附上分享連結

**動態月份選擇功能：**
* 實作 JavaScript AJAX 動態更新功能
* 為統計區塊和參與者清單添加 CSS 類別
* 創建 AJAX 處理函數 `byob_get_monthly_participants_ajax`
* 實現選擇不同月份時自動更新統計資料

### 顧客推薦表單系統（核心功能完成）

**表單欄位設計：**
1. 餐廳名稱（必填）
2. 餐廳類型（選填，多選）
3. 餐廳地址（必填）
4. 餐廳電話（選填）
5. 開瓶費條件（必填，條件式顯示）
6. 開瓶費金額（條件顯示）
7. 開瓶費說明（條件顯示）
8. 酒器設備（選填，多選，支援「其他」）
9. 餐廳特色（選填）
10. 推薦者姓名（選填）
11. 推薦者 Email（選填）

**技術實現：**
* Google Apps Script 基於純淨版結構
* 使用「欄位設定表」工作表實現動態映射
* 支援條件式欄位處理（開瓶費邏輯）
* 餐廳類型「排除法」識別「其他」內容
* 酒器設備「排除法」識別「其他」內容
* WordPress REST API 整合，建立草稿文章
* **核心必填欄位簡化為 3 個**：restaurant_name, address, is_charged

**ACF 欄位對應（已完成）：**
* ✅ `customer_recommender_name`：推薦者姓名
* ✅ `customer_recommender_email`：推薦者 Email
* ✅ `source`：標記為 'customer_recommendation'
* ✅ `equipment_other_note`：酒器設備其他說明
* ✅ `contact_person`：空字串（不使用預設值）
* ✅ `email`：空字串（不使用預設值）

### 待實作功能

**自動回覆系統：**
* 感謝信 Email 模板
* Apps Script 自動發送功能
* 抽獎活動說明
* 錯誤處理機制

**社群推廣系統：**
* IG Reels 腳本和素材
* IG Story 投票內容
* 推廣貼文設計
* 內容日曆規劃

**抽獎活動系統：**
* 抽獎活動頁面
* 參與者名單管理
* 抽獎工具和流程
* 中獎通知系統

**KPI 追蹤系統：**
* 表單提交統計
* 轉換率追蹤
* 社群互動監控
* 週報和月報模板

---

## 🔍 參考文檔

* `wordpress/Apps script - 顧客推薦版.js`：顧客推薦表單處理程式
* `wordpress/Apps script - 純淨版.js`：餐廳業者表單處理程式
* `wordpress/functions.php`：WordPress REST API 端點和 ACF 欄位處理
* `doc/message_and_form/byob_recommender_success_notification.md`：推薦成功通知 Email 模板
* `doc/lottery_activity_planning.md`：抽獎活動完整規劃
* `doc/ai_progress_byob.md`：詳細的開發進度記錄

---

## 📊 專案進度概覽

### 已完成模組
- ✅ 餐廳業者表單系統
- ✅ 顧客推薦表單系統（核心功能）
- ✅ WordPress REST API 整合
- ✅ ACF 欄位動態映射
- ✅ 餐廳類型「其他」欄位處理
- ✅ 酒器設備「其他」欄位處理
- ✅ 開瓶費條件式邏輯
- ✅ 推薦者欄位正確儲存
- ✅ 推薦成功通知系統
- ✅ 重複檢查系統（完整實作）
- ✅ 審核管理系統（完整實作）
- ✅ 抽獎系統（完整實作）

### 進行中模組
- 🔄 抽獎活動文章製作（今日進行）

### 待開發模組
- ⏳ 自動回覆系統實作
- ⏳ 社群推廣素材製作
- ⏳ 抽獎活動推廣素材
- ⏳ KPI 追蹤儀表板
- ⏳ 行銷活動管理系統

### 新增完成模組
- ✅ 抽獎系統測試與修正（10月6日完成）
- ✅ 未中獎通知系統（10月6日完成）
- ✅ 獎項配置優化（10月6日完成）
- ✅ 額外抽獎機會優化（10月6日完成）

---

*最後更新：2025年10月7日*
*版本：v2.2*
