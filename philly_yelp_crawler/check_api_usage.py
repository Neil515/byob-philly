#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
檢查 Google Places API 使用量
"""

import re
from pathlib import Path

def check_api_usage():
    """檢查日誌檔案中的 API 使用量"""
    log_file = Path('philly_email_searcher.log')
    
    if not log_file.exists():
        print("❌ 日誌檔案不存在")
        return
    
    with open(log_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 尋找 API 請求總數
    matches = re.findall(r'API 請求總數: (\d+)', content)
    
    print("="*60)
    print("Google Places API 使用量統計（從日誌檔案）")
    print("="*60)
    
    if matches:
        total_requests = sum(int(x) for x in matches)
        batch_count = len(matches)
        
        print(f"處理批次數: {batch_count}")
        print(f"總 API 請求數: {total_requests}")
        print(f"\n各批次請求數:")
        for i, req_count in enumerate(matches, 1):
            print(f"  批次 {i}: {req_count} 次")
        
        print(f"\n{'='*60}")
        print("免費額度資訊:")
        print(f"  Google Places API 免費額度: 每月 100,000 次")
        print(f"  已使用: {total_requests} 次")
        print(f"  剩餘額度: {100000 - total_requests} 次")
        print(f"  使用率: {(total_requests/100000*100):.2f}%")
        
        if total_requests >= 100000:
            print("\n⚠️  警告：已超過免費額度！")
        elif total_requests >= 90000:
            print("\n⚠️  警告：接近免費額度上限！")
        else:
            print("\n✅ 仍在免費額度內")
    else:
        print("未找到 API 請求統計資訊")
    
    print("="*60)
    print("\n💡 提醒：")
    print("這只是本地日誌檔案的統計，實際使用量請到 Google Cloud Console 查看")
    print("Google Cloud Console: https://console.cloud.google.com/apis/dashboard")

if __name__ == "__main__":
    check_api_usage()

