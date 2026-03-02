#!/usr/bin/env python3
"""
Debug table width issues for personel table
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def debug_table_width():
    """Debug table width issues"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Get personel page
    print("📄 Mengambil halaman personel_ultra...")
    page_url = f"{base_url}/simple_root_system.php?page=personel_ultra"
    response = session.get(page_url, timeout=10)
    
    print(f"📊 HTTP Status: {response.status_code}")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 ANALISIS TABLE WIDTH")
    print("=" * 50)
    
    # Find the personel table
    table = soup.find('table', {'id': 'personelTable'})
    
    if table:
        print("✅ Personel table found")
        
        # Check table classes and attributes
        print(f"\n📋 TABLE ATTRIBUTES:")
        print(f"   ID: {table.get('id')}")
        print(f"   Classes: {table.get('class', [])}")
        print(f"   Style: {table.get('style', 'None')}")
        
        # Check parent containers
        print(f"\n📋 PARENT CONTAINERS:")
        parent = table.find_parent()
        level = 1
        
        while parent and level <= 5:
            tag_name = parent.name
            classes = parent.get('class', [])
            styles = parent.get('style', '')
            
            print(f"   Level {level}: <{tag_name}> class={classes} style={styles[:50] if styles else 'None'}")
            
            # Check for width constraints
            if styles and ('width' in styles.lower() or 'max-width' in styles.lower()):
                print(f"      ⚠️ WIDTH CONSTRAINT FOUND: {styles}")
            
            parent = parent.find_parent()
            level += 1
        
        # Check table wrapper
        wrapper = soup.find('div', {'id': 'personelTable_wrapper'})
        if wrapper:
            print(f"\n📋 TABLE WRAPPER:")
            print(f"   ID: {wrapper.get('id')}")
            print(f"   Classes: {wrapper.get('class', [])}")
            print(f"   Style: {wrapper.get('style', 'None')}")
            
            wrapper_style = wrapper.get('style', '')
            if wrapper_style:
                print(f"      ⚠️ WRAPPER STYLE: {wrapper_style}")
        
        # Check main content area
        main_content = soup.find('main') or soup.find('div', {'class': 'main-content'}) or soup.find('div', {'id': 'main'})
        if main_content:
            print(f"\n📋 MAIN CONTENT AREA:")
            print(f"   Tag: {main_content.name}")
            print(f"   Classes: {main_content.get('class', [])}")
            print(f"   Style: {main_content.get('style', 'None')}")
        
        # Check container
        container = soup.find('div', {'class': 'container'}) or soup.find('div', {'class': 'container-fluid'})
        if container:
            print(f"\n📋 CONTAINER:")
            print(f"   Classes: {container.get('class', [])}")
            print(f"   Style: {container.get('style', 'None')}")
        
        # Analyze table structure
        print(f"\n📋 TABLE STRUCTURE:")
        thead = table.find('thead')
        tbody = table.find('tbody')
        
        if thead:
            headers = thead.find_all('th')
            print(f"   Headers: {len(headers)} columns")
            for i, th in enumerate(headers[:10], 1):  # Show first 10
                text = th.get_text(strip=True)
                classes = th.get('class', [])
                style = th.get('style', '')
                print(f"      {i}. '{text}' class={classes} style={style[:30] if style else 'None'}")
        
        if tbody:
            rows = tbody.find_all('tr')
            print(f"   Rows: {len(rows)} rows")
            
            if rows:
                first_row = rows[0]
                cells = first_row.find_all(['td', 'th'])
                print(f"   First row cells: {len(cells)}")
                
                for i, cell in enumerate(cells[:10], 1):  # Show first 10
                    text = cell.get_text(strip=True)
                    classes = cell.get('class', [])
                    style = cell.get('style', '')
                    print(f"      {i}. '{text[:20]}...' class={classes} style={style[:30] if style else 'None'}")
    
    else:
        print("❌ Personel table not found")
    
    # Check CSS that might affect width
    print(f"\n📋 CSS ANALYSIS")
    print("-" * 40)
    
    # Look for custom CSS in the page
    custom_styles = soup.find_all('style')
    if custom_styles:
        print(f"Found {len(custom_styles)} style tags")
        
        for i, style_tag in enumerate(custom_styles, 1):
            if style_tag.string:
                css_content = style_tag.string
                
                # Look for width-related CSS
                width_patterns = [
                    'width',
                    'max-width',
                    'container',
                    'table-responsive'
                ]
                
                relevant_css = []
                for line in css_content.split('\n'):
                    if any(pattern in line.lower() for pattern in width_patterns):
                        relevant_css.append(line.strip())
                
                if relevant_css:
                    print(f"\nStyle Tag {i} - Width-related CSS:")
                    for css_line in relevant_css[:5]:  # Show first 5
                        print(f"   {css_line}")
    
    # Check for DataTables CSS
    print(f"\n📋 DATABLES CONFIGURATION")
    print("-" * 40)
    
    scripts = soup.find_all('script')
    for script in scripts:
        if script.string and 'personelTable' in script.string:
            script_content = script.string
            
            # Look for DataTables configuration
            if 'DataTable(' in script_content:
                print("✅ DataTables configuration found")
                
                # Look for responsive configuration
                if 'responsive' in script_content:
                    print("   ✅ Responsive: enabled")
                else:
                    print("   ⚠️ Responsive: not found")
                
                # Look for width configuration
                if 'width' in script_content.lower():
                    print("   ⚠️ Width configuration found")
                else:
                    print("   ✅ No explicit width constraints")
    
    print(f"\n🔧 RECOMMENDATIONS")
    print("=" * 50)
    
    print("Common causes for table not being wide:")
    print("1. Container constraints (container vs container-fluid)")
    print("2. CSS width limitations")
    print("3. DataTables responsive behavior")
    print("4. Parent container width restrictions")
    print("5. Bootstrap grid system limitations")
    
    print(f"\nPotential solutions:")
    print("1. Use container-fluid instead of container")
    print("2. Add custom CSS for table width")
    print("3. Configure DataTables for full width")
    print("4. Remove width constraints from parent elements")
    
    print(f"\n✅ TABLE WIDTH DEBUG COMPLETED!")

if __name__ == "__main__":
    debug_table_width()
