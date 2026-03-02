#!/usr/bin/env python3
"""
Simple debug untuk personel table
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def debug_personel():
    """Debug personel table sederhana"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Get personel page
    print("📄 Mengambil personel_ultra...")
    page_url = f"{base_url}/simple_root_system.php?page=personel_ultra"
    response = session.get(page_url, timeout=10)
    
    print(f"📊 Status: {response.status_code}, Size: {len(response.content)} bytes")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 ANALISIS TABLE")
    print("=" * 50)
    
    # Find personel table
    table = soup.find('table', id='personelTable')
    if table:
        print("✅ personelTable ditemukan")
        
        # Check table structure
        thead = table.find('thead')
        tbody = table.find('tbody')
        
        if thead:
            headers = thead.find_all('th')
            print(f"✅ Header: {len(headers)} columns")
            for i, th in enumerate(headers, 1):
                print(f"   {i}. {th.get_text(strip=True)}")
        
        if tbody:
            rows = tbody.find_all('tr')
            print(f"✅ Body: {len(rows)} rows")
            
            # Check first few rows
            for i, row in enumerate(rows[:3], 1):
                cells = row.find_all('td')
                print(f"   Row {i}: {len(cells)} cells")
                for j, cell in enumerate(cells, 1):
                    cell_text = cell.get_text(strip=True)
                    print(f"      {j}. {cell_text[:30]}...")
            
            # Check for error cells
            error_cells = tbody.find_all('td', class_=lambda x: x and 'error' in str(x).lower())
            if error_cells:
                print(f"⚠️ Error cells: {len(error_cells)}")
                for cell in error_cells[:3]:
                    print(f"   - {cell.get_text(strip=True)}")
            
            # Check for colspan cells (error indicators)
            colspan_cells = tbody.find_all('td', colspan=True)
            if colspan_cells:
                print(f"⚠️ Colspan cells: {len(colspan_cells)}")
                for cell in colspan_cells:
                    colspan = cell.get('colspan', 'unknown')
                    text = cell.get_text(strip=True)
                    print(f"   - Colspan {colspan}: {text}")
        else:
            print("❌ Tbody tidak ditemukan")
    else:
        print("❌ personelTable tidak ditemukan")
    
    print("\n🔍 ANALISIS WRAPPER")
    print("=" * 50)
    
    # Look for wrapper
    wrapper = soup.find('div', id='personelTable_wrapper')
    if wrapper:
        print("✅ personelTable_wrapper ditemukan")
    else:
        print("❌ personelTable_wrapper tidak ditemukan")
        
        # Look for any wrapper
        wrappers = soup.find_all('div', class_=lambda x: x and 'wrapper' in str(x).lower())
        print(f"📋 Ditemukan {len(wrappers)} wrapper lain:")
        for i, w in enumerate(wrappers[:3], 1):
            wrapper_id = w.get('id', 'no-id')
            wrapper_class = w.get('class', [])
            print(f"   {i}. ID: {wrapper_id}, Class: {wrapper_class}")
    
    print("\n🔍 ANALISIS ERROR")
    print("=" * 50)
    
    # Find error alerts
    error_alerts = soup.find_all('div', class_='alert-danger')
    if error_alerts:
        print(f"❌ Error alerts: {len(error_alerts)}")
        for alert in error_alerts:
            print(f"   - {alert.get_text(strip())}")
    else:
        print("✅ Tidak ada error alerts")
    
    # Find warning alerts
    warning_alerts = soup.find_all('div', class_='alert-warning')
    if warning_alerts:
        print(f"⚠️ Warning alerts: {len(warning_alerts)}")
        for alert in warning_alerts:
            print(f"   - {alert.get_text(strip())}")
    else:
        print("✅ Tidak ada warning alerts")
    
    print("\n🔍 ANALISIS AJAX")
    print("=" * 50)
    
    # Test AJAX endpoint
    try:
        ajax_url = f"{base_url}/ajax/get_personel.php"
        ajax_response = session.get(ajax_url, timeout=10)
        print(f"📊 AJAX Status: {ajax_response.status_code}")
        
        if ajax_response.status_code == 200:
            try:
                import json
                ajax_data = ajax_response.json()
                print("✅ AJAX Response JSON valid")
                if 'data' in ajax_data:
                    print(f"📊 Records: {len(ajax_data['data'])}")
                    if ajax_data['data']:
                        sample = ajax_data['data'][0]
                        print(f"📋 Sample keys: {list(sample.keys())[:5]}")
                else:
                    print(f"⚠️ No 'data' key: {list(ajax_data.keys())}")
            except:
                print(f"❌ Bukan JSON: {ajax_response.text[:100]}...")
        else:
            print(f"❌ AJAX Error: {ajax_response.status_code}")
            print(f"Response: {ajax_response.text[:100]}...")
    except Exception as e:
        print(f"❌ AJAX Exception: {str(e)}")
    
    print("\n✅ DEBUG SELESAI!")

if __name__ == "__main__":
    debug_personel()
