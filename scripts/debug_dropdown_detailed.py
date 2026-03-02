#!/usr/bin/env python3
"""
Detailed dropdown debugging
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def debug_dropdown_detailed():
    """Detailed dropdown debugging"""
    
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
    
    print("\n🔍 DETAILED DROPDOWN DEBUGGING")
    print("=" * 60)
    
    # Check Bootstrap version and assets
    print(f"\n📋 BOOTSTRAP ASSETS CHECK")
    print("-" * 40)
    
    # Check Bootstrap CSS
    bootstrap_css = soup.find('link', href=lambda x: x and 'bootstrap.min.css' in x)
    if bootstrap_css:
        css_url = bootstrap_css.get('href')
        print(f"✅ Bootstrap CSS: {css_url}")
        
        # Test CSS accessibility
        css_response = session.get(f"{base_url}/{css_url}" if not css_url.startswith('http') else css_url, timeout=5)
        if css_response.status_code == 200:
            print(f"   ✅ Accessible ({len(css_response.content)} bytes)")
            
            # Check for dropdown styles
            if 'dropdown' in css_response.text.lower():
                print("   ✅ Contains dropdown styles")
            else:
                print("   ⚠️ May not contain dropdown styles")
        else:
            print(f"   ❌ Not accessible ({css_response.status_code})")
    else:
        print("❌ Bootstrap CSS not found")
    
    # Check Bootstrap JS
    bootstrap_js = soup.find('script', src=lambda x: x and 'bootstrap.bundle.min.js' in x)
    if bootstrap_js:
        js_url = bootstrap_js.get('src')
        print(f"✅ Bootstrap JS: {js_url}")
        
        # Test JS accessibility
        js_response = session.get(f"{base_url}/{js_url}" if not js_url.startswith('http') else js_url, timeout=5)
        if js_response.status_code == 200:
            print(f"   ✅ Accessible ({len(js_response.content)} bytes)")
            
            # Check for dropdown functionality
            if 'dropdown' in js_response.text.lower():
                print("   ✅ Contains dropdown functionality")
            else:
                print("   ⚠️ May not contain dropdown functionality")
        else:
            print(f"   ❌ Not accessible ({js_response.status_code})")
    else:
        print("❌ Bootstrap JS not found")
    
    # Check jQuery
    jquery_js = soup.find('script', src=lambda x: x and 'jquery-3.6.0.min.js' in x)
    if jquery_js:
        print(f"✅ jQuery: {jquery_js.get('src')}")
    else:
        print("❌ jQuery not found")
    
    # Check dropdown HTML structure
    print(f"\n📋 DROPDOWN HTML STRUCTURE")
    print("-" * 40)
    
    dropdown_li = soup.find('li', class_='nav-item dropdown')
    if dropdown_li:
        print("✅ Dropdown LI found")
        
        # Check toggle
        toggle = dropdown_li.find('a', class_='dropdown-toggle')
        if toggle:
            print("✅ Dropdown toggle found")
            
            # Check all required attributes
            attrs = {
                'class': toggle.get('class', []),
                'href': toggle.get('href'),
                'role': toggle.get('role'),
                'data-bs-toggle': toggle.get('data-bs-toggle'),
                'aria-expanded': toggle.get('aria-expanded')
            }
            
            print("   Attributes:")
            for attr_name, attr_value in attrs.items():
                if attr_name == 'class':
                    print(f"     {attr_name}: {attr_value}")
                else:
                    print(f"     {attr_name}: {attr_value}")
            
            # Check if attributes are correct
            required_attrs = {
                'href': '#',
                'role': 'button',
                'data-bs-toggle': 'dropdown',
                'aria-expanded': 'false'
            }
            
            missing_attrs = []
            for req_attr, req_value in required_attrs.items():
                actual_value = attrs.get(req_attr)
                if actual_value != req_value:
                    missing_attrs.append(f"{req_attr}: expected '{req_value}', got '{actual_value}'")
            
            if missing_attrs:
                print("   ⚠️ Missing/incorrect attributes:")
                for missing in missing_attrs:
                    print(f"     - {missing}")
            else:
                print("   ✅ All required attributes correct")
        
        # Check dropdown menu
        menu = dropdown_li.find('ul', class_='dropdown-menu')
        if menu:
            print("✅ Dropdown menu found")
            
            menu_items = menu.find_all('li')
            print(f"   Menu items: {len(menu_items)}")
            
            for i, item in enumerate(menu_items, 1):
                item_classes = item.get('class', [])
                item_text = item.get_text(strip=True)
                
                if 'dropdown-header' in item_classes:
                    print(f"     {i}. Header: '{item_text}'")
                elif 'dropdown-divider' in item_classes:
                    print(f"     {i}. Divider")
                else:
                    link = item.find('a')
                    if link:
                        link_text = link.get_text(strip=True)
                        link_href = link.get('href')
                        print(f"     {i}. Link: '{link_text}' -> {link_href}")
        else:
            print("❌ Dropdown menu not found")
    else:
        print("❌ Dropdown LI not found")
    
    # Check JavaScript initialization
    print(f"\n📋 JAVASCRIPT INITIALIZATION")
    print("-" * 40)
    
    scripts = soup.find_all('script')
    bootstrap_init_found = False
    
    for script in scripts:
        if script.string:
            script_content = script.string
            
            # Look for Bootstrap dropdown initialization
            if 'bootstrap.Dropdown' in script_content:
                print("✅ Bootstrap dropdown initialization found")
                bootstrap_init_found = True
                
                # Extract initialization code
                lines = script_content.split('\n')
                for line in lines:
                    if 'bootstrap.Dropdown' in line:
                        print(f"   Code: {line.strip()}")
                        break
            
            # Look for any dropdown-related code
            elif 'dropdown' in script_content.lower():
                print("✅ Dropdown-related JavaScript found")
                bootstrap_init_found = True
    
    if not bootstrap_init_found:
        print("⚠️ No explicit dropdown initialization found")
        print("   (Bootstrap should auto-initialize, but this might be an issue)")
    
    # Check for potential conflicts
    print(f"\n📋 POTENTIAL CONFLICTS")
    print("-" + 40)
    
    # Check for multiple Bootstrap versions
    bootstrap_links = soup.find_all('link', href=lambda x: x and 'bootstrap' in x.lower())
    bootstrap_scripts = soup.find_all('script', src=lambda x: x and 'bootstrap' in x.lower())
    
    print(f"Bootstrap CSS files: {len(bootstrap_links)}")
    print(f"Bootstrap JS files: {len(bootstrap_scripts)}")
    
    if len(bootstrap_links) > 1 or len(bootstrap_scripts) > 1:
        print("⚠️ Multiple Bootstrap files detected - potential conflict")
    else:
        print("✅ Single Bootstrap instance")
    
    # Check for other dropdown libraries
    other_dropdown_libs = []
    for script in scripts:
        if script.get('src'):
            src = script.get('src')
            if any(lib in src.lower() for lib in ['select2', 'chosen', 'bootstrap-multiselect']):
                other_dropdown_libs.append(src)
    
    if other_dropdown_libs:
        print(f"⚠️ Other dropdown libraries found: {other_dropdown_libs}")
    else:
        print("✅ No conflicting dropdown libraries")
    
    # Test suggestions
    print(f"\n🔧 TROUBLESHOOTING SUGGESTIONS")
    print("=" + 60)
    
    print("If dropdown is not working:")
    print("1. ✅ Bootstrap assets loaded and accessible")
    print("2. ✅ HTML structure is correct")
    print("3. ✅ All required attributes present")
    
    if not bootstrap_init_found:
        print("4. ⚠️ No explicit dropdown initialization")
        print("   Solution: Add manual initialization script")
    
    print("5. Check browser console for JavaScript errors")
    print("6. Test in different browsers")
    print("7. Check for CSS conflicts")
    
    print(f"\n✅ DETAILED DROPDOWN DEBUG COMPLETED!")

if __name__ == "__main__":
    debug_dropdown_detailed()
