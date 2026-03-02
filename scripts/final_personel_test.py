#!/usr/bin/env python3
"""
Final test untuk personel table functionality
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
from bs4 import BeautifulSoup

def test_personel_table_functionality():
    """Test personel table functionality"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Test 1: Page rendering
    print("\n📄 TEST 1: Page Rendering")
    print("-" * 40)
    
    page_url = f"{base_url}/simple_root_system.php?page=personel_ultra"
    response = session.get(page_url, timeout=10)
    
    print(f"📊 HTTP Status: {response.status_code}")
    print(f"📏 Content Length: {len(response.content)} bytes")
    
    if response.status_code == 200:
        print("✅ Page renders successfully")
    else:
        print("❌ Page render failed")
        return
    
    # Test 2: Table structure
    print("\n📋 TEST 2: Table Structure")
    print("-" * 40)
    
    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Check wrapper
    wrapper = soup.find('div', id='personelTable_wrapper')
    if wrapper:
        print("✅ personelTable_wrapper found")
    else:
        print("❌ personelTable_wrapper not found")
        return
    
    # Check table
    table = soup.find('table', id='personelTable')
    if table:
        print("✅ personelTable found")
        
        # Check headers
        thead = table.find('thead')
        if thead:
            headers = thead.find_all('th')
            print(f"✅ Headers: {len(headers)} columns")
            expected_headers = ['NRP', 'Nama', 'Pangkat', 'Jabatan', 'Unit', 'Status', 'Aksi']
            actual_headers = [th.get_text(strip=True) for th in headers]
            
            if actual_headers == expected_headers:
                print("✅ Headers match expected structure")
            else:
                print(f"⚠️ Headers mismatch. Expected: {expected_headers}, Got: {actual_headers}")
        
        # Check body
        tbody = table.find('tbody')
        if tbody:
            rows = tbody.find_all('tr')
            print(f"✅ Body: {len(rows)} rows")
            
            # Check for error indicators
            colspan_cells = tbody.find_all('td', colspan=True)
            if colspan_cells:
                print(f"⚠️ Found {len(colspan_cells)} colspan cells (might be error messages)")
                for cell in colspan_cells:
                    text = cell.get_text(strip=True)
                    print(f"   - {text}")
            else:
                print("✅ No error indicators found")
        else:
            print("❌ Table body not found")
    else:
        print("❌ personelTable not found")
        return
    
    # Test 3: AJAX endpoint
    print("\n🔄 TEST 3: AJAX Endpoint")
    print("-" * 40)
    
    ajax_url = f"{base_url}/ajax/get_personel.php"
    ajax_response = session.get(ajax_url, timeout=10)
    
    print(f"📊 AJAX Status: {ajax_response.status_code}")
    
    if ajax_response.status_code == 200:
        try:
            import json
            ajax_data = ajax_response.json()
            
            # Check required fields
            required_fields = ['draw', 'recordsTotal', 'recordsFiltered', 'data']
            missing_fields = [field for field in required_fields if field not in ajax_data]
            
            if not missing_fields:
                print("✅ AJAX response has all required fields")
                print(f"📊 Total Records: {ajax_data['recordsTotal']}")
                print(f"📊 Filtered Records: {ajax_data['recordsFiltered']}")
                print(f"📊 Data Rows: {len(ajax_data['data'])}")
                
                # Check data structure
                if ajax_data['data']:
                    sample_row = ajax_data['data'][0]
                    if len(sample_row) == 7:
                        print("✅ Data rows have correct column count (7)")
                        print(f"📋 Sample data: {sample_row[:3]}...")
                    else:
                        print(f"⚠️ Data rows have {len(sample_row)} columns (expected 7)")
                else:
                    print("⚠️ No data rows returned")
            else:
                print(f"❌ Missing fields in AJAX response: {missing_fields}")
                print(f"📋 Response keys: {list(ajax_data.keys())}")
                
        except json.JSONDecodeError as e:
            print(f"❌ AJAX response is not valid JSON: {str(e)}")
            print(f"📋 Response preview: {ajax_response.text[:200]}...")
    else:
        print(f"❌ AJAX endpoint failed: {ajax_response.status_code}")
        print(f"📋 Response: {ajax_response.text[:200]}...")
    
    # Test 4: JavaScript functionality
    print("\n📜 TEST 4: JavaScript Functionality")
    print("-" * 40)
    
    scripts = soup.find_all('script')
    datatables_found = False
    ajax_config_found = False
    
    for script in scripts:
        if script.string:
            if 'DataTable' in script.string:
                datatables_found = True
            if 'ajax:' in script.string and 'get_personel.php' in script.string:
                ajax_config_found = True
    
    if datatables_found:
        print("✅ DataTables initialization found")
    else:
        print("❌ DataTables initialization not found")
    
    if ajax_config_found:
        print("✅ AJAX configuration found")
    else:
        print("❌ AJAX configuration not found")
    
    # Test 5: Error handling
    print("\n⚠️ TEST 5: Error Handling")
    print("-" * 40)
    
    # Check for error alerts
    error_alerts = soup.find_all('div', class_='alert-danger')
    warning_alerts = soup.find_all('div', class_='alert-warning')
    
    if not error_alerts and not warning_alerts:
        print("✅ No error or warning alerts found")
    else:
        print(f"⚠️ Found {len(error_alerts)} error alerts and {len(warning_alerts)} warning alerts")
        for alert in error_alerts:
            print(f"   Error: {alert.get_text(strip())}")
        for alert in warning_alerts:
            print(f"   Warning: {alert.get_text(strip())}")
    
    # Test 6: Performance
    print("\n⏱️ TEST 6: Performance")
    print("-" * 40)
    
    start_time = time.time()
    page_response = session.get(page_url, timeout=10)
    page_load_time = time.time() - start_time
    
    start_time = time.time()
    ajax_response = session.get(ajax_url, timeout=10)
    ajax_load_time = time.time() - start_time
    
    print(f"📊 Page load time: {page_load_time:.3f}s")
    print(f"📊 AJAX load time: {ajax_load_time:.3f}s")
    
    if page_load_time < 2.0:
        print("✅ Page load time is good")
    else:
        print("⚠️ Page load time is slow")
    
    if ajax_load_time < 1.0:
        print("✅ AJAX load time is good")
    else:
        print("⚠️ AJAX load time is slow")
    
    # Summary
    print("\n📊 SUMMARY")
    print("=" * 50)
    
    tests = [
        ("Page Rendering", response.status_code == 200),
        ("Table Structure", wrapper is not None and table is not None),
        ("AJAX Endpoint", ajax_response.status_code == 200),
        ("DataTables", datatables_found),
        ("AJAX Config", ajax_config_found),
        ("Error Handling", len(error_alerts) == 0 and len(warning_alerts) == 0),
        ("Performance", page_load_time < 2.0 and ajax_load_time < 1.0)
    ]
    
    passed = sum(1 for _, result in tests if result)
    total = len(tests)
    
    print(f"Tests Passed: {passed}/{total}")
    print(f"Success Rate: {passed/total*100:.1f}%")
    
    for test_name, result in tests:
        status = "✅" if result else "❌"
        print(f"{status} {test_name}")
    
    if passed == total:
        print("\n🎉 ALL TESTS PASSED! Personel table is working perfectly!")
    else:
        print(f"\n⚠️ {total-passed} tests failed. Review the issues above.")
    
    print("\n✅ PERSONEL TABLE TESTING COMPLETED!")

if __name__ == "__main__":
    test_personel_table_functionality()
