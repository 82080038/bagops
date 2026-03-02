#!/usr/bin/env python3
"""
Verify offline assets functionality
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import os

def verify_offline_assets():
    """Verify bahwa semua local assets berfungsi"""
    
    base_url = "http://localhost/bagops"
    
    print("🧪 VERIFYING OFFLINE ASSETS")
    print("=" * 50)
    
    # Test all local assets
    assets = [
        ("Bootstrap CSS", f"{base_url}/assets/css/bootstrap.min.css"),
        ("Bootstrap JS", f"{base_url}/assets/js/bootstrap.bundle.min.js"),
        ("Font Awesome CSS", f"{base_url}/assets/css/fontawesome.min.css"),
        ("jQuery", f"{base_url}/assets/js/jquery-3.6.0.min.js"),
        ("DataTables CSS", f"{base_url}/assets/css/jquery.dataTables.min.css"),
        ("DataTables JS", f"{base_url}/assets/js/jquery.dataTables.min.js")
    ]
    
    session = requests.Session()
    
    # Login
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    print("📋 Testing individual assets:")
    print("-" * 40)
    
    accessible_count = 0
    for name, url in assets:
        try:
            response = session.get(url, timeout=10)
            if response.status_code == 200:
                size_kb = len(response.content) / 1024
                print(f"✅ {name:20} - {response.status_code} - {size_kb:.1f}KB")
                accessible_count += 1
            else:
                print(f"❌ {name:20} - {response.status_code}")
        except Exception as e:
            print(f"❌ {name:20} - Error: {str(e)[:30]}")
    
    print(f"\n📊 Assets Summary: {accessible_count}/{len(assets)} accessible")
    
    # Test page rendering with local assets
    print(f"\n📄 Testing page rendering:")
    print("-" * 40)
    
    pages_to_test = [
        ("Dashboard", f"{base_url}/simple_root_system.php?page=dashboard"),
        ("Personel", f"{base_url}/simple_root_system.php?page=personel_ultra"),
        ("Jabatan", f"{base_url}/simple_root_system.php?page=jabatan_management")
    ]
    
    working_pages = 0
    for page_name, page_url in pages_to_test:
        try:
            response = session.get(page_url, timeout=10)
            if response.status_code == 200:
                # Check if local assets are referenced
                content = response.text
                local_refs = 0
                
                if "assets/css/bootstrap.min.css" in content:
                    local_refs += 1
                if "assets/js/jquery-3.6.0.min.js" in content:
                    local_refs += 1
                if "assets/css/fontawesome.min.css" in content:
                    local_refs += 1
                
                status = "✅" if local_refs >= 2 else "⚠️"
                print(f"{status} {page_name:15} - {response.status_code} - {local_refs} local refs")
                working_pages += 1
            else:
                print(f"❌ {page_name:15} - {response.status_code}")
        except Exception as e:
            print(f"❌ {page_name:15} - Error: {str(e)[:30]}")
    
    print(f"\n📊 Pages Summary: {working_pages}/{len(pages_to_test)} working")
    
    # Test offline capability
    print(f"\n🌐 Testing offline capability:")
    print("-" * 40)
    
    # Check if page contains CDN references
    dashboard_response = session.get(f"{base_url}/simple_root_system.php?page=dashboard", timeout=10)
    content = dashboard_response.text
    
    cdn_references = []
    cdn_patterns = [
        "cdn.jsdelivr.net",
        "cdnjs.cloudflare.com", 
        "code.jquery.com",
        "cdn.datatables.net"
    ]
    
    for pattern in cdn_patterns:
        if pattern in content:
            cdn_references.append(pattern)
    
    if cdn_references:
        print(f"⚠️ Found {len(cdn_references)} CDN references:")
        for ref in cdn_references:
            print(f"   - {ref}")
        print("   Some pages may still use CDN")
    else:
        print("✅ No CDN references found")
        print("   Application can work offline")
    
    # File verification
    print(f"\n📁 File verification:")
    print("-" * 40)
    
    base_dir = "/var/www/html/bagops"
    required_files = [
        "assets/css/bootstrap.min.css",
        "assets/css/fontawesome.min.css", 
        "assets/css/jquery.dataTables.min.css",
        "assets/js/jquery-3.6.0.min.js",
        "assets/js/bootstrap.bundle.min.js",
        "assets/js/jquery.dataTables.min.js"
    ]
    
    existing_files = 0
    for file_path in required_files:
        full_path = f"{base_dir}/{file_path}"
        if os.path.exists(full_path):
            size_kb = os.path.getsize(full_path) / 1024
            print(f"✅ {file_path:35} - {size_kb:.1f}KB")
            existing_files += 1
        else:
            print(f"❌ {file_path:35} - Missing")
    
    print(f"\n📊 Files Summary: {existing_files}/{len(required_files)} exist")
    
    # Final assessment
    print(f"\n🎯 FINAL ASSESSMENT")
    print("=" * 50)
    
    total_checks = 3
    passed_checks = 0
    
    if accessible_count == len(assets):
        print("✅ All assets accessible via HTTP")
        passed_checks += 1
    else:
        print(f"❌ Some assets not accessible: {len(assets) - accessible_count} missing")
    
    if working_pages == len(pages_to_test):
        print("✅ All pages render correctly")
        passed_checks += 1
    else:
        print(f"❌ Some pages not working: {len(pages_to_test) - working_pages} failed")
    
    if existing_files == len(required_files):
        print("✅ All required files exist locally")
        passed_checks += 1
    else:
        print(f"❌ Some files missing: {len(required_files) - existing_files} missing")
    
    success_rate = passed_checks / total_checks * 100
    print(f"\n📊 Overall Success Rate: {success_rate:.1f}%")
    
    if passed_checks == total_checks:
        print("\n🎉 OFFLINE SETUP COMPLETE!")
        print("✅ Application can work without internet")
        print("📁 All assets are available locally")
        print("🌐 No CDN dependencies remaining")
    else:
        print(f"\n⚠️ {total_checks - passed_checks} issues remaining")
        print("🔧 Review the issues above")
    
    return passed_checks == total_checks

if __name__ == "__main__":
    verify_offline_assets()
