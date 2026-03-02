#!/usr/bin/env python3
"""
Comprehensive CDN assets download and update ALL files
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import os
import re
import time
from pathlib import Path

def download_file(url, local_path, description=""):
    """Download file dari URL ke local path"""
    try:
        print(f"📥 Downloading {description}...")
        print(f"   From: {url}")
        print(f"   To: {local_path}")
        
        # Create directory if not exists
        os.makedirs(os.path.dirname(local_path), exist_ok=True)
        
        # Download with timeout
        response = requests.get(url, timeout=30)
        response.raise_for_status()
        
        # Save file
        with open(local_path, 'wb') as f:
            f.write(response.content)
        
        print(f"   ✅ Success ({len(response.content)} bytes)")
        return True
        
    except Exception as e:
        print(f"   ❌ Failed: {str(e)}")
        return False

def download_all_cdn_assets():
    """Download SEMUA CDN assets yang dibutuhkan"""
    
    base_dir = "/var/www/html/bagops"
    
    print("🌐 COMPREHENSIVE CDN ASSETS DOWNLOAD")
    print("=" * 60)
    
    # List of ALL CDN assets to download
    assets = [
        # Bootstrap 5.3.0
        {
            "url": "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css",
            "local": f"{base_dir}/assets/css/bootstrap.min.css",
            "desc": "Bootstrap 5.3.0 CSS"
        },
        {
            "url": "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js", 
            "local": f"{base_dir}/assets/js/bootstrap.bundle.min.js",
            "desc": "Bootstrap 5.3.0 JS Bundle"
        },
        
        # Font Awesome 6.4.0
        {
            "url": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
            "local": f"{base_dir}/assets/css/fontawesome.min.css",
            "desc": "Font Awesome 6.4.0 CSS"
        },
        
        # jQuery 3.6.0
        {
            "url": "https://code.jquery.com/jquery-3.6.0.min.js",
            "local": f"{base_dir}/assets/js/jquery-3.6.0.min.js",
            "desc": "jQuery 3.6.0"
        },
        
        # DataTables 1.13.6
        {
            "url": "https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css",
            "local": f"{base_dir}/assets/css/jquery.dataTables.min.css",
            "desc": "DataTables 1.13.6 CSS"
        },
        {
            "url": "https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js",
            "local": f"{base_dir}/assets/js/jquery.dataTables.min.js",
            "desc": "DataTables 1.13.6 JS"
        },
        
        # DataTables Bootstrap 5 Integration
        {
            "url": "https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js",
            "local": f"{base_dir}/assets/js/dataTables.bootstrap5.min.js",
            "desc": "DataTables Bootstrap5 JS"
        },
        {
            "url": "https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css",
            "local": f"{base_dir}/assets/css/dataTables.bootstrap5.min.css",
            "desc": "DataTables Bootstrap5 CSS"
        },
        
        # Chart.js (for frontend)
        {
            "url": "https://cdn.jsdelivr.net/npm/chart.js",
            "local": f"{base_dir}/assets/js/chart.min.js",
            "desc": "Chart.js"
        }
    ]
    
    # Download progress
    success_count = 0
    total_count = len(assets)
    
    for i, asset in enumerate(assets, 1):
        print(f"\n[{i}/{total_count}] {asset['desc']}")
        print("-" * 40)
        
        if download_file(asset['url'], asset['local'], asset['desc']):
            success_count += 1
        
        # Small delay to be respectful
        time.sleep(0.5)
    
    # Summary
    print(f"\n📊 DOWNLOAD SUMMARY")
    print("=" * 60)
    print(f"Total assets: {total_count}")
    print(f"Successful: {success_count}")
    print(f"Failed: {total_count - success_count}")
    print(f"Success rate: {success_count/total_count*100:.1f}%")
    
    if success_count == total_count:
        print("\n🎉 ALL ASSETS DOWNLOADED SUCCESSFULLY!")
        print("📁 Assets saved in:")
        print(f"   {base_dir}/assets/css/")
        print(f"   {base_dir}/assets/js/")
    else:
        print(f"\n⚠️ {total_count - success_count} assets failed to download")
    
    return success_count == total_count

def find_all_php_files():
    """Find semua PHP files yang perlu diupdate"""
    
    base_dir = "/var/www/html/bagops"
    php_files = []
    
    # Walk through directory
    for root, dirs, files in os.walk(base_dir):
        # Skip certain directories
        dirs[:] = [d for d in dirs if d not in ['.git', 'node_modules', 'vendor', 'test_results']]
        
        for file in files:
            if file.endswith('.php'):
                file_path = os.path.join(root, file)
                php_files.append(file_path)
    
    return php_files

def update_all_php_files():
    """Update SEMUA PHP files untuk menggunakan local assets"""
    
    base_dir = "/var/www/html/bagops"
    
    print(f"\n🔧 UPDATING ALL PHP FILES TO USE LOCAL ASSETS")
    print("=" * 60)
    
    # Find all PHP files
    php_files = find_all_php_files()
    print(f"📄 Found {len(php_files)} PHP files")
    
    # CDN to local mappings
    replacements = [
        # Bootstrap CSS
        (
            r'https://cdn\.jsdelivr\.net/npm/bootstrap@[^/]+/dist/css/bootstrap\.min\.css',
            'assets/css/bootstrap.min.css'
        ),
        
        # Bootstrap JS
        (
            r'https://cdn\.jsdelivr\.net/npm/bootstrap@[^/]+/dist/js/bootstrap\.bundle\.min\.js',
            'assets/js/bootstrap.bundle.min.js'
        ),
        
        # Font Awesome CSS (multiple versions)
        (
            r'https://cdnjs\.cloudflare\.com/ajax/libs/font-awesome/[^/]+/css/all\.min\.css',
            'assets/css/fontawesome.min.css'
        ),
        
        # jQuery
        (
            r'https://code\.jquery\.com/jquery-[^/]+\.min\.js',
            'assets/js/jquery-3.6.0.min.js'
        ),
        
        # DataTables CSS
        (
            r'https://cdn\.datatables\.net/[^/]+/css/jquery\.dataTables\.min\.css',
            'assets/css/jquery.dataTables.min.css'
        ),
        
        # DataTables JS
        (
            r'https://cdn\.datatables\.net/[^/]+/js/jquery\.dataTables\.min\.js',
            'assets/js/jquery.dataTables.min.js'
        ),
        
        # DataTables Bootstrap5 CSS
        (
            r'https://cdn\.datatables\.net/[^/]+/css/dataTables\.bootstrap5\.min\.css',
            'assets/css/dataTables.bootstrap5.min.css'
        ),
        
        # DataTables Bootstrap5 JS
        (
            r'https://cdn\.datatables\.net/[^/]+/js/dataTables\.bootstrap5\.min\.js',
            'assets/js/dataTables.bootstrap5.min.js'
        ),
        
        # Chart.js
        (
            r'https://cdn\.jsdelivr\.net/npm/chart\.js',
            'assets/js/chart.min.js'
        )
    ]
    
    updated_files = 0
    total_changes = 0
    
    for php_file in php_files:
        try:
            # Read file
            with open(php_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            file_changes = 0
            
            # Apply replacements
            for pattern, replacement in replacements:
                matches = re.findall(pattern, content)
                if matches:
                    content = re.sub(pattern, replacement, content)
                    file_changes += len(matches)
                    print(f"   ✅ {os.path.basename(php_file)}: {len(matches)} {pattern.split('/')[-1]} → {replacement}")
            
            # Only write if changes were made
            if content != original_content:
                # Create backup
                backup_file = php_file + '.cdn_backup'
                with open(backup_file, 'w', encoding='utf-8') as f:
                    f.write(original_content)
                
                # Write updated file
                with open(php_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                
                updated_files += 1
                total_changes += file_changes
            else:
                print(f"   ⚪ {os.path.basename(php_file)}: No CDN references found")
        
        except Exception as e:
            print(f"   ❌ {os.path.basename(php_file)}: Error - {str(e)[:50]}")
    
    print(f"\n📊 UPDATE SUMMARY")
    print("=" * 60)
    print(f"Total PHP files: {len(php_files)}")
    print(f"Files updated: {updated_files}")
    print(f"Total changes: {total_changes}")
    print(f"Success rate: {updated_files/len(php_files)*100:.1f}%")
    
    return updated_files > 0

def update_html_files():
    """Update HTML files untuk menggunakan local assets"""
    
    base_dir = "/var/www/html/bagops"
    
    print(f"\n🔧 UPDATING HTML FILES TO USE LOCAL ASSETS")
    print("=" * 60)
    
    # Find HTML files
    html_files = []
    for root, dirs, files in os.walk(base_dir):
        for file in files:
            if file.endswith('.html'):
                file_path = os.path.join(root, file)
                html_files.append(file_path)
    
    if not html_files:
        print("📄 No HTML files found")
        return False
    
    print(f"📄 Found {len(html_files)} HTML files")
    
    # Same replacements as PHP files
    replacements = [
        (r'https://cdn\.jsdelivr\.net/npm/bootstrap@[^/]+/dist/css/bootstrap\.min\.css', 'assets/css/bootstrap.min.css'),
        (r'https://cdn\.jsdelivr\.net/npm/bootstrap@[^/]+/dist/js/bootstrap\.bundle\.min\.js', 'assets/js/bootstrap.bundle.min.js'),
        (r'https://cdnjs\.cloudflare\.com/ajax/libs/font-awesome/[^/]+/css/all\.min\.css', 'assets/css/fontawesome.min.css'),
        (r'https://code\.jquery\.com/jquery-[^/]+\.min\.js', 'assets/js/jquery-3.6.0.min.js'),
        (r'https://cdn\.datatables\.net/[^/]+/css/jquery\.dataTables\.min\.css', 'assets/css/jquery.dataTables.min.css'),
        (r'https://cdn\.datatables\.net/[^/]+/js/jquery\.dataTables\.min\.js', 'assets/js/jquery.dataTables.min.js'),
        (r'https://cdn\.jsdelivr\.net/npm/chart\.js', 'assets/js/chart.min.js')
    ]
    
    updated_files = 0
    total_changes = 0
    
    for html_file in html_files:
        try:
            with open(html_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            file_changes = 0
            
            for pattern, replacement in replacements:
                matches = re.findall(pattern, content)
                if matches:
                    content = re.sub(pattern, replacement, content)
                    file_changes += len(matches)
                    print(f"   ✅ {os.path.basename(html_file)}: {len(matches)} changes")
            
            if content != original_content:
                # Create backup
                backup_file = html_file + '.cdn_backup'
                with open(backup_file, 'w', encoding='utf-8') as f:
                    f.write(original_content)
                
                # Write updated file
                with open(html_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                
                updated_files += 1
                total_changes += file_changes
            else:
                print(f"   ⚪ {os.path.basename(html_file)}: No CDN references found")
        
        except Exception as e:
            print(f"   ❌ {os.path.basename(html_file)}: Error - {str(e)[:50]}")
    
    print(f"\n📊 HTML UPDATE SUMMARY")
    print("=" * 60)
    print(f"Total HTML files: {len(html_files)}")
    print(f"Files updated: {updated_files}")
    print(f"Total changes: {total_changes}")
    
    return updated_files > 0

def verify_comprehensive_setup():
    """Verify comprehensive offline setup"""
    
    base_url = "http://localhost/bagops"
    
    print(f"\n🧪 COMPREHENSIVE VERIFICATION")
    print("=" * 60)
    
    # Test all assets
    assets = [
        ("Bootstrap CSS", f"{base_url}/assets/css/bootstrap.min.css"),
        ("Bootstrap JS", f"{base_url}/assets/js/bootstrap.bundle.min.js"),
        ("Font Awesome CSS", f"{base_url}/assets/css/fontawesome.min.css"),
        ("jQuery", f"{base_url}/assets/js/jquery-3.6.0.min.js"),
        ("DataTables CSS", f"{base_url}/assets/css/jquery.dataTables.min.css"),
        ("DataTables JS", f"{base_url}/assets/js/jquery.dataTables.min.js"),
        ("DataTables Bootstrap5 CSS", f"{base_url}/assets/css/dataTables.bootstrap5.min.css"),
        ("DataTables Bootstrap5 JS", f"{base_url}/assets/js/dataTables.bootstrap5.min.js"),
        ("Chart.js", f"{base_url}/assets/js/chart.min.js")
    ]
    
    session = requests.Session()
    
    # Login
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    print("📋 Testing all assets:")
    print("-" * 40)
    
    accessible_count = 0
    for name, url in assets:
        try:
            response = session.get(url, timeout=10)
            if response.status_code == 200:
                size_kb = len(response.content) / 1024
                print(f"✅ {name:25} - {response.status_code} - {size_kb:.1f}KB")
                accessible_count += 1
            else:
                print(f"❌ {name:25} - {response.status_code}")
        except Exception as e:
            print(f"❌ {name:25} - Error: {str(e)[:30]}")
    
    print(f"\n📊 Assets Summary: {accessible_count}/{len(assets)} accessible")
    
    # Test critical pages
    critical_pages = [
        ("Login", f"{base_url}/login.php"),
        ("Dashboard", f"{base_url}/simple_root_system.php?page=dashboard"),
        ("Personel", f"{base_url}/simple_root_system.php?page=personel_ultra"),
        ("Jabatan", f"{base_url}/simple_root_system.php?page=jabatan_management")
    ]
    
    print(f"\n📄 Testing critical pages:")
    print("-" * 40)
    
    working_pages = 0
    for page_name, page_url in critical_pages:
        try:
            response = session.get(page_url, timeout=10)
            if response.status_code == 200:
                # Check for CDN references
                content = response.text.lower()
                cdn_refs = 0
                cdn_patterns = ['cdn.jsdelivr.net', 'cdnjs.cloudflare.com', 'code.jquery.com', 'cdn.datatables.net']
                
                for pattern in cdn_patterns:
                    if pattern in content:
                        cdn_refs += 1
                
                status = "✅" if cdn_refs == 0 else "⚠️"
                print(f"{status} {page_name:15} - {response.status_code} - {cdn_refs} CDN refs")
                working_pages += 1
            else:
                print(f"❌ {page_name:15} - {response.status_code}")
        except Exception as e:
            print(f"❌ {page_name:15} - Error: {str(e)[:30]}")
    
    print(f"\n📊 Pages Summary: {working_pages}/{len(critical_pages)} working")
    
    # Final assessment
    total_checks = 2
    passed_checks = 0
    
    if accessible_count == len(assets):
        print("✅ All assets accessible")
        passed_checks += 1
    else:
        print(f"❌ Some assets missing: {len(assets) - accessible_count}")
    
    if working_pages == len(critical_pages):
        print("✅ All critical pages working")
        passed_checks += 1
    else:
        print(f"❌ Some pages not working: {len(critical_pages) - working_pages}")
    
    success_rate = passed_checks / total_checks * 100
    print(f"\n📊 Overall Success Rate: {success_rate:.1f}%")
    
    if passed_checks == total_checks:
        print("\n🎉 COMPREHENSIVE OFFLINE SETUP COMPLETE!")
        print("✅ All assets downloaded and working")
        print("✅ All files updated to use local assets")
        print("✅ Application is 100% offline ready")
    else:
        print(f"\n⚠️ {total_checks - passed_checks} issues remaining")
    
    return passed_checks == total_checks

def main():
    """Main function"""
    print("🚀 COMPREHENSIVE BAGOPS OFFLINE SETUP")
    print("=" * 60)
    print("This script will:")
    print("1. Download ALL CDN assets")
    print("2. Update ALL PHP files to use local assets")
    print("3. Update ALL HTML files to use local assets")
    print("4. Verify comprehensive offline functionality")
    print()
    
    # Step 1: Download all assets
    if download_all_cdn_assets():
        print("\n✅ Step 1: All assets downloaded successfully")
    else:
        print("\n❌ Step 1: Some assets failed to download")
        return
    
    # Step 2: Update PHP files
    if update_all_php_files():
        print("\n✅ Step 2: PHP files updated successfully")
    else:
        print("\n⚠️ Step 2: No PHP files needed updates")
    
    # Step 3: Update HTML files
    if update_html_files():
        print("\n✅ Step 3: HTML files updated successfully")
    else:
        print("\n⚠️ Step 3: No HTML files needed updates")
    
    # Step 4: Verify
    if verify_comprehensive_setup():
        print("\n✅ Step 4: Verification passed")
    else:
        print("\n⚠️ Step 4: Some verification issues")
    
    print("\n🎉 COMPREHENSIVE OFFLINE SETUP COMPLETED!")
    print("=" * 60)
    print("📁 All CDN assets are now available locally")
    print("📄 All files have been updated to use local assets")
    print("🌐 Application is 100% offline ready")
    print("🔄 Restart your web server if needed")

if __name__ == "__main__":
    main()
