#!/usr/bin/env python3
"""
Script untuk mengecek seluruh render halaman BAGOPS untuk role super_admin
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import json
import time
from urllib.parse import urljoin
from bs4 import BeautifulSoup
import mysql.connector

class BAGOPSPageTester:
    def __init__(self, base_url="http://localhost/bagops"):
        self.base_url = base_url
        self.session = requests.Session()
        self.test_results = []
        self.authenticated = False
        
    def login(self, username="super_admin", password="admin123"):
        """Login ke sistem BAGOPS"""
        try:
            login_url = urljoin(self.base_url, "login.php")
            
            # Get login page first to get session
            response = self.session.get(login_url)
            
            # Login data
            login_data = {
                'username': username,
                'password': password
            }
            
            # Post login
            response = self.session.post(login_url, data=login_data)
            
            # Check if login successful
            if response.status_code == 200 and "dashboard" in response.text.lower():
                self.authenticated = True
                print(f"✅ Login successful as {username}")
                return True
            else:
                print(f"❌ Login failed for {username}")
                return False
                
        except Exception as e:
            print(f"❌ Login error: {str(e)}")
            return False
    
    def get_all_pages_from_db(self):
        """Ambil semua halaman dari database"""
        try:
            connection = mysql.connector.connect(
                host='127.0.0.1',
                port='3306',
                user='root',
                password='root',
                database='bagops_db'
            )
            
            cursor = connection.cursor()
            cursor.execute("""
                SELECT page_key, title, target_role, is_active 
                FROM pages 
                WHERE is_active = 1 
                ORDER BY order_index
            """)
            
            pages = cursor.fetchall()
            connection.close()
            
            return pages
            
        except Exception as e:
            print(f"❌ Database error: {str(e)}")
            return []
    
    def get_static_pages(self):
        """Halaman statis yang tidak ada di database"""
        return [
            ('dashboard', 'Dashboard', 'all', True),
            ('profile', 'Profile', 'all', True),
            ('help', 'Help', 'all', True),
            ('jabatan_management', 'Jabatan Management', 'admin', True),
            ('personel_ultra', 'Personel Ultra', 'admin', True),
            ('settings', 'Settings', 'admin', True),
            ('reports', 'Reports', 'user', True),
            ('assignments', 'Assignments', 'user', True),
            ('operations', 'Operations', 'kabag_ops', True),
            ('personel', 'Personel', 'kabag_ops', True),
        ]
    
    def test_page_render(self, page_key, title, target_role, is_active):
        """Test render halaman tertentu"""
        if not self.authenticated:
            return {
                'page_key': page_key,
                'title': title,
                'status': 'ERROR',
                'error': 'Not authenticated',
                'response_time': 0,
                'content_length': 0,
                'has_content': False,
                'has_errors': False,
                'has_forms': False,
                'has_tables': False,
                'has_cards': False
            }
        
        try:
            start_time = time.time()
            
            # Test direct page access
            page_url = urljoin(self.base_url, f"simple_root_system.php?page={page_key}")
            response = self.session.get(page_url, timeout=10)
            
            response_time = time.time() - start_time
            content_length = len(response.content)
            
            # Parse HTML content
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Check for content
            has_content = self.check_content_exists(soup)
            has_errors = self.check_for_errors(soup)
            has_forms = self.check_for_forms(soup)
            has_tables = self.check_for_tables(soup)
            has_cards = self.check_for_cards(soup)
            
            # Determine status
            if response.status_code == 200 and has_content and not has_errors:
                status = 'SUCCESS'
            elif response.status_code == 403:
                status = 'FORBIDDEN'
            elif response.status_code == 404:
                status = 'NOT_FOUND'
            elif has_errors:
                status = 'ERROR'
            else:
                status = 'NO_CONTENT'
            
            result = {
                'page_key': page_key,
                'title': title,
                'target_role': target_role,
                'is_active': is_active,
                'status': status,
                'response_time': round(response_time, 2),
                'content_length': content_length,
                'has_content': has_content,
                'has_errors': has_errors,
                'has_forms': has_forms,
                'has_tables': has_tables,
                'has_cards': has_cards,
                'http_status': response.status_code
            }
            
            # Add error details if any
            if has_errors:
                result['error_details'] = self.extract_error_details(soup)
            
            return result
            
        except requests.exceptions.Timeout:
            return {
                'page_key': page_key,
                'title': title,
                'status': 'TIMEOUT',
                'error': 'Request timeout',
                'response_time': 10.0,
                'content_length': 0,
                'has_content': False,
                'has_errors': True,
                'has_forms': False,
                'has_tables': False,
                'has_cards': False
            }
        except Exception as e:
            return {
                'page_key': page_key,
                'title': title,
                'status': 'ERROR',
                'error': str(e),
                'response_time': 0,
                'content_length': 0,
                'has_content': False,
                'has_errors': True,
                'has_forms': False,
                'has_tables': False,
                'has_cards': False
            }
    
    def check_content_exists(self, soup):
        """Check if page has meaningful content"""
        # Check for page content area
        page_content = soup.find('div', class_='page-content')
        if page_content:
            # Remove whitespace-only text
            text_content = page_content.get_text(strip=True)
            return len(text_content) > 10
        
        # Fallback: check main content area
        main_content = soup.find('main', class_='main-content')
        if main_content:
            text_content = main_content.get_text(strip=True)
            return len(text_content) > 20
        
        return False
    
    def check_for_errors(self, soup):
        """Check for error messages or exceptions"""
        error_indicators = [
            'alert-danger',
            'error',
            'exception',
            'fatal error',
            'warning',
            'notice',
            'undefined',
            'mysql error',
            'database error',
            'syntax error'
        ]
        
        # Check for error alerts
        error_alerts = soup.find_all('div', class_=lambda x: x and 'alert' in x and 'danger' in x)
        if error_alerts:
            return True
        
        # Check for error text
        page_text = soup.get_text().lower()
        for indicator in error_indicators:
            if indicator in page_text:
                return True
        
        return False
    
    def extract_error_details(self, soup):
        """Extract error details from page"""
        error_details = []
        
        # Get error alerts
        error_alerts = soup.find_all('div', class_=lambda x: x and 'alert' in x and 'danger' in x)
        for alert in error_alerts:
            error_text = alert.get_text(strip=True)
            if error_text:
                error_details.append(error_text)
        
        return error_details
    
    def check_for_forms(self, soup):
        """Check if page has forms"""
        forms = soup.find_all('form')
        return len(forms) > 0
    
    def check_for_tables(self, soup):
        """Check if page has tables"""
        tables = soup.find_all('table')
        return len(tables) > 0
    
    def check_for_cards(self, soup):
        """Check if page has Bootstrap cards"""
        cards = soup.find_all('div', class_='card')
        return len(cards) > 0
    
    def test_all_pages(self):
        """Test semua halaman"""
        print("🧪 TESTING ALL PAGES RENDER - SUPER ADMIN ROLE")
        print("=" * 60)
        
        # Login first
        if not self.login():
            print("❌ Cannot login, aborting test")
            return
        
        # Get all pages from database
        db_pages = self.get_all_pages_from_db()
        static_pages = self.get_static_pages()
        
        # Combine pages
        all_pages = db_pages + static_pages
        
        # Remove duplicates
        unique_pages = {}
        for page in all_pages:
            page_key = page[0]
            if page_key not in unique_pages:
                unique_pages[page_key] = page
        
        print(f"📊 Found {len(unique_pages)} unique pages to test")
        print()
        
        # Test each page
        results = []
        for page_key, page_data in unique_pages.items():
            title, target_role, is_active = page_data[1], page_data[2], page_data[3]
            
            print(f"🔍 Testing: {page_key} - {title}")
            result = self.test_page_render(page_key, title, target_role, is_active)
            results.append(result)
            
            # Print result
            status_icon = {
                'SUCCESS': '✅',
                'FORBIDDEN': '🚫',
                'NOT_FOUND': '❓',
                'ERROR': '❌',
                'NO_CONTENT': '⚠️',
                'TIMEOUT': '⏰'
            }.get(result['status'], '❓')
            
            print(f"   {status_icon} {result['status']} - {result['response_time']}s - {result['content_length']} bytes")
            
            if result['has_errors'] and 'error_details' in result:
                for error in result['error_details'][:2]:  # Show first 2 errors
                    print(f"      📝 {error[:100]}...")
            
            print()
        
        self.test_results = results
        return results
    
    def generate_report(self):
        """Generate comprehensive test report"""
        if not self.test_results:
            print("❌ No test results available")
            return
        
        print("📊 COMPREHENSIVE TEST REPORT")
        print("=" * 60)
        
        # Statistics
        total_pages = len(self.test_results)
        success_count = sum(1 for r in self.test_results if r['status'] == 'SUCCESS')
        error_count = sum(1 for r in self.test_results if r['status'] in ['ERROR', 'TIMEOUT'])
        forbidden_count = sum(1 for r in self.test_results if r['status'] == 'FORBIDDEN')
        not_found_count = sum(1 for r in self.test_results if r['status'] == 'NOT_FOUND')
        no_content_count = sum(1 for r in self.test_results if r['status'] == 'NO_CONTENT')
        
        avg_response_time = sum(r['response_time'] for r in self.test_results) / total_pages
        
        print(f"📈 STATISTICS:")
        print(f"   Total Pages Tested: {total_pages}")
        print(f"   ✅ Successful: {success_count} ({success_count/total_pages*100:.1f}%)")
        print(f"   ❌ Errors: {error_count} ({error_count/total_pages*100:.1f}%)")
        print(f"   🚫 Forbidden: {forbidden_count} ({forbidden_count/total_pages*100:.1f}%)")
        print(f"   ❓ Not Found: {not_found_count} ({not_found_count/total_pages*100:.1f}%)")
        print(f"   ⚠️ No Content: {no_content_count} ({no_content_count/total_pages*100:.1f}%)")
        print(f"   ⏱️ Avg Response Time: {avg_response_time:.2f}s")
        print()
        
        # Content analysis
        pages_with_content = sum(1 for r in self.test_results if r['has_content'])
        pages_with_forms = sum(1 for r in self.test_results if r['has_forms'])
        pages_with_tables = sum(1 for r in self.test_results if r['has_tables'])
        pages_with_cards = sum(1 for r in self.test_results if r['has_cards'])
        
        print(f"📋 CONTENT ANALYSIS:")
        print(f"   Pages with Content: {pages_with_content}/{total_pages} ({pages_with_content/total_pages*100:.1f}%)")
        print(f"   Pages with Forms: {pages_with_forms}/{total_pages} ({pages_with_forms/total_pages*100:.1f}%)")
        print(f"   Pages with Tables: {pages_with_tables}/{total_pages} ({pages_with_tables/total_pages*100:.1f}%)")
        print(f"   Pages with Cards: {pages_with_cards}/{total_pages} ({pages_with_cards/total_pages*100:.1f}%)")
        print()
        
        # Failed pages
        failed_pages = [r for r in self.test_results if r['status'] in ['ERROR', 'TIMEOUT', 'NOT_FOUND']]
        if failed_pages:
            print(f"❌ FAILED PAGES ({len(failed_pages)}):")
            for page in failed_pages:
                print(f"   • {page['page_key']} - {page['status']}")
                if 'error' in page:
                    print(f"     Error: {page['error'][:80]}...")
            print()
        
        # Slow pages
        slow_pages = sorted(self.test_results, key=lambda x: x['response_time'], reverse=True)[:5]
        if slow_pages and slow_pages[0]['response_time'] > 2.0:
            print(f"🐌 SLOW PAGES (Top 5):")
            for page in slow_pages:
                print(f"   • {page['page_key']} - {page['response_time']}s")
            print()
        
        # Success pages
        success_pages = [r for r in self.test_results if r['status'] == 'SUCCESS']
        if success_pages:
            print(f"✅ SUCCESSFUL PAGES ({len(success_pages)}):")
            for page in success_pages[:10]:  # Show first 10
                print(f"   • {page['page_key']} - {page['title']}")
            if len(success_pages) > 10:
                print(f"   ... and {len(success_pages) - 10} more")
            print()
        
        # Save detailed report to file
        self.save_detailed_report()
    
    def save_detailed_report(self):
        """Save detailed test report to JSON file"""
        try:
            report_data = {
                'test_timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
                'base_url': self.base_url,
                'role': 'super_admin',
                'total_pages': len(self.test_results),
                'results': self.test_results
            }
            
            report_file = '/var/www/html/bagops/test_results/page_render_report.json'
            
            import os
            os.makedirs(os.path.dirname(report_file), exist_ok=True)
            
            with open(report_file, 'w', encoding='utf-8') as f:
                json.dump(report_data, f, indent=2, ensure_ascii=False)
            
            print(f"📁 Detailed report saved to: {report_file}")
            
        except Exception as e:
            print(f"❌ Error saving report: {str(e)}")

def main():
    """Main function"""
    tester = BAGOPSPageTester()
    
    # Test all pages
    tester.test_all_pages()
    
    # Generate report
    tester.generate_report()
    
    print("🎉 PAGE RENDER TESTING COMPLETED!")
    print("📋 Check the detailed report for more information")

if __name__ == "__main__":
    main()
