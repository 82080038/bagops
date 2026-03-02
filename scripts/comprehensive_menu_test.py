#!/usr/bin/env python3
"""
Comprehensive Menu and Page Testing for BAGOPS Super Admin
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import json
import time
from urllib.parse import urljoin
from bs4 import BeautifulSoup
import sys

class BAGOPSComprehensiveTester:
    def __init__(self, base_url="http://localhost/bagops"):
        self.base_url = base_url
        self.session = requests.Session()
        self.test_results = []
        self.authenticated = False
        
    def login(self, username="super_admin", password="admin123"):
        """Login ke sistem BAGOPS"""
        try:
            login_url = urljoin(self.base_url, "login.php")
            
            print(f"🔐 Attempting login as {username}...")
            
            # Get login page first
            response = self.session.get(login_url)
            print(f"📄 Login page status: {response.status_code}")
            
            # Try different login methods
            login_data = {
                'username': username,
                'password': password
            }
            
            # Post login
            response = self.session.post(login_url, data=login_data, allow_redirects=False)
            print(f"📤 Login POST status: {response.status_code}")
            
            # Check redirects
            if response.status_code == 302:
                redirect_url = response.headers.get('Location', '')
                print(f"🔄 Redirected to: {redirect_url}")
                
                # Follow redirect
                response = self.session.get(urljoin(self.base_url, redirect_url))
                
            # Check if login successful
            if response.status_code == 200:
                if "dashboard" in response.text.lower() or "simple_root_system" in response.text:
                    self.authenticated = True
                    print(f"✅ Login successful as {username}")
                    return True
                else:
                    print(f"❌ Login failed - no dashboard found")
                    print(f"📄 Response preview: {response.text[:500]}")
                    return False
            else:
                print(f"❌ Login failed - status {response.status_code}")
                return False
                
        except Exception as e:
            print(f"❌ Login error: {str(e)}")
            return False
    
    def get_menu_from_database(self):
        """Get menu structure from database"""
        try:
            # Connect to database directly
            import mysql.connector
            
            conn = mysql.connector.connect(
                host='localhost',
                port=3306,
                user='root',
                password='rootpassword',
                database='bagops_db'
            )
            
            cursor = conn.cursor(dictionary=True)
            
            # Get all menu items
            cursor.execute("""
                SELECT id, name, icon, url, parent_id, order_index, is_active 
                FROM menu 
                WHERE is_active = 1 
                ORDER BY parent_id ASC, order_index ASC
            """)
            
            menus = cursor.fetchall()
            
            cursor.close()
            conn.close()
            
            print(f"📋 Found {len(menus)} menu items from database")
            return menus
            
        except Exception as e:
            print(f"❌ Database error: {str(e)}")
            return []
    
    def test_page_access(self, page_url, page_name):
        """Test access to a specific page"""
        try:
            full_url = urljoin(self.base_url, page_url)
            
            print(f"🔍 Testing: {page_name} -> {full_url}")
            
            # Test direct access
            response = self.session.get(full_url, allow_redirects=False)
            
            result = {
                'page_name': page_name,
                'url': page_url,
                'full_url': full_url,
                'status_code': response.status_code,
                'content_length': len(response.text),
                'has_content': len(response.text) > 1000,
                'is_error_page': 'error' in response.text.lower() or 'fatal' in response.text.lower(),
                'redirect': response.status_code == 302,
                'redirect_url': response.headers.get('Location', '') if response.status_code == 302 else ''
            }
            
            # Check for common page elements
            soup = BeautifulSoup(response.text, 'html.parser')
            result['has_bootstrap'] = 'bootstrap' in response.text.lower()
            result['has_navbar'] = soup.find('nav') is not None
            result['has_content'] = soup.find('div', class_='container') is not None or soup.find('div', class_='content') is not None
            
            # Determine success
            if response.status_code == 200 and not result['is_error_page'] and result['has_content']:
                result['success'] = True
                print(f"✅ {page_name} - OK ({len(response.text)} bytes)")
            else:
                result['success'] = False
                print(f"❌ {page_name} - FAILED (Status: {response.status_code})")
            
            return result
            
        except Exception as e:
            print(f"❌ Error testing {page_name}: {str(e)}")
            return {
                'page_name': page_name,
                'url': page_url,
                'success': False,
                'error': str(e)
            }
    
    def test_ajax_content(self):
        """Test AJAX content loading"""
        ajax_pages = [
            'dashboard',
            'personel_ultra', 
            'operations',
            'reports',
            'assignments',
            'settings',
            'profile',
            'jabatan_management',
            'kantor'
        ]
        
        print("\n🔄 Testing AJAX Content Loading...")
        
        for page in ajax_pages:
            try:
                ajax_url = urljoin(self.base_url, "ajax/content.php")
                
                # Test AJAX request
                response = self.session.post(ajax_url, data={'page': page})
                
                if response.status_code == 200:
                    try:
                        data = response.json()
                        if data.get('success'):
                            content_length = len(data.get('content', ''))
                            print(f"✅ AJAX {page} - OK ({content_length} chars)")
                        else:
                            print(f"❌ AJAX {page} - Failed: {data.get('message', 'Unknown error')}")
                    except:
                        print(f"❌ AJAX {page} - Invalid JSON")
                else:
                    print(f"❌ AJAX {page} - HTTP {response.status_code}")
                    
            except Exception as e:
                print(f"❌ AJAX {page} - Error: {str(e)}")
    
    def run_comprehensive_test(self):
        """Run comprehensive test of all pages and menus"""
        print("🧪 COMPREHENSIVE BAGOPS MENU & PAGE TESTING")
        print("=" * 60)
        
        # Login
        if not self.login():
            print("❌ Cannot login, aborting test")
            return False
        
        # Get menu from database
        menus = self.get_menu_from_database()
        
        if not menus:
            print("❌ No menus found, testing common pages...")
            menus = [
                {'name': 'Dashboard', 'url': 'dashboard'},
                {'name': 'Personel', 'url': 'personel_ultra'},
                {'name': 'Operations', 'url': 'operations'},
                {'name': 'Reports', 'url': 'reports'},
                {'name': 'Assignments', 'url': 'assignments'},
                {'name': 'Settings', 'url': 'settings'},
                {'name': 'Profile', 'url': 'profile'},
            ]
        
        print(f"\n📋 Testing {len(menus)} menu items...")
        
        # Test each menu/page
        successful_tests = 0
        failed_tests = 0
        
        for menu in menus:
            page_name = menu.get('name', 'Unknown')
            page_url = menu.get('url', '')
            
            if not page_url:
                continue
                
            result = self.test_page_access(page_url, page_name)
            self.test_results.append(result)
            
            if result['success']:
                successful_tests += 1
            else:
                failed_tests += 1
        
        # Test AJAX content
        self.test_ajax_content()
        
        # Print summary
        print(f"\n📊 TEST SUMMARY")
        print("=" * 40)
        print(f"✅ Successful: {successful_tests}")
        print(f"❌ Failed: {failed_tests}")
        print(f"📈 Success Rate: {(successful_tests/(successful_tests+failed_tests)*100):.1f}%" if (successful_tests+failed_tests) > 0 else "N/A")
        
        # Print failed tests details
        if failed_tests > 0:
            print(f"\n❌ FAILED TESTS:")
            for result in self.test_results:
                if not result['success']:
                    print(f"   • {result['page_name']} - {result.get('status_code', 'Error')}")
        
        return successful_tests > 0

if __name__ == "__main__":
    tester = BAGOPSComprehensiveTester()
    success = tester.run_comprehensive_test()
    
    if success:
        print("\n🎉 COMPREHENSIVE TEST COMPLETED SUCCESSFULLY!")
    else:
        print("\n💥 COMPREHENSIVE TEST FAILED!")
    
    sys.exit(0 if success else 1)
