#!/usr/bin/env python3
"""
Debug navigation HTML output
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import re
from bs4 import BeautifulSoup

def debug_navigation_html():
    """Debug navigation HTML output"""
    
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
    
    html_content = response.text
    
    print("\n🔍 DEBUGGING NAVIGATION HTML")
    print("=" * 50)
    
    # Extract the navigation section
    nav_start = html_content.find('<ul class="navbar-nav mx-auto">')
    nav_end = html_content.find('</ul>', nav_start) + 5
    
    if nav_start != -1 and nav_end != -1:
        nav_html = html_content[nav_start:nav_end]
        print("📋 Navigation HTML found:")
        print("-" * 40)
        print(nav_html)
        print("-" * 40)
        
        # Count LI tags in navigation
        nav_li_opens = nav_html.count('<li')
        nav_li_closes = nav_html.count('</li>')
        
        print(f"\n📊 Navigation LI tags:")
        print(f"   Opening: {nav_li_opens}")
        print(f"   Closing: {nav_li_closes}")
        
        if nav_li_opens != nav_li_closes:
            print(f"   ❌ Mismatch: {nav_li_opens - nav_li_closes} unclosed LI tags")
            
            # Find each LI in navigation
            li_pattern = r'<li[^>]*>.*?(?=</li>|<li|$)'
            li_matches = re.findall(li_pattern, nav_html, re.DOTALL)
            
            print(f"\n📋 Found {len(li_matches)} LI elements:")
            for i, li in enumerate(li_matches, 1):
                # Check if it has closing tag
                has_close = '</li>' in li
                status = "✅" if has_close else "❌"
                
                # Truncate for display
                display_li = li[:100] + "..." if len(li) > 100 else li
                print(f"   {status} LI {i}: {display_li}")
                
                if not has_close:
                    print(f"      Missing </li> tag")
        else:
            print(f"   ✅ All LI tags properly closed")
    
    # Check if the issue is in the dropdown section
    print(f"\n🔍 CHECKING DROPDOWN SECTION")
    print("-" * 40)
    
    dropdown_start = html_content.find('<li class="nav-item dropdown">')
    if dropdown_start != -1:
        # Find the end of this dropdown LI
        dropdown_end = html_content.find('</li>', dropdown_start) + 5
        dropdown_html = html_content[dropdown_start:dropdown_end]
        
        print("📋 Dropdown HTML:")
        print(dropdown_html)
        
        # Check LI tags in dropdown
        dropdown_li_opens = dropdown_html.count('<li')
        dropdown_li_closes = dropdown_html.count('</li>')
        
        print(f"\n📊 Dropdown LI tags:")
        print(f"   Opening: {dropdown_li_opens}")
        print(f"   Closing: {dropdown_li_closes}")
    
    # Look for any malformed HTML
    print(f"\n🔍 CHECKING FOR MALFORMED HTML")
    print("-" * 40)
    
    # Find lines with LI tags
    lines = html_content.split('\n')
    problematic_lines = []
    
    for line_num, line in enumerate(lines, 1):
        if '<li' in line:
            li_count = line.count('<li')
            li_close_count = line.count('</li>')
            
            if li_count != li_close_count:
                problematic_lines.append({
                    'line_num': line_num,
                    'line': line.strip(),
                    'li_open': li_count,
                    'li_close': li_close_count
                })
    
    if problematic_lines:
        print(f"⚠️ Found {len(problematic_lines)} problematic lines:")
        for line_info in problematic_lines[:10]:  # Show first 10
            print(f"   Line {line_info['line_num']}: {line_info['li_open']} opens, {line_info['li_close']} closes")
            print(f"   {line_info['line']}")
    else:
        print("✅ No obvious line-level issues found")
    
    print(f"\n✅ NAVIGATION HTML DEBUG COMPLETED!")

if __name__ == "__main__":
    debug_navigation_html()
