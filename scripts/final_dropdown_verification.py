#!/usr/bin/env python3
"""
Final verification that dropdown should work
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def final_dropdown_verification():
    """Final verification that dropdown should work"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    print("🔍 FINAL DROPDOWN VERIFICATION")
    print("=" * 50)
    
    # Login
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    login_response = session.post(f"{base_url}/login.php", data=login_data)
    
    if login_response.status_code != 200:
        print("❌ Login failed")
        return
    
    print("✅ Login successful")
    
    # Get dashboard
    dashboard_response = session.get(f"{base_url}/simple_root_system.php?page=dashboard", timeout=10)
    
    if dashboard_response.status_code != 200:
        print("❌ Dashboard failed")
        return
    
    print("✅ Dashboard loaded")
    
    soup = BeautifulSoup(dashboard_response.content, 'html.parser')
    
    # Check all dropdown requirements
    print(f"\n📋 DROPDOWN REQUIREMENTS CHECK")
    print("-" * 40)
    
    requirements = {
        "Bootstrap CSS loaded": False,
        "Bootstrap JS loaded": False,
        "jQuery loaded": False,
        "Dropdown toggle found": False,
        "Dropdown menu found": False,
        "User info displayed": False,
        "Dropdown initialization script": False,
        "Correct HTML structure": False
    }
    
    # Check Bootstrap CSS
    bootstrap_css = soup.find('link', href=lambda x: x and 'bootstrap.min.css' in x)
    if bootstrap_css:
        requirements["Bootstrap CSS loaded"] = True
        print("✅ Bootstrap CSS loaded")
    else:
        print("❌ Bootstrap CSS not loaded")
    
    # Check Bootstrap JS
    bootstrap_js = soup.find('script', src=lambda x: x and 'bootstrap.bundle.min.js' in x)
    if bootstrap_js:
        requirements["Bootstrap JS loaded"] = True
        print("✅ Bootstrap JS loaded")
    else:
        print("❌ Bootstrap JS not loaded")
    
    # Check jQuery
    jquery_js = soup.find('script', src=lambda x: x and 'jquery-3.6.0.min.js' in x)
    if jquery_js:
        requirements["jQuery loaded"] = True
        print("✅ jQuery loaded")
    else:
        print("❌ jQuery not loaded")
    
    # Check dropdown toggle
    dropdown_toggle = soup.find('a', {'data-bs-toggle': 'dropdown'})
    if dropdown_toggle:
        requirements["Dropdown toggle found"] = True
        print("✅ Dropdown toggle found")
        
        # Check user info
        toggle_text = dropdown_toggle.get_text(strip=True)
        if 'super_admin' in toggle_text:
            requirements["User info displayed"] = True
            print("✅ User info displayed correctly")
        else:
            print(f"⚠️ User info: {toggle_text}")
    else:
        print("❌ Dropdown toggle not found")
    
    # Check dropdown menu
    dropdown_menu = soup.find('ul', class_='dropdown-menu')
    if dropdown_menu:
        requirements["Dropdown menu found"] = True
        menu_items = dropdown_menu.find_all('li')
        print(f"✅ Dropdown menu found ({len(menu_items)} items)")
    else:
        print("❌ Dropdown menu not found")
    
    # Check dropdown initialization script
    scripts = soup.find_all('script')
    for script in scripts:
        if script.string and 'bootstrap.Dropdown' in script.string:
            requirements["Dropdown initialization script"] = True
            print("✅ Dropdown initialization script found")
            break
    else:
        print("❌ Dropdown initialization script not found")
    
    # Check HTML structure
    dropdown_li = soup.find('li', class_='nav-item dropdown')
    if dropdown_li:
        has_toggle = dropdown_li.find('a', class_='dropdown-toggle')
        has_menu = dropdown_li.find('ul', class_='dropdown-menu')
        
        if has_toggle and has_menu:
            requirements["Correct HTML structure"] = True
            print("✅ Correct HTML structure")
        else:
            print("❌ Incorrect HTML structure")
    else:
        print("❌ Dropdown LI not found")
    
    # Summary
    passed = sum(1 for req, result in requirements.items() if result)
    total = len(requirements)
    
    print(f"\n📊 SUMMARY")
    print("=" * 50)
    print(f"Requirements passed: {passed}/{total}")
    print(f"Success rate: {passed/total*100:.1f}%")
    
    for req, result in requirements.items():
        status = "✅" if result else "❌"
        print(f"{status} {req}")
    
    if passed == total:
        print(f"\n🎉 ALL REQUIREMENTS MET!")
        print("✅ Dropdown should work perfectly")
        print("📱 Try clicking the user dropdown in the browser")
    else:
        print(f"\n⚠️ {total-passed} requirements not met")
        print("🔧 Review the failed requirements above")
    
    # Browser testing instructions
    print(f"\n🌐 BROWSER TESTING INSTRUCTIONS")
    print("=" * 50)
    print("1. Open browser and navigate to:")
    print(f"   {base_url}/login.php")
    print("2. Login with super_admin / admin123")
    print("3. Go to dashboard")
    print("4. Click on the user profile dropdown (top right)")
    print("5. Should see menu with Profile and Keluar options")
    print("6. Check browser console (F12) for any JavaScript errors")
    
    print(f"\n✅ FINAL VERIFICATION COMPLETED!")

if __name__ == "__main__":
    final_dropdown_verification()
