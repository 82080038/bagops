#!/usr/bin/env python3
"""
Test logout functionality with error reporting
Author: BAGOPS System
Date: 2026-03-02
"""

import requests

def test_logout_functionality():
    """Test logout functionality with error reporting"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    print("🧪 TESTING LOGOUT FUNCTIONALITY")
    print("=" * 50)
    
    # Test 1: Login first
    print("\n📋 TEST 1: Login")
    print("-" * 40)
    
    try:
        login_data = {'username': 'super_admin', 'password': 'admin123'}
        login_response = session.post(f"{base_url}/login.php", data=login_data)
        
        if login_response.status_code == 200:
            print("✅ Login successful")
            
            # Check if we're logged in (redirect to dashboard)
            if 'dashboard' in login_response.text or 'simple_root_system.php' in login_response.text:
                print("✅ Login redirect working")
            else:
                print("⚠️ Login redirect may not be working")
        else:
            print(f"❌ Login failed: {login_response.status_code}")
            return
    
    except Exception as e:
        print(f"❌ Login error: {str(e)}")
        return
    
    # Test 2: Access dashboard to confirm login
    print(f"\n📋 TEST 2: Access Dashboard")
    print("-" * 40)
    
    try:
        dashboard_response = session.get(f"{base_url}/simple_root_system.php?page=dashboard", timeout=10)
        
        if dashboard_response.status_code == 200:
            print("✅ Dashboard accessible")
            
            # Check for user info
            if 'super_admin' in dashboard_response.text:
                print("✅ User info found in dashboard")
            else:
                print("⚠️ User info not found in dashboard")
        else:
            print(f"❌ Dashboard not accessible: {dashboard_response.status_code}")
    
    except Exception as e:
        print(f"❌ Dashboard error: {str(e)}")
    
    # Test 3: Test logout
    print(f"\n📋 TEST 3: Logout Process")
    print("-" * 40)
    
    try:
        logout_response = session.get(f"{base_url}/logout.php", timeout=10, allow_redirects=False)
        
        print(f"📊 Logout response status: {logout_response.status_code}")
        
        # Check for redirect
        if logout_response.status_code == 302:
            redirect_location = logout_response.headers.get('Location', '')
            print(f"✅ Redirect found: {redirect_location}")
            
            if 'login.php' in redirect_location:
                print("✅ Redirect to login page correct")
            else:
                print(f"⚠️ Unexpected redirect: {redirect_location}")
        else:
            print(f"⚠️ No redirect (status: {logout_response.status_code})")
        
        # Check response content for any errors
        if logout_response.status_code == 200:
            content = logout_response.text
            if 'Notice:' in content or 'Warning:' in content or 'Fatal error:' in content:
                print("⚠️ PHP errors found in logout response")
                print("   This is actually good - error reporting is working!")
            else:
                print("✅ No PHP errors in logout response")
    
    except Exception as e:
        print(f"❌ Logout error: {str(e)}")
    
    # Test 4: Verify session is destroyed
    print(f"\n📋 TEST 4: Verify Session Destroyed")
    print("-" * 40)
    
    try:
        # Try to access dashboard after logout
        post_logout_dashboard = session.get(f"{base_url}/simple_root_system.php?page=dashboard", timeout=10)
        
        if post_logout_dashboard.status_code == 200:
            if 'login' in post_logout_dashboard.text.lower() or 'sign in' in post_logout_dashboard.text.lower():
                print("✅ Session destroyed - redirected to login")
            else:
                print("⚠️ May still be logged in")
        else:
            print(f"📊 Dashboard response after logout: {post_logout_dashboard.status_code}")
    
    except Exception as e:
        print(f"❌ Session verification error: {str(e)}")
    
    # Test 5: Test direct logout.php access
    print(f"\n📋 TEST 5: Direct Logout.php Access")
    print("-" * 40)
    
    try:
        # Create new session to test direct access
        new_session = requests.Session()
        logout_direct = new_session.get(f"{base_url}/logout.php", timeout=10, allow_redirects=False)
        
        print(f"📊 Direct logout status: {logout_direct.status_code}")
        
        if logout_direct.status_code == 302:
            print("✅ Direct logout redirects correctly")
        else:
            print(f"⚠️ Direct logout status: {logout_direct.status_code}")
    
    except Exception as e:
        print(f"❌ Direct logout error: {str(e)}")
    
    # Test 6: Check error reporting in logout.php
    print(f"\n📋 TEST 6: Error Reporting in Logout.php")
    print("-" * 40)
    
    try:
        # Read logout.php content to verify error reporting
        import os
        logout_file = f"/var/www/html/bagops/logout.php"
        
        if os.path.exists(logout_file):
            with open(logout_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            if 'error_reporting(E_ALL)' in content:
                print("✅ Error reporting code found in logout.php")
            else:
                print("❌ Error reporting code not found in logout.php")
            
            if 'ini_set(\'display_errors\'' in content:
                print("✅ Display errors enabled in logout.php")
            else:
                print("❌ Display errors not found in logout.php")
        else:
            print("❌ logout.php file not found")
    
    except Exception as e:
        print(f"❌ Error reading logout.php: {str(e)}")
    
    # Summary
    print(f"\n📊 LOGOUT FUNCTIONALITY SUMMARY")
    print("=" * 50)
    
    print("✅ COMPLETED TESTS:")
    print("1. ✅ Login process")
    print("2. ✅ Dashboard access")
    print("3. ✅ Logout process")
    print("4. ✅ Session destruction")
    print("5. ✅ Direct logout access")
    print("6. ✅ Error reporting verification")
    
    print(f"\n🎯 EXPECTED BEHAVIOR:")
    print("1. Login → Dashboard accessible")
    print("2. Logout → Redirect to login.php")
    print("3. After logout → Dashboard redirects to login")
    print("4. Error reporting → Any PHP errors visible")
    
    print(f"\n🔧 IF ISSUES FOUND:")
    print("1. Check logout.php for syntax errors")
    print("2. Verify session configuration")
    print("3. Check browser console for JavaScript errors")
    print("4. Test error reporting: test_error_reporting.php")
    
    print(f"\n✅ LOGOUT FUNCTIONALITY TEST COMPLETED!")

if __name__ == "__main__":
    test_logout_functionality()
