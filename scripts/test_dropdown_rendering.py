#!/usr/bin/env python3
"""
Test dropdown rendering issues
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import re
from bs4 import BeautifulSoup

def test_dropdown_rendering():
    """Test dropdown rendering issues"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    login_response = session.post(f"{base_url}/login.php", data=login_data)
    
    print(f"📊 Login Status: {login_response.status_code}")
    
    # Get dashboard page
    print("📄 Mengambil halaman dashboard...")
    page_url = f"{base_url}/simple_root_system.php?page=dashboard"
    response = session.get(page_url, timeout=10)
    
    print(f"📊 Page Status: {response.status_code}")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 ANALISIS DROPDOWN RENDERING")
    print("=" * 50)
    
    # Check for PHP errors in HTML
    html_content = response.text
    php_errors = []
    
    error_patterns = [
        r'Fatal error:',
        r'Parse error:',
        r'Warning:',
        r'Notice:',
        r'Undefined variable:',
        r'Call to undefined function:',
        r'Undefined index:'
    ]
    
    for pattern in error_patterns:
        matches = re.findall(pattern, html_content, re.IGNORECASE)
        if matches:
            php_errors.extend(matches)
    
    if php_errors:
        print(f"❌ Found {len(php_errors)} PHP errors:")
        for error in php_errors[:5]:  # Show first 5
            print(f"   - {error}")
    else:
        print("✅ No obvious PHP errors found")
    
    # Check dropdown structure in detail
    print(f"\n📱 DETAILED DROPDOWN ANALYSIS")
    print("-" * 40)
    
    # Find the dropdown li element
    dropdown_li = soup.find('li', class_='nav-item dropdown')
    
    if dropdown_li:
        print("✅ Dropdown LI element found")
        
        # Check toggle
        toggle = dropdown_li.find('a', class_='dropdown-toggle')
        if toggle:
            print("✅ Dropdown toggle found")
            print(f"   Text: '{toggle.get_text(strip=True)}'")
            print(f"   Classes: {toggle.get('class', [])}")
            
            # Check if user info is displayed
            img = toggle.find('img')
            if img:
                print(f"   ✅ Avatar img found: {img.get('src', 'no-src')}")
            else:
                print("   ❌ No avatar img found")
            
            user_div = toggle.find('div', class_='d-none d-lg-block')
            if user_div:
                username = user_div.find('div', class_='fw-semibold')
                role = user_div.find('div', class_='small text-muted')
                
                if username:
                    print(f"   ✅ Username: {username.get_text(strip=True)}")
                else:
                    print("   ❌ Username not found")
                
                if role:
                    print(f"   ✅ Role: {role.get_text(strip=True)}")
                else:
                    print("   ❌ Role not found")
            else:
                print("   ❌ User info div not found")
        else:
            print("❌ Dropdown toggle not found")
        
        # Check dropdown menu
        dropdown_menu = dropdown_li.find('ul', class_='dropdown-menu')
        if dropdown_menu:
            print("✅ Dropdown menu found")
            
            # Count menu items
            menu_items = dropdown_menu.find_all('li')
            print(f"   📝 Menu items: {len(menu_items)}")
            
            for i, item in enumerate(menu_items, 1):
                if 'dropdown-header' in item.get('class', []):
                    print(f"      {i}. Header: {item.get_text(strip=True)}")
                elif 'dropdown-divider' in item.get('class', []):
                    print(f"      {i}. Divider")
                else:
                    link = item.find('a')
                    if link:
                        text = link.get_text(strip=True)
                        href = link.get('href', 'no-href')
                        icon = link.find('i')
                        icon_class = icon.get('class', []) if icon else []
                        print(f"      {i}. {text} -> {href} (icon: {icon_class})")
        else:
            print("❌ Dropdown menu not found")
    else:
        print("❌ Dropdown LI element not found")
    
    # Check if currentUser is available
    print(f"\n🔍 ANALISIS CURRENTUSER VARIABLE")
    print("-" * 40)
    
    # Look for any indication of currentUser in HTML
    if 'super_admin' in html_content:
        print("✅ 'super_admin' found in HTML")
    else:
        print("❌ 'super_admin' not found in HTML")
    
    if 'GuestGuest' in html_content:
        print("⚠️ 'GuestGuest' found - currentUser may not be set")
    
    # Test other pages for comparison
    print(f"\n📄 TESTING OTHER PAGES")
    print("-" * 40)
    
    other_pages = ['personel', 'jabatan_management', 'profile']
    
    for page in other_pages:
        try:
            page_url = f"{base_url}/simple_root_system.php?page={page}"
            page_response = session.get(page_url, timeout=10)
            
            if page_response.status_code == 200:
                page_soup = BeautifulSoup(page_response.content, 'html.parser')
                page_dropdown = page_soup.find('li', class_='nav-item dropdown')
                
                if page_dropdown:
                    toggle = page_dropdown.find('a', class_='dropdown-toggle')
                    toggle_text = toggle.get_text(strip=True) if toggle else 'No toggle'
                    
                    menu = page_dropdown.find('ul', class_='dropdown-menu')
                    menu_items = len(menu.find_all('a', class_='dropdown-item')) if menu else 0
                    
                    print(f"   ✅ {page:15} - Toggle: '{toggle_text}' - Menu items: {menu_items}")
                else:
                    print(f"   ❌ {page:15} - No dropdown found")
            else:
                print(f"   ❌ {page:15} - HTTP {page_response.status_code}")
        
        except Exception as e:
            print(f"   ❌ {page:15} - Error: {str(e)[:30]}")
    
    print(f"\n🔧 DIAGNOSIS & SOLUTIONS")
    print("-" * 40)
    
    if not dropdown_li:
        print("❌ DIAGNOSIS: Dropdown structure missing")
        print("🔧 SOLUTION: Check layout file for dropdown HTML")
    
    elif not dropdown_li.find('ul', class_='dropdown-menu'):
        print("❌ DIAGNOSIS: Dropdown menu missing")
        print("🔧 SOLUTION: Check PHP errors preventing menu rendering")
        print("   - Check if currentUser variable is set")
        print("   - Check for PHP syntax errors")
    
    elif 'GuestGuest' in html_content:
        print("❌ DIAGNOSIS: currentUser not properly set")
        print("🔧 SOLUTION: Check authentication system")
        print("   - Verify session data")
        print("   - Check Auth class getCurrentUser() method")
    
    else:
        print("✅ DIAGNOSIS: Dropdown structure looks correct")
        print("🔧 SOLUTION: Check JavaScript initialization")
        print("   - Ensure Bootstrap JS loads properly")
        print("   - Check for JavaScript errors in browser console")
    
    print(f"\n✅ DROPDOWN RENDERING TEST COMPLETED!")

if __name__ == "__main__":
    test_dropdown_rendering()
