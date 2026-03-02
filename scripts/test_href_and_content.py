#!/usr/bin/env python3
"""
Script untuk memeriksa setiap href dan content yang dirender
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
import json
import re
from urllib.parse import urljoin, urlparse
from bs4 import BeautifulSoup

class HrefContentTester:
    def __init__(self, base_url="http://localhost/bagops"):
        self.base_url = base_url
        self.session = requests.Session()
        self.test_results = []
        
    def login(self, username="super_admin", password="admin123"):
        """Login ke sistem"""
        try:
            login_data = {'username': username, 'password': password}
            response = self.session.post(f"{self.base_url}/login.php", data=login_data)
            
            # Verify login
            index_response = self.session.get(f"{self.base_url}/index.php")
            if index_response.status_code == 200:
                print("✅ Login successful as super_admin")
                return True
            else:
                print("❌ Login failed")
                return False
        except Exception as e:
            print(f"❌ Login error: {str(e)}")
            return False
    
    def extract_all_hrefs(self, page_url):
        """Extract semua href dari halaman"""
        try:
            response = self.session.get(page_url, timeout=10)
            soup = BeautifulSoup(response.content, 'html.parser')
            
            hrefs = []
            
            # Extract all links
            for link in soup.find_all('a', href=True):
                href = link['href']
                text = link.get_text(strip=True)
                
                # Skip empty or javascript links
                if not href or href.startswith('javascript:') or href.startswith('#'):
                    continue
                
                # Convert relative URLs to absolute
                if href.startswith('/'):
                    href = urljoin(self.base_url, href)
                elif not href.startswith('http'):
                    href = urljoin(page_url, href)
                
                # Only include internal links
                if self.base_url in href:
                    hrefs.append({
                        'url': href,
                        'text': text,
                        'element': str(link)[:100] + '...' if len(str(link)) > 100 else str(link)
                    })
            
            return hrefs
            
        except Exception as e:
            print(f"❌ Error extracting hrefs from {page_url}: {str(e)}")
            return []
    
    def test_page_content(self, page_url, page_name):
        """Test content rendering halaman"""
        try:
            start_time = time.time()
            response = self.session.get(page_url, timeout=10)
            response_time = time.time() - start_time
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Basic checks
            content_analysis = {
                'page_name': page_name,
                'url': page_url,
                'http_status': response.status_code,
                'response_time': round(response_time, 3),
                'content_length': len(response.content),
                'page_title': soup.title.string if soup.title else 'No title',
                
                # Layout components
                'has_header': bool(soup.find('header')),
                'has_main': bool(soup.find('main')),
                'has_navbar': bool(soup.find('nav')),
                'has_breadcrumb': bool(soup.find('nav', class_='breadcrumb-container')),
                'has_page_header': bool(soup.find('div', class_='page-header')),
                'has_page_content': bool(soup.find('div', class_='page-content')),
                'has_footer': bool(soup.find('footer')),
                
                # Content elements
                'has_h1': len(soup.find_all('h1')),
                'has_h2': len(soup.find_all('h2')),
                'has_h3': len(soup.find_all('h3')),
                'has_forms': len(soup.find_all('form')),
                'has_tables': len(soup.find_all('table')),
                'has_cards': len(soup.find_all('div', class_='card')),
                'has_buttons': len(soup.find_all('button')),
                'has_inputs': len(soup.find_all('input')),
                'has_selects': len(soup.find_all('select')),
                'has_textareas': len(soup.find_all('textarea')),
                
                # Interactive elements
                'has_modals': len(soup.find_all('div', class_='modal')),
                'has_dropdowns': len(soup.find_all('div', class_='dropdown')),
                'has_tabs': len(soup.find_all('div', class_='tab')),
                'has_accordions': len(soup.find_all('div', class_='accordion')),
                
                # Error checking
                'has_errors': bool(soup.find('div', class_='alert-danger')),
                'has_warnings': bool(soup.find('div', class_='alert-warning')),
                'error_messages': [],
                
                # Content quality
                'text_content': soup.get_text(strip=True),
                'text_content_length': len(soup.get_text(strip=True)),
                'has_meaningful_content': len(soup.get_text(strip=True)) > 100,
                
                # Navigation
                'navigation_links': len(soup.find_all('nav a')),
                'breadcrumb_items': len(soup.find_all('li', class_='breadcrumb-item')),
                
                # Scripts and styles
                'has_jquery': bool(soup.find('script', src=re.compile(r'jquery', re.I))),
                'has_bootstrap': bool(soup.find('link', href=re.compile(r'bootstrap', re.I)) or 
                                 bool(soup.find('script', src=re.compile(r'bootstrap', re.I)))),
                'has_datatables': bool(soup.find('script', src=re.compile(r'datatables', re.I))),
            }
            
            # Extract error messages
            error_alerts = soup.find_all('div', class_='alert-danger')
            for alert in error_alerts:
                error_text = alert.get_text(strip=True)
                if error_text:
                    content_analysis['error_messages'].append(error_text)
            
            # Extract page title from header
            page_title_elem = soup.find('h1', class_='page-title')
            if page_title_elem:
                content_analysis['displayed_title'] = page_title_elem.get_text(strip=True)
            else:
                content_analysis['displayed_title'] = 'No page title found'
            
            # Determine status
            if response.status_code == 200:
                if content_analysis['has_meaningful_content'] and not content_analysis['has_errors']:
                    content_analysis['status'] = 'SUCCESS'
                elif content_analysis['has_errors']:
                    content_analysis['status'] = 'ERROR'
                else:
                    content_analysis['status'] = 'NO_CONTENT'
            elif response.status_code == 403:
                content_analysis['status'] = 'FORBIDDEN'
            elif response.status_code == 404:
                content_analysis['status'] = 'NOT_FOUND'
            else:
                content_analysis['status'] = 'HTTP_ERROR'
            
            return content_analysis
            
        except Exception as e:
            return {
                'page_name': page_name,
                'url': page_url,
                'status': 'ANALYSIS_ERROR',
                'error': str(e)
            }
    
    def test_href_accessibility(self, hrefs, source_page):
        """Test accessibility of all hrefs"""
        href_results = []
        
        print(f"\n🔗 Testing {len(hrefs)} hrefs from {source_page}")
        
        for i, href_info in enumerate(hrefs, 1):
            href_url = href_info['url']
            href_text = href_info['text']
            
            try:
                start_time = time.time()
                response = self.session.get(href_url, timeout=5)
                response_time = time.time() - start_time
                
                # Quick content check
                has_content = len(response.text.strip()) > 50
                has_error = 'error' in response.text.lower() or 'exception' in response.text.lower()
                
                result = {
                    'source_page': source_page,
                    'href_url': href_url,
                    'href_text': href_text,
                    'http_status': response.status_code,
                    'response_time': round(response_time, 3),
                    'content_length': len(response.content),
                    'has_content': has_content,
                    'has_error': has_error,
                    'accessible': response.status_code == 200 and has_content and not has_error
                }
                
                href_results.append(result)
                
                # Print progress
                status_icon = '✅' if result['accessible'] else '❌'
                print(f"   {i:2d}. {status_icon} {href_text:30} - {response.status_code} - {response_time:.3f}s")
                
            except Exception as e:
                href_results.append({
                    'source_page': source_page,
                    'href_url': href_url,
                    'href_text': href_text,
                    'status': 'ERROR',
                    'error': str(e),
                    'accessible': False
                })
                print(f"   {i:2d}. ❌ {href_text:30} - ERROR - {str(e)[:30]}")
        
        return href_results
    
    def run_comprehensive_test(self):
        """Run comprehensive href and content test"""
        
        print("🧪 COMPREHENSIVE HREF & CONTENT TEST")
        print("=" * 60)
        
        # Login
        if not self.login():
            return
        
        # Main pages to test
        main_pages = [
            ('dashboard', 'Dashboard'),
            ('personel', 'Personel'), 
            ('jabatan_management', 'Jabatan Management'),
            ('settings', 'Settings'),
            ('reports', 'Reports'),
            ('profile', 'Profile'),
            ('help', 'Help'),
            ('assignments', 'Assignments'),
            ('operations', 'Operations'),
        ]
        
        all_results = {
            'page_contents': [],
            'href_tests': [],
            'summary': {}
        }
        
        # Test each main page
        for page_key, page_title in main_pages:
            print(f"\n📄 Testing Page: {page_title}")
            print("-" * 40)
            
            page_url = f"{self.base_url}/simple_root_system.php?page={page_key}"
            
            # Test page content
            content_result = self.test_page_content(page_url, page_title)
            all_results['page_contents'].append(content_result)
            
            # Print content summary
            status_icon = {
                'SUCCESS': '✅',
                'FORBIDDEN': '🚫',
                'NOT_FOUND': '❓',
                'ERROR': '❌',
                'NO_CONTENT': '⚠️',
                'HTTP_ERROR': '❌',
                'ANALYSIS_ERROR': '❌'
            }.get(content_result['status'], '❓')
            
            print(f"   {status_icon} Content: {content_result['status']}")
            print(f"   📊 HTTP: {content_result['http_status']} | Time: {content_result['response_time']}s | Size: {content_result['content_length']} bytes")
            print(f"   🧩 Components: Header={content_result['has_header']} | Main={content_result['has_main']} | Content={content_result['has_page_content']}")
            print(f"   📝 Elements: Forms={content_result['has_forms']} | Tables={content_result['has_tables']} | Cards={content_result['has_cards']}")
            
            # Extract and test hrefs
            if content_result['status'] in ['SUCCESS', 'NO_CONTENT']:
                hrefs = self.extract_all_hrefs(page_url)
                if hrefs:
                    href_results = self.test_href_accessibility(hrefs, page_title)
                    all_results['href_tests'].extend(href_results)
                else:
                    print(f"   🔗 No hrefs found")
            else:
                print(f"   🔗 Skipping href test due to {content_result['status']}")
        
        # Generate summary
        self.generate_summary(all_results)
        
        # Save results
        self.save_results(all_results)
    
    def generate_summary(self, all_results):
        """Generate comprehensive summary"""
        
        print(f"\n📊 COMPREHENSIVE SUMMARY")
        print("=" * 60)
        
        # Page content summary
        page_contents = all_results['page_contents']
        total_pages = len(page_contents)
        successful_pages = [p for p in page_contents if p['status'] == 'SUCCESS']
        failed_pages = [p for p in page_contents if p['status'] in ['ERROR', 'FORBIDDEN', 'NOT_FOUND', 'ANALYSIS_ERROR']]
        
        print(f"📄 PAGE CONTENT SUMMARY:")
        print(f"   Total Pages: {total_pages}")
        print(f"   ✅ Successful: {len(successful_pages)} ({len(successful_pages)/total_pages*100:.1f}%)")
        print(f"   ❌ Failed: {len(failed_pages)} ({len(failed_pages)/total_pages*100:.1f}%)")
        
        if successful_pages:
            avg_response_time = sum(p['response_time'] for p in successful_pages) / len(successful_pages)
            avg_content_size = sum(p['content_length'] for p in successful_pages) / len(successful_pages)
            print(f"   ⏱️ Avg Response Time: {avg_response_time:.3f}s")
            print(f"   📏 Avg Content Size: {avg_content_size:.0f} bytes")
        
        # Href summary
        href_tests = all_results['href_tests']
        total_hrefs = len(href_tests)
        accessible_hrefs = [h for h in href_tests if h['accessible']]
        broken_hrefs = [h for h in href_tests if not h['accessible']]
        
        print(f"\n🔗 HREF ACCESSIBILITY SUMMARY:")
        print(f"   Total Hrefs: {total_hrefs}")
        print(f"   ✅ Accessible: {len(accessible_hrefs)} ({len(accessible_hrefs)/total_hrefs*100:.1f}%)" if total_hrefs > 0 else "   No hrefs found")
        print(f"   ❌ Broken: {len(broken_hrefs)} ({len(broken_hrefs)/total_hrefs*100:.1f}%)" if total_hrefs > 0 else "")
        
        if accessible_hrefs:
            avg_href_response = sum(h['response_time'] for h in accessible_hrefs) / len(accessible_hrefs)
            print(f"   ⏱️ Avg Href Response: {avg_href_response:.3f}s")
        
        # Component analysis
        print(f"\n🧩 COMPONENT ANALYSIS:")
        components = {
            'has_header': 'Header',
            'has_main': 'Main Content', 
            'has_breadcrumb': 'Breadcrumb',
            'has_page_header': 'Page Header',
            'has_page_content': 'Page Content',
            'has_footer': 'Footer',
            'has_forms': 'Forms',
            'has_tables': 'Tables',
            'has_cards': 'Cards',
            'has_modals': 'Modals'
        }
        
        for comp_key, comp_name in components.items():
            count = sum(1 for p in page_contents if p.get(comp_key, False) or p.get(comp_key, 0) > 0)
            print(f"   📦 {comp_name:15} - {count}/{total_pages} ({count/total_pages*100:.1f}%)")
        
        # Failed pages details
        if failed_pages:
            print(f"\n❌ FAILED PAGES DETAILS:")
            for page in failed_pages:
                print(f"   ❌ {page['page_name']:15} - {page['status']}")
                if 'error' in page:
                    print(f"      Error: {page['error'][:60]}...")
                elif 'error_messages' in page and page['error_messages']:
                    print(f"      Error: {page['error_messages'][0][:60]}...")
        
        # Broken hrefs details
        if broken_hrefs:
            print(f"\n❌ BROKEN HREFS DETAILS:")
            for href in broken_hrefs[:10]:  # Show first 10
                print(f"   ❌ {href['href_text']:25} - {href.get('http_status', 'ERROR')} - {href.get('source_page', 'Unknown')}")
            if len(broken_hrefs) > 10:
                print(f"   ... and {len(broken_hrefs) - 10} more broken hrefs")
        
        # Store summary
        all_results['summary'] = {
            'total_pages': total_pages,
            'successful_pages': len(successful_pages),
            'failed_pages': len(failed_pages),
            'total_hrefs': total_hrefs,
            'accessible_hrefs': len(accessible_hrefs),
            'broken_hrefs': len(broken_hrefs)
        }
    
    def save_results(self, all_results):
        """Save detailed results to file"""
        try:
            report_data = {
                'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
                'base_url': self.base_url,
                'role': 'super_admin',
                'test_type': 'href_and_content_comprehensive',
                'summary': all_results['summary'],
                'page_contents': all_results['page_contents'],
                'href_tests': all_results['href_tests']
            }
            
            with open('/var/www/html/bagops/test_results/href_content_test.json', 'w') as f:
                json.dump(report_data, f, indent=2, ensure_ascii=False)
            
            print(f"\n📁 Detailed results saved to: test_results/href_content_test.json")
            
        except Exception as e:
            print(f"❌ Error saving results: {str(e)}")

def main():
    """Main function"""
    tester = HrefContentTester()
    tester.run_comprehensive_test()
    print(f"\n✅ COMPREHENSIVE HREF & CONTENT TEST COMPLETED!")

if __name__ == "__main__":
    main()
