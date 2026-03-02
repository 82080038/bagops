#!/usr/bin/env python3
"""
Debug personel table rendering issues
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
from bs4 import BeautifulSoup

def debug_personel_table():
    """Debug personel table rendering"""
    
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
    print(f"📏 Content Length: {len(response.content)} bytes")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 ANALISIS PERSONEL TABLE")
    print("=" * 50)
    
    # Find personelTable_wrapper
    table_wrapper = soup.find('div', id='personelTable_wrapper')
    if table_wrapper:
        print("✅ personelTable_wrapper ditemukan")
        
        # Check for errors in wrapper
        error_elements = table_wrapper.find_all(class_=lambda x: x and 'error' in x.lower())
        if error_elements:
            print(f"⚠️ Ditemukan {len(error_elements)} error elements di wrapper:")
            for i, error in enumerate(error_elements, 1):
                error_text = error.get_text(strip=True)
                error_class = error.get('class', [])
                print(f"   {i}. Class: {error_class} - Text: {error_text[:100]}...")
        
        # Check table structure
        table = table_wrapper.find('table', id='personelTable')
        if table:
            print("✅ personelTable ditemukan")
            
            # Check table structure
            thead = table.find('thead')
            tbody = table.find('tbody')
            
            if thead:
                headers = thead.find_all('th')
                print(f"✅ Table header: {len(headers)} columns")
                for i, th in enumerate(headers, 1):
                    header_text = th.get_text(strip=True)
                    print(f"   {i}. {header_text}")
            
            if tbody:
                rows = tbody.find_all('tr')
                print(f"✅ Table body: {len(rows)} rows")
                
                # Check first few rows
                for i, row in enumerate(rows[:3], 1):
                    cells = row.find_all('td')
                    print(f"   Row {i}: {len(cells)} cells")
                    for j, cell in enumerate(cells, 1):
                        cell_text = cell.get_text(strip=True)
                        print(f"      Cell {j}: {cell_text[:30]}...")
                
                # Check for error cells
                error_cells = tbody.find_all(class_=lambda x: x and 'error' in x.lower())
                if error_cells:
                    print(f"⚠️ Ditemukan {len(error_cells)} error cells di tbody:")
                    for i, cell in enumerate(error_cells[:3], 1):
                        cell_text = cell.get_text(strip=True)
                        print(f"   {i}. {cell_text[:80]}...")
            else:
                print("❌ Table body tidak ditemukan")
        else:
            print("❌ personelTable tidak ditemukan")
            
            # Look for any table
            tables = table_wrapper.find_all('table')
            print(f"📋 Ditemukan {len(tables)} table lain di wrapper:")
            for i, table in enumerate(tables, 1):
                table_id = table.get('id', 'no-id')
                table_class = table.get('class', [])
                print(f"   {i}. ID: {table_id}, Class: {table_class}")
    else:
        print("❌ personelTable_wrapper tidak ditemukan")
        
        # Look for similar elements
        possible_wrappers = soup.find_all(id=lambda x: x and 'personel' in x.lower())
        print(f"🔍 Ditemukan {len(possible_wrappers)} elemen dengan 'personel' di ID:")
        for i, elem in enumerate(possible_wrappers, 1):
            elem_id = elem.get('id', 'no-id')
            elem_tag = elem.name
            elem_class = elem.get('class', [])
            print(f"   {i}. Tag: {elem_tag}, ID: {elem_id}, Class: {elem_class}")
    
    print("\n🔍 ANALISIS JAVASCRIPT & AJAX")
    print("=" * 50)
    
    # Find JavaScript code related to personelTable
    scripts = soup.find_all('script')
    personel_scripts = []
    
    for script in scripts:
        if script.string and 'personelTable' in script.string:
            personel_scripts.append(script.string)
    
    print(f"📋 Ditemukan {len(personel_scripts)} script dengan 'personelTable':")
    for i, script in enumerate(personel_scripts, 1):
        if script:
            script_lines = script.split('\n')
            for line_num, line in enumerate(script_lines, 1):
                if 'personelTable' in line:
                    print(f"   Script {i}, Line {line_num}: {line.strip()[:100]}...")
    
    # Check for DataTables initialization
    datatables_init = []
    for script in scripts:
        if script.string and ('DataTables' in script.string or 'dataTable' in script.string):
            datatables_init.append(script.string)
    
    print(f"\n📋 Ditemukan {len(datatables_init)} script dengan DataTables:")
    for i, script in enumerate(datatables_init, 1):
        if script:
            script_lines = script.split('\n')
            for line_num, line in enumerate(script_lines, 1):
                if any(keyword in line for keyword in ['DataTables', 'dataTable', 'ajax']):
                    print(f"   DataTables Script {i}, Line {line_num}: {line.strip()[:120]}...")
    
    print("\n🔍 ANALISIS AJAX ENDPOINTS")
    print("=" * 50)
    
    # Check for AJAX calls
    ajax_calls = []
    for script in scripts:
        if script.string:
            script_lines = script.split('\n')
            for line in script_lines:
                if 'ajax' in line.lower() and ('personel' in line.lower() or 'get_personel' in line.lower()):
                    ajax_calls.append(line.strip())
    
    print(f"📋 Ditemukan {len(ajax_calls)} AJAX calls untuk personel:")
    for i, call in enumerate(ajax_calls, 1):
        print(f"   {i}. {call}")
    
    # Test AJAX endpoint directly
    print(f"\n🧪 Testing AJAX endpoint...")
    try:
        ajax_url = f"{base_url}/ajax/get_personel.php"
        ajax_response = session.get(ajax_url, timeout=10)
        print(f"📊 AJAX Status: {ajax_response.status_code}")
        print(f"📏 AJAX Content Length: {len(ajax_response.content)} bytes")
        
        if ajax_response.status_code == 200:
            try:
                import json
                ajax_data = ajax_response.json()
                print(f"✅ AJAX Response JSON valid")
                if 'data' in ajax_data:
                    print(f"📊 Total records: {len(ajax_data['data'])}")
                    if ajax_data['data']:
                        print(f"📋 Sample record:")
                        sample = ajax_data['data'][0]
                        for key, value in list(sample.items())[:5]:
                            print(f"   {key}: {value}")
                else:
                    print("⚠️ No 'data' key in AJAX response")
                    print(f"📋 Response keys: {list(ajax_data.keys())}")
            except json.JSONDecodeError as e:
                print(f"❌ AJAX Response bukan JSON: {str(e)}")
                print(f"📋 Response preview: {ajax_response.text[:200]}...")
        else:
            print(f"❌ AJAX endpoint error: {ajax_response.status_code}")
            print(f"📋 Response: {ajax_response.text[:200]}...")
            
    except Exception as e:
        print(f"❌ Error testing AJAX: {str(e)}")
    
    print("\n🔍 ANALISIS ERROR MESSAGES")
    print("=" * 50)
    
    # Find all error messages
    error_alerts = soup.find_all('div', class_='alert-danger')
    warning_alerts = soup.find_all('div', class_='alert-warning')
    
    if error_alerts:
        print(f"❌ Ditemukan {len(error_alerts)} error alerts:")
        for i, alert in enumerate(error_alerts, 1):
            error_text = alert.get_text(strip=True)
            print(f"   {i}. {error_text}")
    
    if warning_alerts:
        print(f"⚠️ Ditemukan {len(warning_alerts)} warning alerts:")
        for i, alert in enumerate(warning_alerts, 1):
            warning_text = alert.get_text(strip=True)
            print(f"   {i}. {warning_text}")
    
    # Check for console errors in JavaScript
    console_errors = []
    for script in scripts:
        if script.string:
            script_lines = script.split('\n')
            for line in script_lines:
                if any(keyword in line.lower() for keyword in ['console.error', 'error:', 'failed', 'unable']):
                    console_errors.append(line.strip())
    
    if console_errors:
        print(f"\n❌ Ditemukan {len(console_errors)} possible console errors:")
        for i, error in enumerate(console_errors[:5], 1):
            print(f"   {i}. {error}")
    
    print("\n✅ DEBUG PERSONEL TABLE SELESAI!")

if __name__ == "__main__":
    debug_personel_table()
