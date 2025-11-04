#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
查詢 Google Cloud 專案資訊
"""

import requests
from google_config import GOOGLE_API_KEY

def get_project_info():
    """嘗試查詢專案資訊"""
    print("="*60)
    print("查詢 Google Cloud 專案資訊")
    print("="*60)
    
    api_key = GOOGLE_API_KEY
    print(f"API Key: {api_key[:8]}...{api_key[-4:]}")
    print()
    
    # 方法 1: 嘗試使用 Places API 查詢專案資訊
    # 注意：這個方法可能不會直接返回專案名稱，但可以測試 API Key 是否有效
    
    print("方法 1: 測試 API Key 是否有效...")
    test_url = "https://maps.googleapis.com/maps/api/place/textsearch/json"
    params = {
        'query': 'test',
        'key': api_key
    }
    
    try:
        response = requests.get(test_url, params=params, timeout=5)
        if response.status_code == 200:
            data = response.json()
            if data.get('status') == 'OK' or data.get('status') == 'ZERO_RESULTS':
                print("✅ API Key 有效")
            elif data.get('status') == 'REQUEST_DENIED':
                print("❌ API Key 無效或被拒絕")
                print(f"錯誤訊息: {data.get('error_message', '未知錯誤')}")
            else:
                print(f"API 狀態: {data.get('status')}")
        else:
            print(f"❌ HTTP 錯誤: {response.status_code}")
    except Exception as e:
        print(f"❌ 請求失敗: {e}")
    
    print("\n" + "="*60)
    print("💡 如何找到專案名稱：")
    print("="*60)
    print("1. 前往 Google Cloud Console:")
    print("   https://console.cloud.google.com/")
    print()
    print("2. 登入後，查看頂部導覽列的專案選單")
    print("   （通常在頁面左上角，顯示為下拉選單）")
    print()
    print("3. 點選專案選單，會顯示所有可用的專案")
    print()
    print("4. 如果只有一個專案，會自動顯示")
    print("   如果有多個專案，請選擇包含 Places API 的那個")
    print()
    print("5. 或者前往 API 金鑰頁面查看:")
    print("   https://console.cloud.google.com/apis/credentials")
    print("   找到您的 API Key，點選後可以看到所屬專案")
    print("="*60)

if __name__ == "__main__":
    get_project_info()

