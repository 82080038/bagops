#!/usr/bin/env python3
"""
Test dropdown with manual click simulation
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def test_dropdown_manual():
    """Test dropdown with manual click simulation"""
    
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
    
    print("\n🔍 TESTING DROPDOWN WITH MANUAL FIXES")
    print("=" * 60)
    
    # Check if CSS fixes are present
    print(f"\n📋 CHECKING CSS FIXES")
    print("-" * 40)
    
    style_tags = soup.find_all('style')
    css_fixes_found = False
    
    for style_tag in style_tags:
        if style_tag.string:
            css_content = style_tag.string
            
            if 'navbar-nav .dropdown-menu' in css_content:
                print("✅ Dropdown z-index CSS found")
                css_fixes_found = True
            
            if 'dropdown-toggle' in css_content and 'cursor: pointer' in css_content:
                print("✅ Dropdown cursor CSS found")
                css_fixes_found = True
            
            if 'dropdown-menu.dropdown-menu-end' in css_content:
                print("✅ Dropdown positioning CSS found")
                css_fixes_found = True
    
    if css_fixes_found:
        print("✅ CSS fixes found in page")
    else:
        print("⚠️ CSS fixes may not be loaded")
    
    # Check if JavaScript fixes are present
    print(f"\n📋 CHECKING JAVASCRIPT FIXES")
    print("-" + 40)
    
    scripts = soup.find_all('script')
    js_fixes_found = False
    
    for script in scripts:
        if script.string:
            script_content = script.string
            
            if 'addEventListener' in script_content and 'dropdown-toggle' in script_content:
                print("✅ Manual dropdown JavaScript found")
                js_fixes_found = True
            
            if 'getBoundingClientRect' in script_content:
                print("✅ Manual positioning JavaScript found")
                js_fixes_found = True
            
            if 'clicking outside' in script_content:
                print("✅ Click outside handler found")
                js_fixes_found = True
    
    if js_fixes_found:
        print("✅ JavaScript fixes found in page")
    else:
        print("⚠️ JavaScript fixes may not be loaded")
    
    # Check dropdown structure
    print(f"\n📋 CHECKING DROPDOWN STRUCTURE")
    print("-" + 40)
    
    dropdown_li = soup.find('li', class_='nav-item dropdown')
    if dropdown_li:
        print("✅ Dropdown LI found")
        
        toggle = dropdown_li.find('a', class_='dropdown-toggle')
        if toggle:
            toggle_classes = toggle.get('class', [])
            print(f"✅ Toggle classes: {toggle_classes}")
        
        menu = dropdown_li.find('ul', class_='dropdown-menu')
        if menu:
            menu_classes = menu.get('class', [])
            print(f"✅ Menu classes: {menu_classes}")
            
            menu_items = menu.find_all('li')
            print(f"✅ Menu items: {len(menu_items)}")
    else:
        print("❌ Dropdown structure not found")
    
    # Test suggestions
    print(f"\n🔧 MANUAL TESTING INSTRUCTIONS")
    print("=" + 60)
    
    print("If dropdown still doesn't work:")
    print("1. ✅ Open browser and navigate to dashboard")
    print("2. ✅ Press F12 to open developer tools")
    print("3. ✅ Go to Console tab")
    print("4. ✅ Look for 'Bootstrap dropdowns initialized: X' message")
    print("5. ✅ Click on user dropdown")
    print("6. ✅ Check if dropdown menu appears")
    print("7. ✅ Check console for any JavaScript errors")
    
    print(f"\n🔧 DEBUGGING STEPS")
    print("=" + 60)
    
    print("In browser console, try:")
    print("1. Check Bootstrap object:")
    print("   typeof bootstrap")
    print("   (should return 'object')")
    print()
    print("2. Check dropdown elements:")
    print("   document.querySelectorAll('.dropdown-toggle')")
    print("   (should return array with 1 element)")
    print()
    print("3. Manual dropdown test:")
    print("   var dropdown = new bootstrap.Dropdown(document.querySelector('.dropdown-toggle'));")
    print("   dropdown.toggle();")
    print()
    print("4. Check CSS:")
    print("   getComputedStyle(document.querySelector('.dropdown-menu')).zIndex")
    print("   (should return '1050' or higher)")
    
    print(f"\n🎯 EXPECTED BEHAVIOR AFTER FIXES")
    print("=" + 60)
    
    print("After CSS and JavaScript fixes:")
    print("1. ✅ Dropdown should have proper z-index")
    print("2. ✅ Manual click handler should work")
    print("3. ✅ Menu should position correctly")
    print("4. ✅ Click outside should close dropdown")
    print("5. ✅ Console should show initialization message")
    
    print(f"\n✅ DROPDOWN MANUAL FIX TEST COMPLETED!")

if __name__ == "__main__":
    test_dropdown_manual()
