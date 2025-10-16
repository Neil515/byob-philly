@echo off
chcp 65001 >nul
title 費城 BYOB 餐廳爬蟲 - 多平台整合版

echo.
echo ============================================================
echo 費城 BYOB 餐廳爬蟲 - 多平台整合版
echo Philadelphia BYOB Restaurant Crawler - Multi-Platform
echo ============================================================
echo.

REM 檢查 Python 是否安裝
python --version >nul 2>&1
if errorlevel 1 (
    echo 錯誤: 找不到 Python
    echo    請先安裝 Python 3.7 或更高版本
    echo    下載地址: https://www.python.org/downloads/
    echo.
    pause
    exit /b 1
)

echo Python 環境檢查通過
echo.

REM 檢查必要檔案
if not exist "main_crawler.py" (
    echo 錯誤: 找不到 main_crawler.py
    echo    請確認所有檔案都已正確安裝
    echo.
    pause
    exit /b 1
)

if not exist "yelp_config.py" (
    echo 錯誤: 找不到 yelp_config.py
    echo    請確認所有檔案都已正確安裝
    echo.
    pause
    exit /b 1
)

echo 檔案檢查通過
echo.

REM 檢查 API Key 是否已設定
python -c "from yelp_config import YELP_API_KEY; exit(0 if YELP_API_KEY != 'YOUR_API_KEY_HERE' else 1)" 2>nul
if errorlevel 1 (
    echo 警告: Yelp API Key 尚未設定
    echo    請在 yelp_config.py 中設定你的 Yelp API Key
    echo    或修改 main_config.py 關閉 Yelp 爬蟲
    echo.
)

python -c "from google_config import GOOGLE_API_KEY; exit(0 if GOOGLE_API_KEY != 'YOUR_GOOGLE_API_KEY_HERE' else 1)" 2>nul
if errorlevel 1 (
    echo 警告: Google Places API Key 尚未設定
    echo    請在 google_config.py 中設定你的 Google Places API Key
    echo    或修改 main_config.py 關閉 Google Places 爬蟲
    echo.
)

echo 開始執行多平台爬蟲...
echo.

REM 執行主控程式
python main_crawler.py

REM 保持視窗開啟
echo.
echo 按任意鍵關閉視窗...
pause >nul
