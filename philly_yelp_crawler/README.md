# 🍷 費城 Yelp BYOB 餐廳爬蟲

Philadelphia Yelp BYOB Restaurant Crawler

## 📋 專案概述

這個爬蟲專門用於收集費城地區的 BYOB（Bring Your Own Bottle）餐廳資訊，作為費城 BYOB 專案的資料收集工具。

## 🎯 功能特色

- **多關鍵字搜尋**：使用多個 BYOB 相關關鍵字進行全面搜尋
- **智能去重**：自動識別和去除重複餐廳
- **信心度評估**：根據搜尋關鍵字評估資料可信度
- **多格式輸出**：支援 CSV 和 JSON 格式
- **詳細日誌**：完整的爬取過程記錄
- **友善延遲**：避免對 Yelp 伺服器造成負擔

## 📁 檔案結構

```
philly_yelp_crawler/
├── yelp_crawler.py          # 主要爬蟲程式
├── config.py                # 配置檔案
├── requirements.txt         # Python 套件需求
├── install_requirements.py  # 自動安裝腳本
├── README.md               # 說明文件
└── 輸出檔案/
    ├── philly_byob_restaurants.csv   # CSV 格式輸出
    ├── philly_byob_restaurants.json  # JSON 格式輸出
    └── crawler_log.txt              # 爬取日誌
```

## 🚀 快速開始

### 1. 安裝環境

```bash
# 方法一：使用自動安裝腳本
python install_requirements.py

# 方法二：手動安裝
pip install -r requirements.txt
```

### 2. 執行爬蟲

```bash
python yelp_crawler.py
```

### 3. 查看結果

爬取完成後，會在當前目錄生成以下檔案：
- `philly_byob_restaurants.csv` - 餐廳資料（CSV 格式）
- `philly_byob_restaurants.json` - 餐廳資料（JSON 格式）
- `crawler_log.txt` - 詳細日誌

## ⚙️ 配置說明

### 搜尋關鍵字

在 `config.py` 中預設的搜尋關鍵字：

- "BYOB Philadelphia"
- "bring your own wine Philadelphia"
- "bring your own bottle Philadelphia"
- "corkage fee Philadelphia"
- "BYO Philadelphia"
- "BYOB restaurants Philadelphia"
- "bring wine Philadelphia"
- "corkage Philadelphia"

### 費城區域

支援的主要費城區域：

- Center City
- Rittenhouse Square
- Old City
- Society Hill
- Queen Village
- South Street
- Fishtown
- Northern Liberties
- University City
- Manayunk
- East Passyunk
- Fairmount

## 📊 輸出資料格式

### CSV 格式欄位

| 欄位名稱 | 說明 |
|---------|------|
| restaurant_name | 餐廳名稱 |
| address | 地址 |
| phone | 電話號碼 |
| website | 官方網站 |
| cuisine_type | 料理類型 |
| yelp_url | Yelp 頁面連結 |
| search_keyword | 找到此餐廳的搜尋關鍵字 |
| confidence_level | 信心度等級 (high/medium/low) |
| neighborhood | 所在區域 |
| crawl_date | 爬取日期時間 |
| notes | 備註 |

### 信心度評估標準

- **High（高）**：包含 "BYOB"、"bring your own"、"corkage" 等明確關鍵字
- **Medium（中）**：包含 "wine"、"bottle"、"alcohol" 等相關關鍵字
- **Low（低）**：一般餐廳相關關鍵字

## 🔧 技術規格

### 使用技術

- **Python 3.7+**
- **requests** - HTTP 請求
- **BeautifulSoup4** - HTML 解析
- **pandas** - 資料處理
- **fake-useragent** - 隨機 User-Agent

### 請求設定

- **延遲時間**：3-5 秒隨機延遲
- **重試次數**：最多 3 次
- **超時時間**：30 秒
- **User-Agent**：隨機選擇，避免被封鎖

## 📝 使用注意事項

### 法律合規

✅ **允許的行為**：
- 只抓取公開的基本資訊
- 設定合理的請求間隔
- 遵守 Yelp 的 robots.txt

❌ **禁止的行為**：
- 不抓取評論、評分、照片
- 不使用 Yelp API（需要付費）
- 不進行高頻率請求

### 技術限制

- Yelp 可能會更新網站結構，需要相應調整解析邏輯
- 部分餐廳資訊可能不在搜尋結果頁面顯示
- 建議在非尖峰時段執行爬蟲

## 🐛 故障排除

### 常見問題

1. **找不到餐廳結果**
   - 檢查網路連線
   - 確認 Yelp 網站結構是否有變化
   - 查看日誌檔案了解詳細錯誤

2. **安裝套件失敗**
   - 確認 Python 版本（建議 3.7+）
   - 更新 pip：`pip install --upgrade pip`
   - 使用虛擬環境避免套件衝突

3. **爬取速度太慢**
   - 這是正常現象，為了避免被封鎖
   - 可以調整 `config.py` 中的 `REQUEST_DELAY` 設定

## 📈 預期結果

根據費城 BYOB 市場特性，預期可以收集到：

- **總餐廳數量**：80-150 家候選餐廳
- **高信心度**：30-50 家明確標示 BYOB 的餐廳
- **中信心度**：40-70 家可能支援 BYOB 的餐廳
- **低信心度**：10-30 家需要進一步驗證的餐廳

## 🔄 後續處理

爬取完成後，建議進行以下步驟：

1. **資料清理**：檢查和修正明顯錯誤的資料
2. **重複檢查**：手動確認去重邏輯是否正確
3. **Reddit 驗證**：將結果與 Reddit 社群推薦交叉驗證
4. **官網確認**：訪問餐廳官網確認 BYOB 政策

## 📞 技術支援

如有技術問題，請：

1. 查看 `crawler_log.txt` 日誌檔案
2. 檢查 `config.py` 配置設定
3. 確認網路連線和 Python 環境
4. 參考本 README 的故障排除章節

---

**開發團隊**：BYOB 專案團隊  
**最後更新**：2025年10月14日  
**版本**：v1.0
