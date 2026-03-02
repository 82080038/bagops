#!/usr/bin/env python3
"""
Final test untuk memastikan tidak ada browser console errors
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
from bs4 import BeautifulSoup

def test_browser_console():
    """Test untuk memastikan tidak ada browser console errors"""
    
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
    
    print("\n🔍 ANALISIS JAVASCRIPT CONFIGURATION")
    print("=" * 50)
    
    # Find DataTables script
    scripts = soup.find_all('script')
    datatables_config = None
    
    for script in scripts:
        if script.string and 'personelTable' in script.string and 'DataTable' in script.string:
            datatables_config = script.string
            break
    
    if datatables_config:
        print("✅ DataTables configuration found")
        
        # Check for potential issues
        issues = []
        
        # Check AJAX URL
        if 'url: \'ajax/get_personel.php\'' in datatables_config:
            print("✅ Correct AJAX URL")
        else:
            issues.append("Incorrect AJAX URL")
        
        # Check error handling
        if 'error:' in datatables_config:
            print("✅ Error handling configured")
        else:
            issues.append("No error handling")
        
        # Check processing indicator
        if 'processing: true' in datatables_config:
            print("✅ Processing indicator enabled")
        else:
            issues.append("No processing indicator")
        
        # Check serverSide
        if 'serverSide: true' in datatables_config:
            print("✅ Server-side processing enabled")
        else:
            issues.append("No server-side processing")
        
        # Check responsive
        if 'responsive: true' in datatables_config:
            print("✅ Responsive design enabled")
        else:
            issues.append("No responsive design")
        
        if issues:
            print(f"\n⚠️ Potential Issues Found:")
            for issue in issues:
                print(f"   - {issue}")
        else:
            print("\n✅ No configuration issues found")
    
    else:
        print("❌ DataTables configuration not found")
        return
    
    print("\n🧪 TESTING AJAX ENDPOINTS")
    print("=" * 50)
    
    # Test main AJAX endpoint
    try:
        ajax_url = f"{base_url}/ajax/get_personel.php"
        ajax_response = session.get(ajax_url, timeout=10)
        
        if ajax_response.status_code == 200:
            print("✅ AJAX endpoint accessible")
            
            try:
                import json
                ajax_data = ajax_response.json()
                
                # Check response structure
                required_fields = ['draw', 'recordsTotal', 'recordsFiltered', 'data']
                missing_fields = [field for field in required_fields if field not in ajax_data]
                
                if not missing_fields:
                    print("✅ AJAX response structure correct")
                    print(f"📊 Records: {ajax_data['recordsTotal']}")
                    print(f"📊 Data rows: {len(ajax_data['data'])}")
                else:
                    print(f"❌ Missing fields: {missing_fields}")
                
            except json.JSONDecodeError:
                print("❌ AJAX response not valid JSON")
        else:
            print(f"❌ AJAX endpoint failed: {ajax_response.status_code}")
    
    except Exception as e:
        print(f"❌ AJAX test error: {str(e)}")
    
    print("\n🔍 ANALISIS POTENTIAL CONSOLE ERRORS")
    print("=" * 50)
    
    # Check for common JavaScript error patterns
    error_patterns = [
        'undefined',
        'null',
        'cannot read',
        'is not a function',
        'failed to load',
        '404',
        '500',
        'error',
        'exception'
    ]
    
    found_errors = []
    
    for script in scripts:
        if script.string:
            for pattern in error_patterns:
                if pattern in script.string.lower() and 'console' not in script.string.lower():
                    # Find the line with the pattern
                    lines = script.string.split('\n')
                    for line_num, line in enumerate(lines, 1):
                        if pattern.lower() in line.lower():
                            found_errors.append(f"Line {line_num}: {line.strip()[:80]}")
                            break
    
    if found_errors:
        print(f"⚠️ Found {len(found_errors)} potential error patterns:")
        for error in found_errors[:5]:  # Show first 5
            print(f"   {error}")
        if len(found_errors) > 5:
            print(f"   ... and {len(found_errors) - 5} more")
    else:
        print("✅ No obvious JavaScript error patterns found")
    
    print("\n🔍 ANALISIS DEPENDENCIES")
    print("=" * 50)
    
    # Check for required JavaScript libraries
    required_scripts = [
        'jquery',
        'datatables',
        'bootstrap'
    ]
    
    found_scripts = []
    
    for script in scripts:
        if script.get('src'):
            src = script.get('src', '').lower()
            for lib in required_scripts:
                if lib in src:
                    found_scripts.append(lib)
                    break
    
    print(f"📋 Found JavaScript libraries: {found_scripts}")
    
    missing_libs = [lib for lib in required_scripts if lib not in found_scripts]
    if missing_libs:
        print(f"⚠️ Missing libraries: {missing_libs}")
    else:
        print("✅ All required libraries found")
    
    print("\n📊 FINAL ASSESSMENT")
    print("=" * 50)
    
    # Overall assessment
    assessment_points = [
        ("Page Loads", response.status_code == 200),
        ("DataTables Config", datatables_config is not None),
        ("AJAX URL Correct", 'url: \'ajax/get_personel.php\'' in (datatables_config or '')),
        ("AJAX Endpoint Works", ajax_response.status_code == 200 if 'ajax_response' in locals() else False),
        ("No JS Error Patterns", len(found_errors) == 0),
        ("All Libraries Present", len(missing_libs) == 0)
    ]
    
    passed = sum(1 for _, result in assessment_points if result)
    total = len(assessment_points)
    
    print(f"Assessment: {passed}/{total} points ({passed/total*100:.1f}%)")
    
    for point_name, result in assessment_points:
        status = "✅" if result else "❌"
        print(f"{status} {point_name}")
    
    if passed == total:
        print("\n🎉 EXCELLENT! No browser console errors expected!")
        print("📱 Personel table should work perfectly in browser!")
    else:
        print(f"\n⚠️ {total-passed} issues found. Review above details.")
    
    print("\n✅ BROWSER CONSOLE TEST COMPLETED!")

if __name__ == "__main__":
    test_browser_console()
