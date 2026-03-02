#!/usr/bin/env python3
"""
Debug dropdown navigation issues
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def debug_dropdown():
    """Debug dropdown navigation issues"""
    
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
    print(f"📏 Content Length: {len(response.content)} bytes")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 ANALISIS DROPDOWN NAVIGATION")
    print("=" * 50)
    
    # Find dropdown elements
    dropdown_toggles = soup.find_all('a', class_='dropdown-toggle')
    dropdown_menus = soup.find_all('div', class_='dropdown-menu')
    
    print(f"📋 Dropdown toggles found: {len(dropdown_toggles)}")
    print(f"📋 Dropdown menus found: {len(dropdown_menus)}")
    
    # Analyze each dropdown toggle
    for i, toggle in enumerate(dropdown_toggles, 1):
        print(f"\n🔗 Dropdown Toggle {i}:")
        print(f"   Tag: {toggle.name}")
        print(f"   Classes: {toggle.get('class', [])}")
        print(f"   Text: {toggle.get_text(strip=True)}")
        print(f"   Attributes: {dict(toggle.attrs)}")
        
        # Check for required Bootstrap attributes
        has_data_toggle = toggle.get('data-bs-toggle') == 'dropdown'
        has_href = toggle.get('href') == '#'
        has_aria_expanded = 'aria-expanded' in toggle.attrs
        has_role = toggle.get('role') == 'button'
        
        print(f"   ✅ data-bs-toggle='dropdown': {has_data_toggle}")
        print(f"   ✅ href='#': {has_href}")
        print(f"   ✅ aria-expanded: {has_aria_expanded}")
        print(f"   ✅ role='button': {has_role}")
    
    # Analyze dropdown menus
    for i, menu in enumerate(dropdown_menus, 1):
        print(f"\n📋 Dropdown Menu {i}:")
        print(f"   Tag: {menu.name}")
        print(f"   Classes: {menu.get('class', [])}")
        
        # Count menu items
        menu_items = menu.find_all('a', class_='dropdown-item')
        print(f"   📝 Menu items: {len(menu_items)}")
        
        for j, item in enumerate(menu_items[:3], 1):  # Show first 3
            print(f"      {j}. {item.get_text(strip=True)}")
    
    print(f"\n🔍 ANALISIS JAVASCRIPT ASSETS")
    print("=" * 50)
    
    # Check Bootstrap JS loading
    scripts = soup.find_all('script')
    bootstrap_js_found = False
    jquery_found = False
    
    for script in scripts:
        src = script.get('src', '')
        if 'bootstrap' in src.lower():
            bootstrap_js_found = True
            print(f"✅ Bootstrap JS found: {src}")
        elif 'jquery' in src.lower():
            jquery_found = True
            print(f"✅ jQuery found: {src}")
    
    if not bootstrap_js_found:
        print("❌ Bootstrap JS NOT found - This is likely the problem!")
    
    if not jquery_found:
        print("❌ jQuery NOT found - This could cause issues!")
    
    print(f"\n🔍 ANALISIS BOOTSTRAP VERSION")
    print("=" * 50)
    
    # Check Bootstrap version
    bootstrap_links = soup.find_all('link', href=lambda x: x and 'bootstrap' in x.lower())
    bootstrap_scripts = soup.find_all('script', src=lambda x: x and 'bootstrap' in x.lower())
    
    print(f"📋 Bootstrap CSS links: {len(bootstrap_links)}")
    for link in bootstrap_links:
        print(f"   {link.get('href')}")
    
    print(f"📋 Bootstrap JS scripts: {len(bootstrap_scripts)}")
    for script in bootstrap_scripts:
        print(f"   {script.get('src')}")
    
    print(f"\n🔍 ANALISIS DROPDOWN HTML STRUCTURE")
    print("=" * 50)
    
    # Find the complete dropdown structure
    nav_dropdowns = soup.find_all('li', class_='dropdown')
    
    for i, nav_dropdown in enumerate(nav_dropdowns, 1):
        print(f"\n📱 Navigation Dropdown {i}:")
        
        # Get the toggle
        toggle = nav_dropdown.find('a', class_='dropdown-toggle')
        if toggle:
            print(f"   Toggle: {toggle.get_text(strip=True)}")
            print(f"   Toggle HTML: {str(toggle)[:100]}...")
        
        # Get the menu
        menu = nav_dropdown.find('div', class_='dropdown-menu')
        if menu:
            items = menu.find_all('a', class_='dropdown-item')
            print(f"   Menu items: {len(items)}")
            for item in items:
                print(f"      - {item.get_text(strip())} -> {item.get('href')}")
    
    print(f"\n🔍 TESTING BOOTSTRAP ASSETS")
    print("=" * 50)
    
    # Test Bootstrap CSS
    try:
        css_response = session.get(f"{base_url}/assets/css/bootstrap.min.css", timeout=5)
        if css_response.status_code == 200:
            print("✅ Bootstrap CSS accessible")
            # Check if it contains dropdown styles
            css_content = css_response.text
            if 'dropdown' in css_content.lower():
                print("✅ Bootstrap CSS contains dropdown styles")
            else:
                print("⚠️ Bootstrap CSS may not contain dropdown styles")
        else:
            print(f"❌ Bootstrap CSS not accessible: {css_response.status_code}")
    except Exception as e:
        print(f"❌ Error testing Bootstrap CSS: {str(e)}")
    
    # Test Bootstrap JS
    try:
        js_response = session.get(f"{base_url}/assets/js/bootstrap.bundle.min.js", timeout=5)
        if js_response.status_code == 200:
            print("✅ Bootstrap JS accessible")
            # Check if it contains dropdown functionality
            js_content = js_response.text
            if 'dropdown' in js_content.lower():
                print("✅ Bootstrap JS contains dropdown functionality")
            else:
                print("⚠️ Bootstrap JS may not contain dropdown functionality")
        else:
            print(f"❌ Bootstrap JS not accessible: {js_response.status_code}")
    except Exception as e:
        print(f"❌ Error testing Bootstrap JS: {str(e)}")
    
    print(f"\n🔧 RECOMMENDATIONS")
    print("=" * 50)
    
    if not bootstrap_js_found:
        print("❌ ISSUE: Bootstrap JS not loaded")
        print("🔧 SOLUTION: Ensure Bootstrap JS is loaded in layout")
        print("   Check: layouts/simple_layout.php")
    
    if not jquery_found:
        print("❌ ISSUE: jQuery not loaded")
        print("🔧 SOLUTION: Ensure jQuery is loaded before Bootstrap")
        print("   Check: layouts/simple_layout.php")
    
    if bootstrap_js_found and jquery_found:
        print("✅ Bootstrap JS and jQuery found")
        print("🔧 Check if dropdown attributes are correct:")
        print("   - data-bs-toggle='dropdown'")
        print("   - href='#'")
        print("   - aria-expanded='false'")
        print("   - role='button'")
    
    print(f"\n✅ DROPDOWN DEBUG COMPLETED!")

if __name__ == "__main__":
    debug_dropdown()
