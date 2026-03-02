#!/usr/bin/env python3
"""
Enable PHP error reporting for development
Author: BAGOPS System
Date: 2026-03-02
"""

import os

def enable_php_error_reporting():
    """Enable PHP error reporting for development"""
    
    base_dir = "/var/www/html/bagops"
    
    print("🔧 ENABLING PHP ERROR REPORTING")
    print("=" * 50)
    
    # Files to update with error reporting
    files_to_update = [
        "login.php",
        "simple_root_system.php", 
        "layouts/simple_layout.php"
    ]
    
    error_reporting_code = """<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>"""
    
    updated_files = 0
    
    for file_path in files_to_update:
        full_path = f"{base_dir}/{file_path}"
        
        if os.path.exists(full_path):
            try:
                # Read current file
                with open(full_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Check if error reporting already exists
                if 'error_reporting(E_ALL)' in content:
                    print(f"⚪ {file_path}: Error reporting already enabled")
                    continue
                
                # Find the first <?php tag
                php_start = content.find('<?php')
                if php_start == -1:
                    print(f"❌ {file_path}: No PHP opening tag found")
                    continue
                
                # Insert error reporting after <?php
                insert_pos = content.find('\n', php_start) + 1
                if insert_pos == 0:
                    insert_pos = len('<?php')
                
                # Create backup
                backup_path = f"{full_path}.error_reporting_backup"
                with open(backup_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                
                # Insert error reporting code
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
                
                print(f"✅ {file_path}: Error reporting enabled")
                print(f"   Backup created: {backup_path}")
                updated_files += 1
                
            except Exception as e:
                print(f"❌ {file_path}: Error - {str(e)}")
        else:
            print(f"❌ {file_path}: File not found")
    
    # Create .htaccess file for error reporting
    print(f"\n📁 CREATING .HTACCESS FOR ERROR REPORTING")
    print("-" * 40)
    
    htaccess_path = f"{base_dir}/.htaccess"
    
    htaccess_content = """# Enable PHP error reporting for development
php_flag display_errors on
php_value error_reporting E_ALL
php_flag display_startup_errors on

# Directory indexing
Options -Indexes

# Default character set
AddDefaultCharset UTF-8

# URL rewriting
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
"""
    
    try:
        # Check if .htaccess exists
        if os.path.exists(htaccess_path):
            # Read existing content
            with open(htaccess_path, 'r', encoding='utf-8') as f:
                existing_content = f.read()
            
            # Check if PHP error settings already exist
            if 'php_flag display_errors' in existing_content:
                print("⚪ .htaccess: PHP error reporting already configured")
            else:
                # Create backup
                backup_path = f"{htaccess_path}.backup"
                with open(backup_path, 'w', encoding='utf-8') as f:
                    f.write(existing_content)
                
                # Add PHP error reporting to existing .htaccess
                new_content = htaccess_content + "\n\n" + existing_content
                
                with open(htaccess_path, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                
                print(f"✅ .htaccess: PHP error reporting added")
                print(f"   Backup created: {backup_path}")
        else:
            # Create new .htaccess
            with open(htaccess_path, 'w', encoding='utf-8') as f:
                f.write(htaccess_content)
            
            print(f"✅ .htaccess: Created with PHP error reporting")
        
        updated_files += 1
        
    except Exception as e:
        print(f"❌ .htaccess: Error - {str(e)}")
    
    # Create test file to verify error reporting
    print(f"\n📄 CREATING ERROR REPORTING TEST FILE")
    print("-" * 40)
    
    test_file_path = f"{base_dir}/test_error_reporting.php"
    
    test_content = """<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>PHP Error Reporting Test</h1>";
echo "<h2>Current Error Reporting Settings:</h2>";

echo "<table border='1'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>error_reporting()</td><td>" . error_reporting() . "</td></tr>";
echo "<tr><td>display_errors</td><td>" . ini_get('display_errors') . "</td></tr>";
echo "<tr><td>display_startup_errors</td><td>" . ini_get('display_startup_errors') . "</td></tr>";
echo "<tr><td>log_errors</td><td>" . ini_get('log_errors') . "</td></tr>";
echo "</table>";

echo "<h2>Testing Error Types:</h2>";

// This should trigger a Notice
echo "<h3>Notice Test:</h3>";
$undefined_var = $undefined_var;

// This should trigger a Warning
echo "<h3>Warning Test:</h3>";
$file_handle = fopen('nonexistent_file.txt', 'r');

// This should trigger a Deprecated warning (if PHP version supports it)
echo "<h3>Deprecated Test:</h3>";
$deprecated_function = split(':', 'test:string');

echo "<h2>Complete!</h2>";
echo "<p>If you see error messages above, error reporting is working.</p>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>"""
    
    try:
        with open(test_file_path, 'w', encoding='utf-8') as f:
            f.write(test_content)
        
        print(f"✅ Test file created: test_error_reporting.php")
        print(f"   Access at: http://localhost/bagops/test_error_reporting.php")
        updated_files += 1
        
    except Exception as e:
        print(f"❌ Test file: Error - {str(e)}")
    
    # Summary
    print(f"\n📊 SUMMARY")
    print("=" * 50)
    print(f"Files updated: {updated_files}")
    print(f"Error reporting enabled in:")
    
    for file_path in files_to_update:
        full_path = f"{base_dir}/{file_path}"
        if os.path.exists(full_path):
            print(f"   ✅ {file_path}")
    
    print(f"   ✅ .htaccess")
    print(f"   ✅ test_error_reporting.php")
    
    print(f"\n🔧 INSTRUCTIONS")
    print("=" * 50)
    print("1. Restart your web server to apply .htaccess changes")
    print("2. Visit: http://localhost/bagops/test_error_reporting.php")
    print("3. You should see error messages for undefined variables")
    print("4. If you see errors, error reporting is working")
    print("5. For production, disable error reporting:")
    print("   - Set display_errors = 0")
    print("   - Set error_reporting = E_ALL & ~E_NOTICE & ~E_WARNING")
    
    print(f"\n✅ PHP ERROR REPORTING SETUP COMPLETED!")

if __name__ == "__main__":
    enable_php_error_reporting()
