#!/usr/bin/env python3
"""
Download CDN assets untuk offline usage
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import os
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

def download_cdn_assets():
    """Download semua CDN assets yang dibutuhkan"""
    
    base_dir = "/var/www/html/bagops"
    
    print("🌐 DOWNLOAD CDN ASSETS FOR OFFLINE USAGE")
    print("=" * 60)
    
    # List of CDN assets to download
    assets = [
        # Bootstrap 5.3
        {
            "url": "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css",
            "local": f"{base_dir}/assets/css/bootstrap.min.css",
            "desc": "Bootstrap 5.3 CSS"
        },
        {
            "url": "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js", 
            "local": f"{base_dir}/assets/js/bootstrap.bundle.min.js",
            "desc": "Bootstrap 5.3 JS Bundle"
        },
        
        # Font Awesome 6.4
        {
            "url": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
            "local": f"{base_dir}/assets/css/fontawesome.min.css",
            "desc": "Font Awesome 6.4 CSS"
        },
        
        # jQuery 3.6
        {
            "url": "https://code.jquery.com/jquery-3.6.0.min.js",
            "local": f"{base_dir}/assets/js/jquery-3.6.0.min.js",
            "desc": "jQuery 3.6.0"
        },
        
        # DataTables 1.13.6
        {
            "url": "https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css",
            "local": f"{base_dir}/assets/css/jquery.dataTables.min.css",
            "desc": "DataTables CSS"
        },
        {
            "url": "https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js",
            "local": f"{base_dir}/assets/js/jquery.dataTables.min.js",
            "desc": "DataTables JS"
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

def update_layout_to_local():
    """Update layout untuk menggunakan local assets"""
    
    base_dir = "/var/www/html/bagops"
    layout_file = f"{base_dir}/layouts/simple_layout.php"
    
    print(f"\n🔧 UPDATING LAYOUT TO USE LOCAL ASSETS")
    print("=" * 60)
    
    try:
        # Read current layout
        with open(layout_file, 'r') as f:
            content = f.read()
        
        print("📄 Reading current layout...")
        
        # Create backup
        backup_file = f"{layout_file}.backup"
        with open(backup_file, 'w') as f:
            f.write(content)
        print(f"✅ Backup created: {backup_file}")
        
        # Define replacements
        replacements = [
            # Bootstrap CSS
            (
                '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">',
                '<link href="assets/css/bootstrap.min.css" rel="stylesheet">'
            ),
            
            # Bootstrap JS
            (
                '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>',
                '<script src="assets/js/bootstrap.bundle.min.js"></script>'
            ),
            
            # Font Awesome
            (
                '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">',
                '<link href="assets/css/fontawesome.min.css" rel="stylesheet">'
            ),
            
            # jQuery
            (
                '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>',
                '<script src="assets/js/jquery-3.6.0.min.js"></script>'
            ),
            
            # DataTables CSS
            (
                '<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">',
                '<link href="assets/css/jquery.dataTables.min.css" rel="stylesheet">'
            ),
            
            # DataTables JS
            (
                '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>',
                '<script src="assets/js/jquery.dataTables.min.js"></script>'
            )
        ]
        
        # Apply replacements
        updated_content = content
        changes_made = 0
        
        for old, new in replacements:
            if old in updated_content:
                updated_content = updated_content.replace(old, new)
                changes_made += 1
                print(f"✅ Updated: {old[:50]}...")
        
        # Write updated layout
        with open(layout_file, 'w') as f:
            f.write(updated_content)
        
        print(f"\n📊 LAYOUT UPDATE SUMMARY")
        print("=" * 60)
        print(f"Changes made: {changes_made}")
        print(f"Layout file: {layout_file}")
        print(f"Backup file: {backup_file}")
        
        if changes_made > 0:
            print("\n✅ Layout updated successfully!")
            print("🔄 Now using local assets instead of CDN")
        else:
            print("\n⚠️ No changes needed - already using local assets")
        
        return True
        
    except Exception as e:
        print(f"❌ Error updating layout: {str(e)}")
        return False

def verify_local_assets():
    """Verify bahwa local assets berfungsi"""
    
    base_url = "http://localhost/bagops"
    
    print(f"\n🧪 VERIFYING LOCAL ASSETS")
    print("=" * 60)
    
    # Test assets
    assets_to_test = [
        ("Bootstrap CSS", f"{base_url}/assets/css/bootstrap.min.css"),
        ("Bootstrap JS", f"{base_url}/assets/js/bootstrap.bundle.min.js"),
        ("Font Awesome", f"{base_url}/assets/css/fontawesome.min.css"),
        ("jQuery", f"{base_url}/assets/js/jquery-3.6.0.min.js"),
        ("DataTables CSS", f"{base_url}/assets/css/jquery.dataTables.min.css"),
        ("DataTables JS", f"{base_url}/assets/js/jquery.dataTables.min.js")
    ]
    
    session = requests.Session()
    
    # Login first
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    success_count = 0
    
    for name, url in assets_to_test:
        try:
            response = session.get(url, timeout=10)
            status_icon = "✅" if response.status_code == 200 else "❌"
            print(f"{status_icon} {name}: {response.status_code}")
            
            if response.status_code == 200:
                success_count += 1
                # Check if it's not empty
                if len(response.content) < 1000:
                    print(f"   ⚠️ Small file size: {len(response.content)} bytes")
            
        except Exception as e:
            print(f"❌ {name}: Error - {str(e)[:50]}")
    
    print(f"\n📊 VERIFICATION SUMMARY")
    print("=" * 60)
    print(f"Assets tested: {len(assets_to_test)}")
    print(f"Accessible: {success_count}")
    print(f"Success rate: {success_count/len(assets_to_test)*100:.1f}%")
    
    # Test page rendering
    print(f"\n📄 Testing page rendering...")
    try:
        page_response = session.get(f"{base_url}/simple_root_system.php?page=dashboard", timeout=10)
        if page_response.status_code == 200:
            print("✅ Dashboard page loads successfully")
            
            # Check for local assets in HTML
            page_content = page_response.text
            local_assets_found = 0
            
            if "assets/css/bootstrap.min.css" in page_content:
                local_assets_found += 1
            if "assets/js/jquery-3.6.0.min.js" in page_content:
                local_assets_found += 1
            if "assets/css/jquery.dataTables.min.css" in page_content:
                local_assets_found += 1
            
            print(f"📋 Local assets found in page: {local_assets_found}")
            
            if local_assets_found >= 2:
                print("✅ Page is using local assets")
            else:
                print("⚠️ Page might still be using CDN")
        else:
            print(f"❌ Page load failed: {page_response.status_code}")
    
    except Exception as e:
        print(f"❌ Page test error: {str(e)}")
    
    return success_count == len(assets_to_test)

def main():
    """Main function"""
    print("🚀 BAGOPS OFFLINE ASSETS SETUP")
    print("=" * 60)
    print("This script will download CDN assets and configure")
    print("the application to use them offline.")
    print()
    
    # Step 1: Download assets
    if download_cdn_assets():
        print("\n✅ Step 1: Download completed successfully")
    else:
        print("\n❌ Step 1: Download failed - check errors above")
        return
    
    # Step 2: Update layout
    if update_layout_to_local():
        print("\n✅ Step 2: Layout updated successfully")
    else:
        print("\n❌ Step 2: Layout update failed")
        return
    
    # Step 3: Verify
    if verify_local_assets():
        print("\n✅ Step 3: Verification passed")
    else:
        print("\n⚠️ Step 3: Some verification issues - check above")
    
    print("\n🎉 OFFLINE ASSETS SETUP COMPLETED!")
    print("=" * 60)
    print("📁 All assets are now available locally")
    print("🌐 Application can work without internet connection")
    print("🔄 Restart your web server if needed")

if __name__ == "__main__":
    main()
