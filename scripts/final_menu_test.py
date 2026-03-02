#!/usr/bin/env python3
"""
Final Complete Menu Test for BAGOPS Super Admin
"""

import requests
from urllib.parse import urljoin
from bs4 import BeautifulSoup

def final_menu_test():
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    print("🎯 FINAL MENU TEST - SUPER ADMIN ACCESS")
    print("=" * 60)
    
    # Login
    print("🔐 Login sebagai Super Admin...")
    session.post(f"{base_url}/login.php", data={'username': 'super_admin', 'password': 'admin123'})
    
    # Get dashboard to extract menu
    response = session.get(f"{base_url}/simple_root_system.php?page=dashboard")
    
    if response.status_code != 200:
        print("❌ Gagal load dashboard")
        return
    
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # Extract menu from navbar
    menu_items = []
    main_nav = soup.find('ul', class_='navbar-nav')
    
    if main_nav:
        links = main_nav.find_all('a', href=True)
        for link in links:
            href = link.get('href', '')
            text = link.get_text(strip=True)
            if text and href and 'simple_root_system.php' in href:
                # Extract page name from URL
                if 'page=' in href:
                    page = href.split('page=')[1].split('&')[0]
                    menu_items.append({'name': text, 'page': page, 'url': href})
    
    print(f"\n📋 MENU YANG DITEMUKAN ({len(menu_items)} items):")
    for i, item in enumerate(menu_items, 1):
        print(f"  {i}. {item['name']} -> {item['page']}")
    
    print(f"\n📄 TESTING SEMUA HALAMAN...")
    print("=" * 40)
    
    successful = 0
    failed = 0
    detailed_results = []
    
    for item in menu_items:
        try:
            response = session.get(f"{base_url}/{item['url']}")
            
            result = {
                'name': item['name'],
                'page': item['page'],
                'status': response.status_code,
                'size': len(response.text),
                'success': response.status_code == 200 and len(response.text) > 1000
            }
            
            if result['success']:
                # Analyze content
                soup = BeautifulSoup(response.text, 'html.parser')
                
                result['has_title'] = soup.find('title') is not None
                result['has_content'] = soup.find('main') is not None or soup.find('div', class_='container') is not None
                result['has_cards'] = len(soup.find_all('div', class_='card')) > 0
                result['has_tables'] = len(soup.find_all('table')) > 0
                result['has_forms'] = len(soup.find_all('form')) > 0
                result['has_buttons'] = len(soup.find_all('button')) > 0
                
                # Check for actual errors (not CSS classes)
                page_text = soup.get_text()
                result['has_php_error'] = 'fatal error:' in page_text.lower() or 'parse error:' in page_text.lower()
                result['has_db_error'] = 'sqlstate' in page_text.lower() and 'error' in page_text.lower()
                
                if result['has_php_error'] or result['has_db_error']:
                    result['success'] = False
                    failed += 1
                    print(f"❌ {item['name']} - ERROR DETECTED")
                else:
                    successful += 1
                    print(f"✅ {item['name']} - OK ({result['size']} bytes)")
            else:
                failed += 1
                print(f"❌ {item['name']} - FAILED ({result['status']})")
            
            detailed_results.append(result)
            
        except Exception as e:
            failed += 1
            print(f"❌ {item['name']} - ERROR: {e}")
            detailed_results.append({
                'name': item['name'],
                'page': item['page'],
                'success': False,
                'error': str(e)
            })
    
    print(f"\n📊 SUMMARY")
    print("=" * 30)
    print(f"✅ Successful: {successful}")
    print(f"❌ Failed: {failed}")
    print(f"📈 Success Rate: {(successful/(successful+failed)*100):.1f}%" if (successful+failed) > 0 else "N/A")
    
    # Detailed analysis
    print(f"\n🔍 DETAILED ANALYSIS")
    print("=" * 30)
    
    for result in detailed_results:
        if result['success']:
            print(f"\n📄 {result['name']}:")
            print(f"   Status: {result['status']} | Size: {result['size']} bytes")
            print(f"   Cards: {result.get('has_cards', False)} | Tables: {result.get('has_tables', False)}")
            print(f"   Forms: {result.get('has_forms', False)} | Buttons: {result.get('has_buttons', False)}")
        else:
            print(f"\n❌ {result['name']}: {result.get('status', 'ERROR')}")
            if 'error' in result:
                print(f"   Error: {result['error']}")
    
    # Test specific super admin features
    print(f"\n🎯 SUPER ADMIN FEATURES")
    print("=" * 30)
    
    admin_tests = [
        ('Settings Page', 'simple_root_system.php?page=settings'),
        ('Profile Page', 'simple_root_system.php?page=profile'),
        ('Help Page', 'simple_root_system.php?page=help'),
    ]
    
    for name, url in admin_tests:
        try:
            response = session.get(f"{base_url}/{url}")
            if response.status_code == 200:
                print(f"✅ {name}: Accessible")
            else:
                print(f"❌ {name}: Not accessible ({response.status_code})")
        except:
            print(f"❌ {name}: Error")
    
    # Test AJAX functionality
    print(f"\n🔄 AJAX FUNCTIONALITY")
    print("=" * 30)
    
    ajax_pages = ['dashboard', 'personel_ultra', 'operations', 'reports', 'settings']
    
    for page in ajax_pages:
        try:
            response = session.post(f"{base_url}/ajax/content.php", data={'page': page})
            
            if response.status_code == 200:
                content_type = response.headers.get('content-type', '')
                if 'application/json' in content_type:
                    try:
                        data = response.json()
                        if data.get('success'):
                            print(f"✅ AJAX {page}: Working")
                        else:
                            print(f"⚠️  AJAX {page}: {data.get('message', 'Failed')}")
                    except:
                        print(f"❌ AJAX {page}: Invalid JSON")
                else:
                    print(f"⚠️  AJAX {page}: Not JSON ({content_type})")
            else:
                print(f"❌ AJAX {page}: HTTP {response.status_code}")
                
        except Exception as e:
            print(f"❌ AJAX {page}: {e}")
    
    return successful > failed

if __name__ == "__main__":
    success = final_menu_test()
    
    print(f"\n{'🎉 ALL TESTS PASSED' if success else '⚠️  SOME TESTS FAILED'}")
    print("🏁 Test completed!")
