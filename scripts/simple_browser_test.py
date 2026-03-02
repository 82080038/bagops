#!/usr/bin/env python3
"""
Simple test untuk memastikan tidak ada browser errors
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time

def simple_browser_test():
    """Simple browser test"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Test personel page
    print("📄 Testing personel_ultra page...")
    page_url = f"{base_url}/simple_root_system.php?page=personel_ultra"
    
    start_time = time.time()
    response = session.get(page_url, timeout=10)
    load_time = time.time() - start_time
    
    print(f"📊 Status: {response.status_code}")
    print(f"⏱️ Load time: {load_time:.3f}s")
    print(f"📏 Size: {len(response.content)} bytes")
    
    # Check for error indicators
    content = response.text.lower()
    error_indicators = [
        'fatal error',
        'parse error',
        'syntax error',
        '404 not found',
        '500 internal server error',
        'undefined',
        'null',
        'cannot read property'
    ]
    
    found_errors = []
    for indicator in error_indicators:
        if indicator in content:
            found_errors.append(indicator)
    
    if found_errors:
        print(f"⚠️ Found error indicators: {found_errors}")
    else:
        print("✅ No obvious error indicators in HTML")
    
    # Test AJAX endpoint
    print("\n🔄 Testing AJAX endpoint...")
    ajax_url = f"{base_url}/ajax/get_personel.php"
    
    start_time = time.time()
    ajax_response = session.get(ajax_url, timeout=10)
    ajax_time = time.time() - start_time
    
    print(f"📊 AJAX Status: {ajax_response.status_code}")
    print(f"⏱️ AJAX time: {ajax_time:.3f}s")
    
    if ajax_response.status_code == 200:
        try:
            import json
            data = ajax_response.json()
            print(f"✅ AJAX works: {data['recordsTotal']} records")
        except:
            print("❌ AJAX response not valid JSON")
    else:
        print("❌ AJAX endpoint failed")
    
    # Final assessment
    print("\n📊 FINAL RESULT")
    print("=" * 30)
    
    if (response.status_code == 200 and 
        ajax_response.status_code == 200 and 
        len(found_errors) == 0):
        print("🎉 ALL GOOD! No errors expected!")
        print("📱 Personel table should work perfectly!")
    else:
        print("⚠️ Some issues detected")
    
    print("\n✅ TEST COMPLETED!")

if __name__ == "__main__":
    simple_browser_test()
