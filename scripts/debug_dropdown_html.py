#!/usr/bin/env python3
"""
Debug dropdown HTML structure in detail
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def debug_dropdown_html():
    """Debug dropdown HTML structure in detail"""
    
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
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 DETAILED DROPDOWN HTML ANALYSIS")
    print("=" * 60)
    
    # Find the dropdown
    dropdown_li = soup.find('li', class_='nav-item dropdown')
    
    if dropdown_li:
        print("✅ Dropdown LI found")
        
        # Get the complete HTML of the dropdown
        dropdown_html = str(dropdown_li)
        print(f"\n📋 Complete Dropdown HTML:")
        print("-" * 40)
        print(dropdown_html)
        print("-" * 40)
        
        # Analyze toggle
        toggle = dropdown_li.find('a', class_='dropdown-toggle')
        if toggle:
            print(f"\n🔗 TOGGLE ANALYSIS:")
            print(f"   Tag: {toggle.name}")
            print(f"   Classes: {toggle.get('class', [])}")
            print(f"   Text content: '{toggle.get_text(strip=True)}'")
            print(f"   Raw HTML: {str(toggle)}")
            
            # Check children
            children = list(toggle.children)
            print(f"   Children count: {len(children)}")
            
            for i, child in enumerate(children, 1):
                if hasattr(child, 'name'):
                    if child.name == 'img':
                        print(f"      {i}. IMG: src={child.get('src')}")
                    elif child.name == 'div':
                        print(f"      {i}. DIV: class={child.get('class')}")
                        div_text = child.get_text(strip=True)
                        print(f"         Text: '{div_text}'")
                        
                        # Check div children
                        div_children = list(child.children)
                        for j, div_child in enumerate(div_children, 1):
                            if hasattr(div_child, 'name'):
                                print(f"            {j}. {div_child.name}: {div_child.get_text(strip=True)}")
                else:
                    print(f"      {i}. Text: '{str(child).strip()}'")
        
        # Analyze menu
        menu = dropdown_li.find('ul', class_='dropdown-menu')
        if menu:
            print(f"\n📋 MENU ANALYSIS:")
            print(f"   Tag: {menu.name}")
            print(f"   Classes: {menu.get('class', [])}")
            
            menu_items = menu.find_all('li')
            print(f"   Menu items: {len(menu_items)}")
            
            for i, item in enumerate(menu_items, 1):
                item_classes = item.get('class', [])
                item_text = item.get_text(strip=True)
                
                if 'dropdown-header' in item_classes:
                    print(f"      {i}. HEADER: '{item_text}'")
                elif 'dropdown-divider' in item_classes:
                    print(f"      {i}. DIVIDER")
                else:
                    link = item.find('a')
                    if link:
                        link_text = link.get_text(strip=True)
                        link_href = link.get('href')
                        print(f"      {i}. LINK: '{link_text}' -> {link_href}")
    else:
        print("❌ Dropdown LI not found")
    
    # Check for any HTML issues
    print(f"\n🔍 HTML VALIDITY CHECK")
    print("-" * 40)
    
    # Check for unclosed tags
    html_content = response.text
    
    # Count opening and closing tags
    open_divs = html_content.count('<div')
    close_divs = html_content.count('</div>')
    open_lis = html_content.count('<li')
    close_lis = html_content.count('</li>')
    open_uls = html_content.count('<ul')
    close_uls = html_content.count('</ul>')
    
    print(f"DIV tags: {open_divs} open, {close_divs} close")
    print(f"LI tags: {open_lis} open, {close_lis} close")
    print(f"UL tags: {open_uls} open, {close_uls} close")
    
    if open_divs != close_divs:
        print("⚠️ Unclosed DIV tags detected")
    if open_lis != close_lis:
        print("⚠️ Unclosed LI tags detected")
    if open_uls != close_uls:
        print("⚠️ Unclosed UL tags detected")
    
    print(f"\n✅ DETAILED HTML ANALYSIS COMPLETED!")

if __name__ == "__main__":
    debug_dropdown_html()
