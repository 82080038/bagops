#!/usr/bin/env python3
"""
Script untuk analisis detail render halaman BAGOPS
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
import json
from urllib.parse import urljoin
from bs4 import BeautifulSoup

def analyze_page_details():
    """Analisis detail setiap halaman"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Pages to analyze
    pages = [
        ('dashboard', 'Dashboard'),
        ('personel', 'Personel'),
        ('jabatan_management', 'Jabatan Management'),
        ('settings', 'Settings'),
        ('reports', 'Reports'),
        ('profile', 'Profile'),
        ('help', 'Help'),
        ('assignments', 'Assignments'),
        ('operations', 'Operations'),
    ]
    
    print("\n📊 DETAILED PAGE ANALYSIS")
    print("=" * 60)
    
    analysis_results = []
    
    for page_key, page_title in pages:
        print(f"\n🔍 Analyzing: {page_key}")
        
        try:
            start_time = time.time()
            page_url = f"{base_url}/simple_root_system.php?page={page_key}"
            response = session.get(page_url, timeout=10)
            
            response_time = time.time() - start_time
            
            # Parse HTML
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Extract details
            analysis = {
                'page_key': page_key,
                'title': page_title,
                'http_status': response.status_code,
                'response_time': round(response_time, 3),
                'content_length': len(response.content),
                'page_title_tag': soup.title.string if soup.title else 'No title',
                'has_header': bool(soup.find('header')),
                'has_main': bool(soup.find('main')),
                'has_breadcrumb': bool(soup.find('nav', class_='breadcrumb-container')),
                'has_page_header': bool(soup.find('div', class_='page-header')),
                'has_page_content': bool(soup.find('div', class_='page-content')),
                'has_footer': bool(soup.find('footer')),
                'has_forms': len(soup.find_all('form')),
                'has_tables': len(soup.find_all('table')),
                'has_cards': len(soup.find_all('div', class_='card')),
                'has_alerts': len(soup.find_all('div', class_=lambda x: x and 'alert' in x)),
                'has_errors': bool(soup.find('div', class_='alert-danger')),
                'error_messages': [],
                'navigation_items': len(soup.find_all('li', class_='nav-item')),
                'buttons': len(soup.find_all('button')),
                'links': len(soup.find_all('a')),
            }
            
            # Extract error messages
            error_alerts = soup.find_all('div', class_='alert-danger')
            for alert in error_alerts:
                error_text = alert.get_text(strip=True)
                if error_text:
                    analysis['error_messages'].append(error_text)
            
            # Extract page title from header
            page_title_elem = soup.find('h1', class_='page-title')
            if page_title_elem:
                analysis['displayed_title'] = page_title_elem.get_text(strip=True)
            else:
                analysis['displayed_title'] = 'No page title found'
            
            # Extract breadcrumbs
            breadcrumbs = soup.find_all('li', class_='breadcrumb-item')
            analysis['breadcrumb_count'] = len(breadcrumbs)
            analysis['breadcrumbs'] = [b.get_text(strip=True) for b in breadcrumbs]
            
            # Determine status
            if response.status_code == 200:
                if analysis['has_page_content'] and not analysis['has_errors']:
                    analysis['status'] = 'SUCCESS'
                elif analysis['has_errors']:
                    analysis['status'] = 'ERROR'
                else:
                    analysis['status'] = 'NO_CONTENT'
            elif response.status_code == 403:
                analysis['status'] = 'FORBIDDEN'
            elif response.status_code == 404:
                analysis['status'] = 'NOT_FOUND'
            else:
                analysis['status'] = 'HTTP_ERROR'
            
            analysis_results.append(analysis)
            
            # Print summary
            status_icon = {
                'SUCCESS': '✅',
                'FORBIDDEN': '🚫',
                'NOT_FOUND': '❓',
                'ERROR': '❌',
                'NO_CONTENT': '⚠️',
                'HTTP_ERROR': '❌'
            }.get(analysis['status'], '❓')
            
            print(f"   {status_icon} Status: {analysis['status']}")
            print(f"   📊 HTTP: {analysis['http_status']} | Time: {analysis['response_time']}s | Size: {analysis['content_length']} bytes")
            print(f"   📋 Title: {analysis['displayed_title']}")
            print(f"   🧩 Components: Header={analysis['has_header']} | Main={analysis['has_main']} | Content={analysis['has_page_content']}")
            print(f"   📝 Elements: Forms={analysis['has_forms']} | Tables={analysis['has_tables']} | Cards={analysis['has_cards']}")
            
            if analysis['error_messages']:
                print(f"   ❌ Errors: {len(analysis['error_messages'])}")
                for error in analysis['error_messages'][:2]:
                    print(f"      - {error[:80]}...")
            
        except Exception as e:
            print(f"   ❌ Error analyzing {page_key}: {str(e)}")
            analysis_results.append({
                'page_key': page_key,
                'title': page_title,
                'status': 'ANALYSIS_ERROR',
                'error': str(e)
            })
    
    # Generate comprehensive report
    print(f"\n📊 COMPREHENSIVE ANALYSIS REPORT")
    print("=" * 60)
    
    total_pages = len(analysis_results)
    success_pages = [r for r in analysis_results if r['status'] == 'SUCCESS']
    error_pages = [r for r in analysis_results if r['status'] in ['ERROR', 'FORBIDDEN', 'NOT_FOUND', 'HTTP_ERROR', 'ANALYSIS_ERROR']]
    
    print(f"📈 SUMMARY:")
    print(f"   Total Pages: {total_pages}")
    print(f"   ✅ Successful: {len(success_pages)} ({len(success_pages)/total_pages*100:.1f}%)")
    print(f"   ❌ Failed: {len(error_pages)} ({len(error_pages)/total_pages*100:.1f}%)")
    
    if success_pages:
        avg_response_time = sum(r['response_time'] for r in success_pages) / len(success_pages)
        avg_content_size = sum(r['content_length'] for r in success_pages) / len(success_pages)
        
        print(f"   ⏱️ Avg Response Time: {avg_response_time:.3f}s")
        print(f"   📏 Avg Content Size: {avg_content_size:.0f} bytes")
    
    print(f"\n📋 SUCCESSFUL PAGES:")
    for page in success_pages:
        print(f"   ✅ {page['page_key']:15} - {page['displayed_title']}")
    
    if error_pages:
        print(f"\n❌ FAILED PAGES:")
        for page in error_pages:
            print(f"   ❌ {page['page_key']:15} - {page['status']}")
            if 'error' in page:
                print(f"      Error: {page['error'][:60]}...")
            elif 'error_messages' in page and page['error_messages']:
                print(f"      Error: {page['error_messages'][0][:60]}...")
    
    # Component analysis
    print(f"\n🧩 COMPONENT ANALYSIS:")
    components = {
        'has_header': 'Header',
        'has_main': 'Main Content',
        'has_breadcrumb': 'Breadcrumb',
        'has_page_header': 'Page Header',
        'has_page_content': 'Page Content',
        'has_footer': 'Footer'
    }
    
    for comp_key, comp_name in components.items():
        count = sum(1 for r in analysis_results if r.get(comp_key, False))
        print(f"   📦 {comp_name:15} - {count}/{total_pages} ({count/total_pages*100:.1f}%)")
    
    # Interactive elements analysis
    print(f"\n🎮 INTERACTIVE ELEMENTS:")
    interactive = {
        'has_forms': 'Forms',
        'has_tables': 'Tables', 
        'has_cards': 'Cards',
        'has_alerts': 'Alerts'
    }
    
    for int_key, int_name in interactive.items():
        total_elements = sum(r.get(int_key, 0) for r in analysis_results)
        pages_with_elements = sum(1 for r in analysis_results if r.get(int_key, 0) > 0)
        print(f"   🎯 {int_name:12} - {total_elements} total in {pages_with_elements} pages")
    
    # Save detailed report
    try:
        report_data = {
            'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
            'base_url': base_url,
            'role': 'super_admin',
            'summary': {
                'total_pages': total_pages,
                'success_count': len(success_pages),
                'error_count': len(error_pages),
                'success_rate': len(success_pages)/total_pages*100
            },
            'results': analysis_results
        }
        
        with open('/var/www/html/bagops/test_results/detailed_analysis.json', 'w') as f:
            json.dump(report_data, f, indent=2, ensure_ascii=False)
        
        print(f"\n📁 Detailed report saved to: test_results/detailed_analysis.json")
        
    except Exception as e:
        print(f"❌ Error saving report: {str(e)}")
    
    print(f"\n✅ DETAILED ANALYSIS COMPLETED!")

if __name__ == "__main__":
    analyze_page_details()
