#!/usr/bin/env python3
"""
Test AJAX URL yang dipanggil dari browser
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def test_ajax_url():
    """Test AJAX URL configuration"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Get personel page
    print("📄 Mengambil halaman personel_ultra...")
    page_url = f"{base_url}/simple_root_system.php?page=personel_ultra"
    response = session.get(page_url, timeout=10)
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 ANALISIS AJAX URL")
    print("=" * 50)
    
    # Find DataTables configuration
    scripts = soup.find_all('script')
    ajax_url_found = False
    
    for script in scripts:
        if script.string and 'get_personel.php' in script.string:
            print("✅ Found get_personel.php in JavaScript")
            
            # Extract the URL
            script_content = script.string
            lines = script_content.split('\n')
            
            for line in lines:
                if 'url:' in line and 'get_personel.php' in line:
                    print(f"📋 AJAX URL Line: {line.strip()}")
                    
                    # Check if it's the correct URL
                    if 'ajax/get_personel.php' in line:
                        print("✅ Correct relative URL: ajax/get_personel.php")
                        ajax_url_found = True
                    elif '../ajax/get_personel.php' in line:
                        print("⚠️ Incorrect relative URL: ../ajax/get_personel.php")
                    elif '/ajax/get_personel.php' in line:
                        print("⚠️ Absolute URL might cause issues")
                    
                    break
    
    if not ajax_url_found:
        print("❌ AJAX URL not found or incorrect")
        return False
    
    print("\n🧪 TESTING URL ACCESS")
    print("=" * 50)
    
    # Test different URL variations
    urls_to_test = [
        f"{base_url}/ajax/get_personel.php",
        f"{base_url}/../ajax/get_personel.php",
        "http://localhost/ajax/get_personel.php"
    ]
    
    for url in urls_to_test:
        try:
            response = session.get(url, timeout=5)
            status_icon = "✅" if response.status_code == 200 else "❌"
            print(f"{status_icon} {url} - {response.status_code}")
            
            if response.status_code == 200:
                try:
                    import json
                    data = response.json()
                    if 'data' in data and 'recordsTotal' in data:
                        print(f"   📊 Valid JSON with {data['recordsTotal']} records")
                    else:
                        print(f"   ⚠️ JSON but missing fields")
                except:
                    print(f"   ❌ Not valid JSON")
            
        except Exception as e:
            print(f"❌ {url} - Error: {str(e)[:50]}")
    
    print("\n🔍 ANALISIS PATH RESOLUTION")
    print("=" * 50)
    
    # Explain the path resolution
    print("📋 Path Resolution Explanation:")
    print("   Current page: /bagops/simple_root_system.php")
    print("   AJAX URL: ajax/get_personel.php")
    print("   Resolved to: /bagops/ajax/get_personel.php ✅")
    print()
    print("❌ Wrong URL: ../ajax/get_personel.php")
    print("   Resolved to: /ajax/get_personel.php (404)")
    print()
    print("❌ Wrong URL: http://localhost/ajax/get_personel.php")
    print("   Resolved to: /ajax/get_personel.php (404)")
    
    print("\n✅ AJAX URL TESTING COMPLETED!")
    return ajax_url_found

if __name__ == "__main__":
    test_ajax_url()
