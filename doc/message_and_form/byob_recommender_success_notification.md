# BYOB 推薦成功通知 Email 模板

## 📧 推薦成功通知（顧客版）

### 主旨：🎉 你推薦的餐廳已成功上架 BYOB 平台！

### 內容：

```
Hi [推薦者姓名] 👋

太棒了！你推薦的「[餐廳名稱]」已經通過審核並成功上架我們的 BYOB 平台了！

🍷 餐廳資訊：
📍 地址：[餐廳地址]
💰 開瓶費：[開瓶費條件]
🥂 酒器設備：[酒器設備清單]
📞 聯絡方式：[電話/網站]

🔗 立即查看：[餐廳頁面連結]

感謝你的推薦，讓更多愛酒的朋友能找到這個好地方！你的貢獻讓台北變得更開瓶友善 🥂

🎁 抽獎提醒：
你已經獲得本月推薦抽獎資格，獎品包括餐酒券、精美酒杯或禮券！
每月月底我們會抽出幸運得主，記得關注我們的社群更新喔！

💡 繼續推薦：
知道其他可以自帶酒的餐廳嗎？歡迎繼續推薦：
👉 https://forms.gle/[顧客推薦表單連結]

Cheers！
— 台北 BYOB 小隊

🔍 參觀我們的平台 👉 https://byobmap.com/
📱 追蹤我們：
   Instagram: @byobmap_tw
   Facebook: BYOB Map Taiwan
```

---

## 📧 推薦成功通知（HTML 版本）

### HTML 模板：

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>推薦成功通知</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .restaurant-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #8B4513;
        }
        .info-item {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .cta-button {
            display: inline-block;
            background-color: #8B4513;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
            font-weight: bold;
        }
        .cta-button:hover {
            background-color: #6d3410;
        }
        .prize-section {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #ffeaa7;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            color: #8B4513;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🍷 BYOB Map</div>
            <h1 class="title">🎉 推薦成功！</h1>
        </div>

        <p>Hi <strong>[推薦者姓名]</strong> 👋</p>

        <p>太棒了！你推薦的「<strong>[餐廳名稱]</strong>」已經通過審核並成功上架我們的 BYOB 平台了！</p>

        <div class="restaurant-info">
            <h3>🍷 餐廳資訊</h3>
            <div class="info-item">
                <span class="info-label">📍 地址：</span>[餐廳地址]
            </div>
            <div class="info-item">
                <span class="info-label">💰 開瓶費：</span>[開瓶費條件]
            </div>
            <div class="info-item">
                <span class="info-label">🥂 酒器設備：</span>[酒器設備清單]
            </div>
            <div class="info-item">
                <span class="info-label">📞 聯絡方式：</span>[電話/網站]
            </div>
        </div>

        <p style="text-align: center;">
            <a href="[餐廳頁面連結]" class="cta-button">🔗 立即查看餐廳頁面</a>
        </p>

        <p>感謝你的推薦，讓更多愛酒的朋友能找到這個好地方！你的貢獻讓台北變得更開瓶友善 🥂</p>

        <div class="prize-section">
            <h3>🎁 抽獎提醒</h3>
            <p>你已經獲得本月推薦抽獎資格，獎品包括：</p>
            <ul>
                <li>🍽️ 餐酒券</li>
                <li>🥂 精美酒杯</li>
                <li>🎫 禮券</li>
            </ul>
            <p>每月月底我們會抽出幸運得主，記得關注我們的社群更新喔！</p>
        </div>

        <h3>💡 繼續推薦</h3>
        <p>知道其他可以自帶酒的餐廳嗎？歡迎繼續推薦：</p>
        <p style="text-align: center;">
            <a href="https://forms.gle/[顧客推薦表單連結]" class="cta-button">📝 推薦更多餐廳</a>
        </p>

        <div class="footer">
            <p><strong>Cheers！<br>— 台北 BYOB 小隊</strong></p>
            
            <div class="social-links">
                <p>🔍 <a href="https://byobmap.com/">參觀我們的平台</a></p>
                <p>📱 追蹤我們：</p>
                <a href="https://instagram.com/byobmap_tw">Instagram</a>
                <a href="https://facebook.com/byobmap.tw">Facebook</a>
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 📧 推薦成功通知（純文字版本）

### 純文字模板：

```
Hi [推薦者姓名] 👋

太棒了！你推薦的「[餐廳名稱]」已經通過審核並成功上架我們的 BYOB 平台了！

🍷 餐廳資訊：
📍 地址：[餐廳地址]
💰 開瓶費：[開瓶費條件]
🥂 酒器設備：[酒器設備清單]
📞 聯絡方式：[電話/網站]

🔗 立即查看：[餐廳頁面連結]

感謝你的推薦，讓更多愛酒的朋友能找到這個好地方！你的貢獻讓台北變得更開瓶友善 🥂

🎁 抽獎提醒：
你已經獲得本月推薦抽獎資格，獎品包括餐酒券、精美酒杯或禮券！
每月月底我們會抽出幸運得主，記得關注我們的社群更新喔！

💡 繼續推薦：
知道其他可以自帶酒的餐廳嗎？歡迎繼續推薦：
👉 https://forms.gle/[顧客推薦表單連結]

Cheers！
— 台北 BYOB 小隊

🔍 參觀我們的平台 👉 https://byobmap.com/
📱 追蹤我們：
   Instagram: @byobmap_tw
   Facebook: BYOB Map Taiwan
```

---

## 🔧 技術實作參數

### 動態變數清單：
- `[推薦者姓名]` - customer_recommender_name
- `[餐廳名稱]` - restaurant_name
- `[餐廳地址]` - address
- `[開瓶費條件]` - is_charged + corkage_fee_amount + corkage_fee_note
- `[酒器設備清單]` - equipment + equipment_other_note
- `[電話/網站]` - phone + website
- `[餐廳頁面連結]` - 動態生成的餐廳頁面 URL
- `[顧客推薦表單連結]` - 固定的 Google 表單連結

### 觸發條件：
- 餐廳狀態從 'draft' 變更為 'publish'
- source = 'customer_recommendation'
- customer_recommender_email 不為空
- 尚未發送過通知（檢查 post meta `_byob_recommender_notified`）

### 防重複機制：
- 使用 post meta `_byob_recommender_notified` 標記已發送
- 值設為發送時間戳記
- 發送前檢查此 meta 是否存在

---

## 📝 使用說明

1. **HTML 版本**：用於 WordPress 的 wp_mail 函數，支援豐富格式
2. **純文字版本**：作為備用格式，確保所有郵件客戶端都能正常顯示
3. **動態變數**：在發送時替換為實際的餐廳和推薦者資料
4. **連結更新**：需要更新實際的 Google 表單連結和社群媒體連結

---

## 🎯 預期效果

- **感謝推薦者**：讓推薦者感受到貢獻被重視
- **提供價值**：直接提供餐廳資訊和連結
- **鼓勵持續**：透過抽獎機制鼓勵繼續推薦
- **品牌建立**：強化 BYOB Map 的品牌形象
- **社群導流**：引導關注社群媒體獲得最新資訊
