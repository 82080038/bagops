#!/usr/bin/env python3
"""
Check critical PHP files for error reporting
Author: BAGOPS System
Date: 2026-03-02
"""

import os
import re

def check_critical_php_files():
    """Check critical PHP files for error reporting"""
    
    base_dir = "/var/www/html/bagops"
    
    print("🔍 CHECKING CRITICAL PHP FILES FOR ERROR REPORTING")
    print("=" * 60)
    
    # Critical files that should have error reporting
    critical_files = [
        "logout.php",
        "register.php", 
        "config/database.php",
        "config/config.php",
        "classes/Auth.php",
        "classes/Database.php"
    ]
    
    # AJAX files that handle important operations
    ajax_files = [
        "ajax/get_personel.php",
        "ajax/login.php",
        "ajax/logout.php",
        "ajax/save_user.php",
        "ajax/delete_user.php"
    ]
    
    files_to_check = critical_files + ajax_files
    
    files_with_error_reporting = []
    files_without_error_reporting = []
    files_not_found = []
    
    for file_path in files_to_check:
        full_path = f"{base_dir}/{file_path}"
        
        if os.path.exists(full_path):
            try:
                with open(full_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Check for error reporting
                has_error_reporting = (
                    'error_reporting(E_ALL)' in content or
                    'ini_set(\'display_errors\'' in content or
                    'ini_set("display_errors"' in content
                )
                
                if has_error_reporting:
                    files_with_error_reporting.append(file_path)
                else:
                    files_without_error_reporting.append(file_path)
                
            except Exception as e:
                print(f"❌ Error reading {file_path}: {str(e)}")
        else:
            files_not_found.append(file_path)
    
    # Results
    print(f"\n📊 RESULTS SUMMARY")
    print("=" * 60)
    print(f"Total files checked: {len(files_to_check)}")
    print(f"Files with error reporting: {len(files_with_error_reporting)}")
    print(f"Files without error reporting: {len(files_without_error_reporting)}")
    print(f"Files not found: {len(files_not_found)}")
    
    # Show files with error reporting
    if files_with_error_reporting:
        print(f"\n✅ FILES WITH ERROR REPORTING ({len(files_with_error_reporting)}):")
        for file_path in files_with_error_reporting:
            print(f"   ✅ {file_path}")
    
    # Show files without error reporting
    if files_without_error_reporting:
        print(f"\n⚠️ FILES WITHOUT ERROR REPORTING ({len(files_without_error_reporting)}):")
        for file_path in files_without_error_reporting:
            print(f"   ⚠️ {file_path}")
    
    # Show files not found
    if files_not_found:
        print(f"\n❌ FILES NOT FOUND ({len(files_not_found)}):")
        for file_path in files_not_found:
            print(f"   ❌ {file_path}")
    
    # Add error reporting to critical files
    if files_without_error_reporting:
        print(f"\n🔧 ADDING ERROR REPORTING TO CRITICAL FILES")
        print("=" * 60)
        
        error_reporting_code = """<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
"""
        
        updated_count = 0
        
        for file_path in files_without_error_reporting:
            full_path = f"{base_dir}/{file_path}"
            
            try:
                # Read current file
                with open(full_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Find the first <?php tag
                php_start = content.find('<?php')
                if php_start == -1:
                    print(f"   ❌ {file_path}: No PHP opening tag found")
                    continue
                
                # Create backup
                backup_path = f"{full_path}.error_reporting_backup"
                with open(backup_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                
                # Insert error reporting after <?php
                insert_pos = content.find('\n', php_start) + 1
                if insert_pos == 0:
                    insert_pos = len('<?php')
                
                error_code = """
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
"""
                
                new_content = content[:insert_pos] + error_code + content[insert_pos:]
                
                # Write updated file
                with open(full_path, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                
                print(f"   ✅ {file_path}: Error reporting added")
                print(f"      Backup: {backup_path}")
                updated_count += 1
                
            except Exception as e:
                print(f"   ❌ {file_path}: Error - {str(e)}")
        
        print(f"\n✅ Updated {updated_count} files with error reporting")
    
    # Final summary
    print(f"\n🎯 FINAL STATUS")
    print("=" * 60)
    
    total_after = len(files_with_error_reporting) + len(files_without_error_reporting)
    
    if len(files_without_error_reporting) == 0:
        print("🎉 ALL CRITICAL PHP FILES HAVE ERROR REPORTING!")
    else:
        print(f"⚠️ {len(files_without_error_reporting)} files still need error reporting")
    
    print(f"\n📋 RECOMMENDATIONS:")
    print("1. Test logout functionality")
    print("2. Test AJAX endpoints")
    print("3. Check browser console for any PHP errors")
    print("4. Verify error reporting is working")
    
    print(f"\n✅ CRITICAL PHP FILES CHECK COMPLETED!")

if __name__ == "__main__":
    check_critical_php_files()
