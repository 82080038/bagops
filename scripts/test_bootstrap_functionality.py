#!/usr/bin/env python3
"""
Test Bootstrap functionality including dropdowns
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import re
from bs4 import BeautifulSoup

def test_bootstrap_functionality():
    """Test Bootstrap functionality including dropdowns"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Get dashboard page
    print("📄 Mengambil halaman dashboard...")
    page_url = f"{base_url}/simple_root_system.php?page=dashboard"
    response = session.get(page_url, timeout=10)
    
    print(f"📊 HTTP Status: {response.status_code}")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 BOOTSTRAP FUNCTIONALITY TEST")
    print("=" * 50)
    
    # Test 1: Check Bootstrap CSS
    print("📋 TEST 1: Bootstrap CSS")
    print("-" * 30)
    
    bootstrap_css_links = soup.find_all('link', href=lambda x: x and 'bootstrap' in x.lower())
    if bootstrap_css_links:
        print("✅ Bootstrap CSS found in page")
        for link in bootstrap_css_links:
            css_url = link.get('href')
            print(f"   {css_url}")
            
            # Test if CSS is accessible
            if css_url.startswith('http'):
                css_response = session.get(css_url, timeout=5)
                if css_response.status_code == 200:
                    print(f"   ✅ CSS accessible ({len(css_response.content)} bytes)")
                    
                    # Check for dropdown styles
                    if 'dropdown' in css_response.text.lower():
                        print("   ✅ Contains dropdown styles")
                    else:
                        print("   ⚠️ May not contain dropdown styles")
                else:
                    print(f"   ❌ CSS not accessible ({css_response.status_code})")
            else:
                full_css_url = f"{base_url}/{css_url}"
                css_response = session.get(full_css_url, timeout=5)
                if css_response.status_code == 200:
                    print(f"   ✅ CSS accessible ({len(css_response.content)} bytes)")
                else:
                    print(f"   ❌ CSS not accessible ({css_response.status_code})")
    else:
        print("❌ Bootstrap CSS not found")
    
    # Test 2: Check Bootstrap JS
    print(f"\n📋 TEST 2: Bootstrap JS")
    print("-" * 30)
    
    bootstrap_js_scripts = soup.find_all('script', src=lambda x: x and 'bootstrap' in x.lower())
    if bootstrap_js_scripts:
        print("✅ Bootstrap JS found in page")
        for script in bootstrap_js_scripts:
            js_url = script.get('src')
            print(f"   {js_url}")
            
            # Test if JS is accessible
            if js_url.startswith('http'):
                js_response = session.get(js_url, timeout=5)
                if js_response.status_code == 200:
                    print(f"   ✅ JS accessible ({len(js_response.content)} bytes)")
                    
                    # Check for dropdown functionality
                    if 'dropdown' in js_response.text.lower():
                        print("   ✅ Contains dropdown functionality")
                    else:
                        print("   ⚠️ May not contain dropdown functionality")
                else:
                    print(f"   ❌ JS not accessible ({js_response.status_code})")
            else:
                full_js_url = f"{base_url}/{js_url}"
                js_response = session.get(full_js_url, timeout=5)
                if js_response.status_code == 200:
                    print(f"   ✅ JS accessible ({len(js_response.content)} bytes)")
                else:
                    print(f"   ❌ JS not accessible ({js_response.status_code})")
    else:
        print("❌ Bootstrap JS not found")
    
    # Test 3: Check jQuery
    print(f"\n📋 TEST 3: jQuery")
    print("-" * 30)
    
    jquery_scripts = soup.find_all('script', src=lambda x: x and 'jquery' in x.lower())
    if jquery_scripts:
        print("✅ jQuery found in page")
        for script in jquery_scripts:
            js_url = script.get('src')
            print(f"   {js_url}")
            
            if not js_url.startswith('http'):
                full_js_url = f"{base_url}/{js_url}"
                js_response = session.get(full_js_url, timeout=5)
                if js_response.status_code == 200:
                    print(f"   ✅ jQuery accessible ({len(js_response.content)} bytes)")
                else:
                    print(f"   ❌ jQuery not accessible ({js_response.status_code})")
    else:
        print("❌ jQuery not found")
    
    # Test 4: Check dropdown HTML structure
    print(f"\n📋 TEST 4: Dropdown HTML Structure")
    print("-" * 30)
    
    dropdown_toggles = soup.find_all('a', {'data-bs-toggle': 'dropdown'})
    print(f"📋 Dropdown toggles: {len(dropdown_toggles)}")
    
    for i, toggle in enumerate(dropdown_toggles, 1):
        print(f"   Toggle {i}:")
        print(f"     Classes: {toggle.get('class', [])}")
        print(f"     data-bs-toggle: {toggle.get('data-bs-toggle')}")
        print(f"     href: {toggle.get('href')}")
        print(f"     role: {toggle.get('role')}")
        print(f"     aria-expanded: {toggle.get('aria-expanded')}")
        
        # Check if dropdown menu exists
        parent_li = toggle.find_parent('li')
        if parent_li:
            dropdown_menu = parent_li.find('ul', class_='dropdown-menu')
            if dropdown_menu:
                menu_items = dropdown_menu.find_all('li')
                print(f"     ✅ Menu found with {len(menu_items)} items")
            else:
                print(f"     ❌ No dropdown menu found")
    
    # Test 5: Check for JavaScript initialization
    print(f"\n📋 TEST 5: JavaScript Initialization")
    print("-" * 30)
    
    scripts = soup.find_all('script')
    bootstrap_init_found = False
    
    for script in scripts:
        if script.string:
            script_content = script.string
            if 'dropdown' in script_content.lower():
                print("✅ Found dropdown-related JavaScript")
                bootstrap_init_found = True
                
                # Look for Bootstrap initialization
                if 'bootstrap' in script_content.lower():
                    print("   ✅ Bootstrap initialization found")
                
                # Look for manual dropdown initialization
                if 'dropdown' in script_content and ('toggle' in script_content or 'Dropdown' in script_content):
                    print("   ✅ Manual dropdown initialization found")
    
    if not bootstrap_init_found:
        print("⚠️ No dropdown JavaScript initialization found")
        print("   Bootstrap should auto-initialize, but this might be an issue")
    
    # Test 6: Check for console errors indicators
    print(f"\n📋 TEST 6: Error Indicators")
    print("-" * 30)
    
    html_content = response.text
    
    error_indicators = [
        'bootstrap is not defined',
        'dropdown is not a function',
        'undefined',
        'null',
        'error',
        'failed'
    ]
    
    found_errors = []
    for indicator in error_indicators:
        if indicator.lower() in html_content.lower():
            found_errors.append(indicator)
    
    if found_errors:
        print(f"⚠️ Found potential error indicators: {found_errors}")
    else:
        print("✅ No obvious error indicators in HTML")
    
    # Test 7: Check HTML validity
    print(f"\n📋 TEST 7: HTML Validity")
    print("-" * 30)
    
    # Count tags
    open_divs = html_content.count('<div')
    close_divs = html_content.count('</div>')
    open_lis = html_content.count('<li')
    close_lis = html_content.count('</li')
    
    print(f"DIV tags: {open_divs} open, {close_divs} close")
    print(f"LI tags: {open_lis} open, {close_lis} close")
    
    if open_divs != close_divs:
        print("⚠️ Unclosed DIV tags - this could cause issues")
    if open_lis != close_lis:
        print("⚠️ Unclosed LI tags - this could cause Bootstrap issues")
    
    # Recommendations
    print(f"\n🔧 RECOMMENDATIONS")
    print("=" * 50)
    
    if not bootstrap_css_links:
        print("❌ Bootstrap CSS missing - Add Bootstrap CSS link")
    
    if not bootstrap_js_scripts:
        print("❌ Bootstrap JS missing - Add Bootstrap JS script")
    
    if not jquery_scripts:
        print("❌ jQuery missing - Add jQuery script (required for Bootstrap)")
    
    if open_lis != close_lis:
        print("❌ HTML structure issue - Fix unclosed LI tags")
        print("   This is likely causing the dropdown problem")
    
    if bootstrap_css_links and bootstrap_js_scripts and jquery_scripts and open_lis == close_lis:
        print("✅ All required components present")
        print("🔧 Check browser console for JavaScript errors")
        print("🔧 Ensure no CSS conflicts")
        print("🔧 Test in different browsers")
    
    print(f"\n✅ BOOTSTRAP FUNCTIONALITY TEST COMPLETED!")

if __name__ == "__main__":
    test_bootstrap_functionality()
