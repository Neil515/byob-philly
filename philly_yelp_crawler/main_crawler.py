#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
費城 BYOB 餐廳爬蟲 - 主控程式
Philadelphia BYOB Restaurant Crawler - Main Controller

整合 Yelp、Google Places、TripAdvisor 三個平台的爬蟲
"""

import pandas as pd
import json
from datetime import datetime
from main_config import CRAWLERS, OUTPUT_FORMATS, CONFIDENCE_THRESHOLD

# 導入各平台爬蟲
from yelp_api_crawler import YelpBYOBCrawler
from google_places_crawler import GooglePlacesBYOBCrawler
from tripadvisor_crawler import TripAdvisorBYOBCrawler

class MainBYOBCrawler:
    def __init__(self):
        """初始化主控程式"""
        self.all_results = []
        self.yelp_results = []
        self.google_results = []
        self.tripadvisor_results = []
        
    def run_yelp_crawler(self):
        """執行 Yelp 爬蟲"""
        if not CRAWLERS.get('yelp', False):
            print("跳過 Yelp 爬蟲")
            return []
        
        print("=" * 60)
        print("執行 Yelp 爬蟲")
        print("=" * 60)
        
        try:
            crawler = YelpBYOBCrawler()
            results = crawler.crawl_all_terms()
            self.yelp_results = results
            print(f"Yelp 爬蟲完成，收集到 {len(results)} 家餐廳")
            return results
        except Exception as e:
            print(f"Yelp 爬蟲執行失敗: {str(e)}")
            return []
    
    def run_google_places_crawler(self):
        """執行 Google Places 爬蟲"""
        if not CRAWLERS.get('google_places', False):
            print("跳過 Google Places 爬蟲")
            return []
        
        print("=" * 60)
        print("執行 Google Places 爬蟲")
        print("=" * 60)
        
        try:
            crawler = GooglePlacesBYOBCrawler()
            results = crawler.crawl_all_terms()
            self.google_results = results
            print(f"Google Places 爬蟲完成，收集到 {len(results)} 家餐廳")
            return results
        except Exception as e:
            print(f"Google Places 爬蟲執行失敗: {str(e)}")
            return []
    
    def run_tripadvisor_crawler(self):
        """執行 TripAdvisor 爬蟲"""
        if not CRAWLERS.get('tripadvisor', False):
            print("跳過 TripAdvisor 爬蟲")
            return []
        
        print("=" * 60)
        print("執行 TripAdvisor 爬蟲")
        print("=" * 60)
        
        try:
            crawler = TripAdvisorBYOBCrawler()
            results = crawler.crawl_all_terms()
            self.tripadvisor_results = results
            print(f"TripAdvisor 爬蟲完成，收集到 {len(results)} 家餐廳")
            return results
        except Exception as e:
            print(f"TripAdvisor 爬蟲執行失敗: {str(e)}")
            return []
    
    def merge_results(self):
        """合併所有平台的結果"""
        print("=" * 60)
        print("合併所有平台結果")
        print("=" * 60)
        
        # 合併所有結果
        all_results = []
        all_results.extend(self.yelp_results)
        all_results.extend(self.google_results)
        all_results.extend(self.tripadvisor_results)
        
        print(f"合併前總數: {len(all_results)} 家餐廳")
        
        # 去重處理
        unique_results = self.remove_duplicates(all_results)
        print(f"去重後總數: {len(unique_results)} 家餐廳")
        
        self.all_results = unique_results
        return unique_results
    
    def remove_duplicates(self, results):
        """去除重複餐廳，但保留所有來源資訊"""
        seen = set()
        unique_results = []
        
        for restaurant in results:
            # 使用餐廳名稱和地址作為唯一識別
            key = f"{restaurant.get('name', '').lower()}_{restaurant.get('address', '').lower()}"
            
            if key not in seen:
                seen.add(key)
                # 初始化來源列表
                restaurant['sources'] = [restaurant.get('source', 'Unknown')]
                unique_results.append(restaurant)
            else:
                # 如果重複，合併來源資訊
                for i, existing in enumerate(unique_results):
                    existing_key = f"{existing.get('name', '').lower()}_{existing.get('address', '').lower()}"
                    if existing_key == key:
                        # 添加新來源到來源列表
                        new_source = restaurant.get('source', 'Unknown')
                        if new_source not in existing.get('sources', []):
                            existing['sources'].append(new_source)
                        
                        # 如果新資料信心度更高，更新主要資料
                        if self.get_confidence_score(restaurant) > self.get_confidence_score(existing):
                            # 保留來源列表
                            sources = existing.get('sources', [])
                            restaurant['sources'] = sources
                            unique_results[i] = restaurant
                        break
        
        return unique_results
    
    def get_confidence_score(self, restaurant):
        """獲取信心度分數"""
        confidence = restaurant.get('confidence_score', 'Low')
        score_map = {'High': 3, 'Medium': 2, 'Low': 1}
        return score_map.get(confidence, 1)
    
    def rank_results(self):
        """對結果進行排序和評級"""
        print("=" * 60)
        print("對結果進行排序和評級")
        print("=" * 60)
        
        # 按信心度排序
        self.all_results.sort(key=lambda x: self.get_confidence_score(x), reverse=True)
        
        # 統計各信心度數量
        high_count = len([r for r in self.all_results if r.get('confidence_score') == 'High'])
        medium_count = len([r for r in self.all_results if r.get('confidence_score') == 'Medium'])
        low_count = len([r for r in self.all_results if r.get('confidence_score') == 'Low'])
        
        print(f"高信心度: {high_count} 家")
        print(f"中信心度: {medium_count} 家")
        print(f"低信心度: {low_count} 家")
        
        return self.all_results
    
    def save_results(self):
        """儲存結果到各種格式"""
        print("=" * 60)
        print("儲存結果")
        print("=" * 60)
        
        if not self.all_results:
            print("沒有結果可儲存")
            return
        
        # 儲存 CSV 格式
        if 'csv' in OUTPUT_FORMATS:
            df = pd.DataFrame(self.all_results)
            csv_file = "data/combined_byob_restaurants.csv"
            df.to_csv(csv_file, index=False, encoding='utf-8-sig')
            print(f"CSV 檔案已儲存: {csv_file}")
        
        # 儲存 JSON 格式
        if 'json' in OUTPUT_FORMATS:
            json_file = "data/combined_byob_restaurants.json"
            with open(json_file, 'w', encoding='utf-8') as f:
                json.dump(self.all_results, f, ensure_ascii=False, indent=2)
            print(f"JSON 檔案已儲存: {json_file}")
        
        # 儲存 Excel 格式
        if 'excel' in OUTPUT_FORMATS:
            try:
                df = pd.DataFrame(self.all_results)
                excel_file = "data/combined_byob_restaurants.xlsx"
                df.to_excel(excel_file, index=False, engine='openpyxl')
                print(f"Excel 檔案已儲存: {excel_file}")
            except PermissionError:
                print(f"警告: Excel 檔案儲存失敗，可能正在被其他程式使用")
                print(f"請關閉 Excel 或其他可能使用該檔案的程式，然後重新執行")
            except Exception as e:
                print(f"Excel 儲存錯誤: {str(e)}")
        
        # 儲存高信心度餐廳
        high_confidence_restaurants = [r for r in self.all_results if r.get('confidence_score') == 'High']
        if high_confidence_restaurants:
            df_high = pd.DataFrame(high_confidence_restaurants)
            high_file = "data/high_confidence_byob_restaurants.csv"
            df_high.to_csv(high_file, index=False, encoding='utf-8-sig')
            print(f"高信心度餐廳已儲存: {high_file}")
    
    def generate_report(self):
        """生成爬取報告"""
        print("=" * 60)
        print("生成爬取報告")
        print("=" * 60)
        
        # 統計來源分布
        source_stats = {}
        for restaurant in self.all_results:
            sources = restaurant.get('sources', ['Unknown'])
            for source in sources:
                source_stats[source] = source_stats.get(source, 0) + 1
        
        # 統計交叉驗證情況
        cross_platform_count = len([r for r in self.all_results if len(r.get('sources', [])) > 1])
        single_platform_count = len([r for r in self.all_results if len(r.get('sources', [])) == 1])
        
        report = {
            'crawl_date': datetime.now().isoformat(),
            'total_restaurants': len(self.all_results),
            'platforms_used': [k for k, v in CRAWLERS.items() if v],
            'confidence_distribution': {
                'high': len([r for r in self.all_results if r.get('confidence_score') == 'High']),
                'medium': len([r for r in self.all_results if r.get('confidence_score') == 'Medium']),
                'low': len([r for r in self.all_results if r.get('confidence_score') == 'Low'])
            },
            'platform_results': {
                'yelp': len(self.yelp_results),
                'google_places': len(self.google_results),
                'tripadvisor': len(self.tripadvisor_results)
            },
            'source_distribution': source_stats,
            'cross_platform_verification': {
                'cross_platform_restaurants': cross_platform_count,
                'single_platform_restaurants': single_platform_count,
                'verification_rate': round(cross_platform_count / len(self.all_results) * 100, 2) if self.all_results else 0
            }
        }
        
        # 儲存報告
        report_file = "data/crawl_report.json"
        with open(report_file, 'w', encoding='utf-8') as f:
            json.dump(report, f, ensure_ascii=False, indent=2)
        print(f"爬取報告已儲存: {report_file}")
        
        # 顯示報告摘要
        print("\n爬取報告摘要:")
        print(f"爬取日期: {report['crawl_date']}")
        print(f"總餐廳數: {report['total_restaurants']}")
        print(f"使用平台: {', '.join(report['platforms_used'])}")
        print(f"信心度分布: 高({report['confidence_distribution']['high']}) 中({report['confidence_distribution']['medium']}) 低({report['confidence_distribution']['low']})")
        print(f"各平台原始結果: Yelp({report['platform_results']['yelp']}) Google Places({report['platform_results']['google_places']}) TripAdvisor({report['platform_results']['tripadvisor']})")
        print(f"來源分布: {report['source_distribution']}")
        print(f"交叉驗證: {report['cross_platform_verification']['cross_platform_restaurants']} 家餐廳在多個平台出現 ({report['cross_platform_verification']['verification_rate']}%)")
        print(f"單一平台: {report['cross_platform_verification']['single_platform_restaurants']} 家餐廳只在一個平台出現")

def main():
    """主程式"""
    print("費城 BYOB 餐廳爬蟲 - 多平台整合版")
    print("=" * 60)
    print(f"使用平台: {', '.join([k for k, v in CRAWLERS.items() if v])}")
    print(f"輸出格式: {', '.join(OUTPUT_FORMATS)}")
    print(f"信心度閾值: {CONFIDENCE_THRESHOLD}")
    print("=" * 60)
    
    # 建立主控程式實例
    main_crawler = MainBYOBCrawler()
    
    # 執行各平台爬蟲
    main_crawler.run_yelp_crawler()
    main_crawler.run_google_places_crawler()
    main_crawler.run_tripadvisor_crawler()
    
    # 合併結果
    main_crawler.merge_results()
    
    # 排序和評級
    main_crawler.rank_results()
    
    # 儲存結果
    main_crawler.save_results()
    
    # 生成報告
    main_crawler.generate_report()
    
    print("\n" + "=" * 60)
    print("所有爬蟲執行完成！")
    print("=" * 60)

if __name__ == "__main__":
    main()
