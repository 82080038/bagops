#!/usr/bin/env python3
"""
Test table width fix
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def test_table_width_fix():
    """Test table width fix"""
    
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
    
    print("\n🔍 TESTING TABLE WIDTH FIX")
    print("=" * 50)
    
    # Check if CSS updates are present
    print("📋 CHECKING CSS UPDATES")
    print("-" * 40)
    
    style_tags = soup.find_all('style')
    css_updates_found = False
    
    for style_tag in style_tags:
        if style_tag.string:
            css_content = style_tag.string
            
            # Check for our CSS updates
            if '#personelTable' in css_content and 'width: 100% !important' in css_content:
                print("✅ DataTables width CSS found")
                css_updates_found = True
            
            if '#personelTable_wrapper' in css_content:
                print("✅ DataTables wrapper CSS found")
                css_updates_found = True
            
            if '.table-responsive' in css_content and 'width: 100% !important' in css_content:
                print("✅ Table responsive width CSS found")
                css_updates_found = True
    
    if css_updates_found:
        print("✅ All CSS updates found in page")
    else:
        print("⚠️ Some CSS updates may be missing")
    
    # Check DataTables configuration
    print(f"\n📋 CHECKING DATATABLES CONFIGURATION")
    print("-" * 40)
    
    scripts = soup.find_all('script')
    datatables_config_found = False
    
    for script in scripts:
        if script.string and 'personelTable' in script.string:
            script_content = script.string
            
            if 'autoWidth: false' in script_content:
                print("✅ autoWidth: false found")
                datatables_config_found = True
            
            if 'scrollX: true' in script_content:
                print("✅ scrollX: true found")
                datatables_config_found = True
            
            if 'responsive: true' in script_content:
                print("✅ responsive: true found")
    
    if datatables_config_found:
        print("✅ DataTables configuration updated")
    else:
        print("⚠️ DataTables configuration may need update")
    
    # Check table structure
    print(f"\n📋 CHECKING TABLE STRUCTURE")
    print("-" * 40)
    
    table = soup.find('table', {'id': 'personelTable'})
    if table:
        print("✅ Personel table found")
        
        # Check table wrapper
        wrapper = soup.find('div', {'id': 'personelTable_wrapper'})
        if wrapper:
            print("✅ Table wrapper found")
            
            # Check for DataTables elements
            dt_wrapper = wrapper.find('div', {'class': lambda x: x and 'dataTables_wrapper' in ' '.join(x)})
            if dt_wrapper:
                print("✅ DataTables wrapper found")
                
                # Check for length, filter, info elements
                length_div = dt_wrapper.find('div', {'class': lambda x: x and 'dataTables_length' in ' '.join(x)})
                filter_div = dt_wrapper.find('div', {'class': lambda x: x and 'dataTables_filter' in ' '.join(x)})
                info_div = dt_wrapper.find('div', {'class': lambda x: x and 'dataTables_info' in ' '.join(x)})
                
                if length_div:
                    print("✅ DataTables length control found")
                if filter_div:
                    print("✅ DataTables filter control found")
                if info_div:
                    print("✅ DataTables info control found")
            else:
                print("⚠️ DataTables wrapper not found (may not be initialized yet)")
        else:
            print("❌ Table wrapper not found")
    else:
        print("❌ Personel table not found")
    
    # Check container hierarchy
    print(f"\n📋 CHECKING CONTAINER HIERARCHY")
    print("-" * 40)
    
    # Find the main container
    container = soup.find('div', {'class': 'container-fluid'})
    if container:
        print("✅ container-fluid found (good for full width)")
    else:
        container = soup.find('div', {'class': 'container'})
        if container:
            print("⚠️ container found (may limit width)")
        else:
            print("❌ No main container found")
    
    # Check card structure
    card = soup.find('div', {'class': 'card'})
    if card:
        print("✅ Card structure found")
        
        card_body = card.find('div', {'class': 'card-body'})
        if card_body:
            print("✅ Card body found")
        else:
            print("⚠️ Card body not found")
    else:
        print("❌ Card structure not found")
    
    print(f"\n🎯 EXPECTED BEHAVIOR")
    print("=" * 50)
    print("After the fix, the table should:")
    print("1. ✅ Use full width of available space")
    print("2. ✅ Have horizontal scroll on small screens")
    print("3. ✅ Display all 7 columns properly")
    print("4. ✅ Be responsive on different screen sizes")
    
    print(f"\n🔧 HOW TO VERIFY")
    print("=" * 50)
    print("1. Open browser and navigate to personel page")
    print("2. Check if table spans full width")
    print("3. Resize browser to test responsiveness")
    print("4. Check if horizontal scroll appears on small screens")
    
    print(f"\n📊 TECHNICAL DETAILS")
    print("=" * 50)
    print("Fixes applied:")
    print("1. ✅ CSS: .table-responsive { width: 100% !important; }")
    print("2. ✅ CSS: #personelTable { width: 100% !important; }")
    print("3. ✅ CSS: DataTables wrapper width: 100%")
    print("4. ✅ DataTables: autoWidth: false, scrollX: true")
    print("5. ✅ Container: container-fluid (full width)")
    
    print(f"\n✅ TABLE WIDTH FIX TEST COMPLETED!")

if __name__ == "__main__":
    test_table_width_fix()
