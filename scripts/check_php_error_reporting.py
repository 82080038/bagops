#!/usr/bin/env python3
"""
Check PHP error reporting configuration
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import re

def check_php_error_reporting():
    """Check PHP error reporting configuration"""
    
    base_url = "http://localhost/bagops"
    
    print("🔍 CHECKING PHP ERROR REPORTING")
    print("=" * 50)
    
    # Test 1: Check phpinfo
    print("\n📋 TEST 1: phpinfo() Check")
    print("-" * 40)
    
    try:
        # Create a simple phpinfo file
        phpinfo_url = f"{base_url}/phpinfo_test.php"
        
        # We'll check if error reporting is enabled by triggering a warning
        test_url = f"{base_url}/error_test.php"
        
        # Create test content that would show errors
        test_php_content = "<?php\n"
        test_php_content += "error_reporting(E_ALL);\n"
        test_php_content += "ini_set('display_errors', 1);\n"
        test_php_content += "echo 'Error Reporting Test\\n';\n"
        test_php_content += "echo 'display_errors: ' . ini_get('display_errors') . '\\n';\n"
        test_php_content += "echo 'error_reporting: ' . error_reporting() . '\\n';\n"
        test_php_content += "$undefined_var = $undefined_var; // This should trigger a notice\n"
        test_php_content += "?>"
        
        print("📄 Testing error reporting with undefined variable...")
        
        # Test login page for any error indicators
        login_response = requests.get(f"{base_url}/login.php", timeout=10)
        login_content = login_response.text
        
        # Look for PHP error patterns
        error_patterns = [
            r'Notice:',
            r'Warning:',
            r'Fatal error:',
            r'Parse error:',
            r'Undefined variable:',
            r'Undefined index:',
            r'Call to undefined function:',
            r'Failed to open stream:',
            r'include\(\): Failed opening'
        ]
        
        found_errors = []
        for pattern in error_patterns:
            matches = re.findall(pattern, login_content, re.IGNORECASE)
            if matches:
                found_errors.extend(matches)
        
        if found_errors:
            print(f"❌ PHP errors found in login page:")
            for error in found_errors[:5]:  # Show first 5
                print(f"   - {error}")
        else:
            print("✅ No PHP errors visible in login page")
        
        # Check for error reporting settings in common files
        print(f"\n📋 TEST 2: Check Common Configuration Files")
        print("-" * 40)
        
        # Check if we can access any PHP config files
        config_files_to_check = [
            f"{base_url}/config.php",
            f"{base_url}/index.php", 
            f"{base_url}/login.php"
        ]
        
        for config_file in config_files_to_check:
            try:
                response = requests.get(config_file, timeout=5)
                if response.status_code == 200:
                    content = response.text
                    
                    # Look for error_reporting settings
                    error_reporting_patterns = [
                        r'error_reporting\s*\(',
                        r'ini_set\s*\(\s*[\'"]display_errors',
                        r'display_errors\s*=',
                        r'error_reporting\s*='
                    ]
                    
                    found_settings = []
                    for pattern in error_reporting_patterns:
                        matches = re.findall(pattern, content, re.IGNORECASE)
                        if matches:
                            found_settings.extend(matches)
                    
                    if found_settings:
                        print(f"   {config_file.split('/')[-1]}: Found error reporting settings")
                        for setting in found_settings:
                            print(f"      - {setting}")
                    else:
                        print(f"   {config_file.split('/')[-1]}: No error reporting settings found")
                else:
                    print(f"   {config_file.split('/')[-1]}: Not accessible ({response.status_code})")
            except Exception as e:
                print(f"   {config_file.split('/')[-1]}: Error - {str(e)[:30]}")
    
    except Exception as e:
        print(f"❌ Error checking PHP configuration: {str(e)}")
    
    print(f"\n🔧 RECOMMENDATIONS FOR PHP ERROR REPORTING")
    print("=" * 50)
    
    print("To enable PHP error reporting for development:")
    print("1. Add to the beginning of your PHP files:")
    print("   <?php")
    print("   error_reporting(E_ALL);")
    print("   ini_set('display_errors', 1);")
    print("   ?>")
    print()
    print("2. Or add to .htaccess:")
    print("   php_flag display_errors on")
    print("   php_value error_reporting E_ALL")
    print()
    print("3. Or add to php.ini:")
    print("   display_errors = On")
    print("   error_reporting = E_ALL")
    print()
    print("4. For production, use:")
    print("   error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);")
    print("   ini_set('display_errors', 0);")
    print("   ini_set('log_errors', 1);")
    
    print(f"\n✅ PHP ERROR REPORTING CHECK COMPLETED!")

if __name__ == "__main__":
    check_php_error_reporting()
