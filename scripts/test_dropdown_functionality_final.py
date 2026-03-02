#!/usr/bin/env python3
"""
Final test for dropdown functionality
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def test_dropdown_functionality():
    """Test dropdown functionality with local assets"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    print("🧪 FINAL DROPDOWN FUNCTIONALITY TEST")
    print("=" * 50)
    
    # Test 1: Simple dropdown test page
    print("\n📋 TEST 1: Simple Dropdown Test Page")
    print("-" * 40)
    
    try:
        test_url = f"{base_url}/test_dropdown.html"
        response = session.get(test_url, timeout=10)
        
        if response.status_code == 200:
            print("✅ Test page accessible")
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Check if assets are loaded
            bootstrap_css = soup.find('link', href=lambda x: x and 'bootstrap.min.css' in x)
            bootstrap_js = soup.find('script', src=lambda x: x and 'bootstrap.bundle.min.js' in x)
            jquery_js = soup.find('script', src=lambda x: x and 'jquery-3.6.0.min.js' in x)
            
            print(f"   Bootstrap CSS: {'✅' if bootstrap_css else '❌'}")
            print(f"   Bootstrap JS: {'✅' if bootstrap_js else '❌'}")
            print(f"   jQuery JS: {'✅' if jquery_js else '❌'}")
            
            # Check dropdown structure
            dropdown_toggle = soup.find('a', {'data-bs-toggle': 'dropdown'})
            dropdown_menu = soup.find('ul', class_='dropdown-menu')
            
            if dropdown_toggle and dropdown_menu:
                print("   ✅ Dropdown structure found")
                menu_items = dropdown_menu.find_all('a', class_='dropdown-item')
                print(f"   ✅ Menu items: {len(menu_items)}")
            else:
                print("   ❌ Dropdown structure not found")
        else:
            print(f"❌ Test page not accessible: {response.status_code}")
    
    except Exception as e:
        print(f"❌ Error testing dropdown page: {str(e)}")
    
    # Test 2: Main application dropdown
    print(f"\n📋 TEST 2: Main Application Dropdown")
    print("-" * 40)
    
    # Login first
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    login_response = session.post(f"{base_url}/login.php", data=login_data)
    
    if login_response.status_code == 200:
        print("✅ Login successful")
        
        # Get dashboard page
        dashboard_url = f"{base_url}/simple_root_system.php?page=dashboard"
        dashboard_response = session.get(dashboard_url, timeout=10)
        
        if dashboard_response.status_code == 200:
            print("✅ Dashboard accessible")
            
            soup = BeautifulSoup(dashboard_response.content, 'html.parser')
            
            # Check dropdown in main app
            main_dropdown = soup.find('li', class_='nav-item dropdown')
            
            if main_dropdown:
                print("✅ Main dropdown found")
                
                toggle = main_dropdown.find('a', class_='dropdown-toggle')
                menu = main_dropdown.find('ul', class_='dropdown-menu')
                
                if toggle and menu:
                    print("✅ Dropdown structure complete")
                    
                    # Check attributes
                    has_data_toggle = toggle.get('data-bs-toggle') == 'dropdown'
                    has_href = toggle.get('href') == '#'
                    has_role = toggle.get('role') == 'button'
                    has_aria = 'aria-expanded' in toggle.attrs
                    
                    print(f"   data-bs-toggle: {'✅' if has_data_toggle else '❌'}")
                    print(f"   href='#': {'✅' if has_href else '❌'}")
                    print(f"   role='button': {'✅' if has_role else '❌'}")
                    print(f"   aria-expanded: {'✅' if has_aria else '❌'}")
                    
                    # Check menu items
                    menu_items = menu.find_all('li')
                    print(f"   Menu items: {len(menu_items)}")
                    
                    # Check user info
                    toggle_text = toggle.get_text(strip=True)
                    if 'super_admin' in toggle_text:
                        print("✅ User info displayed correctly")
                    else:
                        print(f"⚠️ User info: {toggle_text}")
                    
                else:
                    print("❌ Dropdown structure incomplete")
            else:
                print("❌ Main dropdown not found")
        else:
            print(f"❌ Dashboard not accessible: {dashboard_response.status_code}")
    else:
        print(f"❌ Login failed: {login_response.status_code}")
    
    # Test 3: Asset accessibility
    print(f"\n📋 TEST 3: Asset Accessibility")
    print("-" * 40)
    
    assets_to_test = [
        ("Bootstrap CSS", f"{base_url}/assets/css/bootstrap.min.css"),
        ("Bootstrap JS", f"{base_url}/assets/js/bootstrap.bundle.min.js"),
        ("jQuery", f"{base_url}/assets/js/jquery-3.6.0.min.js"),
        ("Font Awesome", f"{base_url}/assets/css/fontawesome.min.css")
    ]
    
    for name, url in assets_to_test:
        try:
            response = session.get(url, timeout=5)
            status = "✅" if response.status_code == 200 else "❌"
            size_kb = len(response.content) / 1024
            print(f"   {name:15} - {status} {response.status_code} - {size_kb:.1f}KB")
        except Exception as e:
            print(f"   {name:15} - ❌ Error: {str(e)[:30]}")
    
    # Test 4: Bootstrap version check
    print(f"\n📋 TEST 4: Bootstrap Version Check")
    print("-" * 40)
    
    try:
        bootstrap_css_response = session.get(f"{base_url}/assets/css/bootstrap.min.css", timeout=5)
        if bootstrap_css_response.status_code == 200:
            css_content = bootstrap_css_response.text
            
            # Look for Bootstrap version
            if 'Bootstrap v5' in css_content:
                print("✅ Bootstrap 5 detected")
            elif 'Bootstrap' in css_content:
                print("✅ Bootstrap detected (version unclear)")
            else:
                print("⚠️ Bootstrap version unclear")
            
            # Check for dropdown styles
            if '.dropdown' in css_content:
                print("✅ Dropdown styles found")
            else:
                print("❌ No dropdown styles found")
        
        bootstrap_js_response = session.get(f"{base_url}/assets/js/bootstrap.bundle.min.js", timeout=5)
        if bootstrap_js_response.status_code == 200:
            js_content = bootstrap_js_response.text
            
            # Look for dropdown functionality
            if 'Dropdown' in js_content:
                print("✅ Dropdown functionality found")
            else:
                print("❌ No dropdown functionality found")
                
    except Exception as e:
        print(f"❌ Error checking Bootstrap version: {str(e)}")
    
    # Recommendations
    print(f"\n🔧 RECOMMENDATIONS")
    print("=" * 50)
    
    print("If dropdown still doesn't work:")
    print("1. Check browser console for JavaScript errors")
    print("2. Test the simple dropdown page: /test_dropdown.html")
    print("3. Ensure no CSS conflicts")
    print("4. Try different browsers")
    print("5. Check if Bootstrap is properly initialized")
    
    print(f"\n✅ FINAL DROPDOWN TEST COMPLETED!")

if __name__ == "__main__":
    test_dropdown_functionality()
