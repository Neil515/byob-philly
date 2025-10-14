#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
費城 Yelp 爬蟲環境安裝腳本
Philadelphia Yelp Crawler Environment Setup Script

自動安裝所需的 Python 套件
"""

import subprocess
import sys
import os

def install_package(package):
    """安裝 Python 套件"""
    try:
        subprocess.check_call([sys.executable, "-m", "pip", "install", package])
        print(f"✅ {package} 安裝成功")
        return True
    except subprocess.CalledProcessError as e:
        print(f"❌ {package} 安裝失敗: {e}")
        return False

def main():
    """主安裝程式"""
    print("🍷 費城 Yelp BYOB 爬蟲環境安裝")
    print("=" * 50)
    
    # 讀取 requirements.txt
    requirements_file = "requirements.txt"
    
    if not os.path.exists(requirements_file):
        print(f"❌ 找不到 {requirements_file} 檔案")
        return
    
    # 讀取套件清單
    with open(requirements_file, 'r', encoding='utf-8') as f:
        packages = [line.strip() for line in f if line.strip() and not line.startswith('#')]
    
    print(f"📦 準備安裝 {len(packages)} 個套件...")
    
    # 安裝套件
    success_count = 0
    for package in packages:
        if install_package(package):
            success_count += 1
    
    print("\n" + "=" * 50)
    print(f"📊 安裝結果：{success_count}/{len(packages)} 個套件安裝成功")
    
    if success_count == len(packages):
        print("🎉 所有套件安裝完成！可以開始使用爬蟲了")
        print("\n📝 使用方式：")
        print("   python yelp_crawler.py")
    else:
        print("⚠️ 部分套件安裝失敗，請檢查錯誤訊息")

if __name__ == "__main__":
    main()
