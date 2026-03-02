#!/usr/bin/env python3
"""
Test personel header count fix
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup
import re

def test_personel_header_fix():
    """Test personel header count fix"""
    
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
    
    print("\n🔍 TESTING PERSONEL HEADER COUNT FIX")
    print("=" * 60)
    
    # Find the header - try multiple approaches
    header = None
    
    # Try to find by exact text pattern
    headers = soup.find_all('h6', class_='font-weight-bold text-primary')
    for h in headers:
        if 'Daftar Personel' in h.get_text():
            header = h
            break
    
    # If not found, try broader search
    if not header:
        headers = soup.find_all('h6')
        for h in headers:
            if 'Daftar Personel' in h.get_text():
                header = h
                break
    
    # If still not found, try any element with the text
    if not header:
        elements = soup.find_all(text=lambda text: text and 'Daftar Personel' in text)
        for elem in elements:
            parent = elem.parent
            if parent.name in ['h6', 'h5', 'h4', 'h3', 'h2', 'h1']:
                header = parent
                break
    
    if header:
        header_text = header.get_text(strip=True)
        print(f"✅ Header found: '{header_text}'")
        
        # Extract count from header
        count_match = re.search(r'\((\d+(?:,\d+)*) records\)', header_text)
        if count_match:
            header_count = count_match.group(1)
            print(f"✅ Header count: {header_count}")
        else:
            print("❌ Could not extract count from header")
            return
    else:
        print("❌ Header not found")
        return
    
    # Check total personel card
    print(f"\n📋 CHECKING TOTAL PERSONEL CARD")
    print("-" * 40)
    
    total_card = soup.find('div', class_='text-xs font-weight-bold text-primary text-uppercase mb-1')
    if total_card and 'Total Personel' in total_card.get_text():
        # Find the count value
        count_div = total_card.find_next('div', class_='h5 mb-0 font-weight-bold text-gray-800')
        if count_div:
            card_count = count_div.get_text(strip=True)
            print(f"✅ Total Personel card: {card_count}")
            
            # Remove formatting for comparison
            card_count_clean = card_count.replace(',', '')
            header_count_clean = header_count.replace(',', '')
            
            if card_count_clean == header_count_clean:
                print("✅ Header count matches card count")
            else:
                print(f"⚠️ Mismatch: Header={header_count}, Card={card_count}")
        else:
            print("❌ Could not find count in total personel card")
    else:
        print("❌ Total Personel card not found")
    
    # Check DataTables info
    print(f"\n📋 CHECKING DATATABLES INFO")
    print("-" * 40)
    
    # Try to get actual count from database via AJAX
    try:
        ajax_response = session.get(f"{base_url}/ajax/get_personel.php", timeout=10)
        
        if ajax_response.status_code == 200:
            try:
                import json
                ajax_data = ajax_response.json()
                
                if 'recordsTotal' in ajax_data:
                    datatable_count = ajax_data['recordsTotal']
                    print(f"✅ DataTables recordsTotal: {datatable_count}")
                    
                    # Compare with header
                    header_count_clean = header_count.replace(',', '')
                    if str(datatable_count) == header_count_clean:
                        print("✅ Header count matches DataTables count")
                    else:
                        print(f"⚠️ Mismatch: Header={header_count}, DataTables={datatable_count}")
                
                if 'data' in ajax_data:
                    displayed_count = len(ajax_data['data'])
                    print(f"✅ DataTables displayed records: {displayed_count}")
                
            except json.JSONDecodeError:
                print("⚠️ AJAX response is not JSON")
        else:
            print(f"❌ AJAX request failed: {ajax_response.status_code}")
    
    except Exception as e:
        print(f"❌ Error checking AJAX: {str(e)}")
    
    # Check PHP variables in page source
    print(f"\n📋 CHECKING PHP VARIABLES")
    print("-" * 40)
    
    page_content = response.text
    
    # Look for PHP count variable
    if '<?php echo number_format($count); ?>' in page_content:
        print("✅ Found PHP $count variable in header")
    elif '<?php echo number_format(count($personel)); ?>' in page_content:
        print("❌ Still using old count($personel) - fix may not be applied")
    else:
        print("⚠️ Could not determine PHP variable usage")
    
    # Summary
    print(f"\n📊 HEADER COUNT FIX SUMMARY")
    print("=" * 60)
    
    print(f"Header Text: {header_text}")
    print(f"Header Count: {header_count}")
    
    if 'card_count' in locals():
        print(f"Card Count: {card_count}")
        print(f"Match: {'✅' if card_count_clean == header_count_clean else '❌'}")
    
    if 'datatable_count' in locals():
        print(f"DataTables Count: {datatable_count}")
        print(f"Match: {'✅' if str(datatable_count) == header_count_clean else '❌'}")
    
    print(f"\n🎯 EXPECTED BEHAVIOR")
    print("=" * 60)
    print("After fix:")
    print("1. ✅ Header should show total database records")
    print("2. ✅ Header count should match card count")
    print("3. ✅ Header count should match DataTables recordsTotal")
    print("4. ✅ Should not show just 10 records")
    
    print(f"\n✅ PERSONEL HEADER COUNT FIX TEST COMPLETED!")

if __name__ == "__main__":
    test_personel_header_fix()
