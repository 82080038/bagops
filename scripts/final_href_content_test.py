#!/usr/bin/env python3
"""
Final comprehensive test untuk href dan content dengan role permissions
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
import json
from urllib.parse import urljoin
from bs4 import BeautifulSoup

class FinalHrefContentTester:
    def __init__(self, base_url="http://localhost/bagops"):
        self.base_url = base_url
        self.session = requests.Session()
        
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
    
    def get_page_permissions(self):
        """Get page permissions from database simulation"""
        # Based on database query results
        return {
            'assignments': {'title': 'Tugas', 'target_role': 'all'},
            'dashboard': {'title': 'Dashboard Utama', 'target_role': 'all'},
            'help': {'title': 'Bantuan', 'target_role': 'all'},
            'operations': {'title': 'Data Operasi', 'target_role': 'all'},
            'personel_ultra': {'title': 'Data Personel', 'target_role': 'all'},
            'profile': {'title': 'Profile', 'target_role': 'all'},
            'reports': {'title': 'Laporan', 'target_role': 'all'},
            'settings': {'title': 'Pengaturan', 'target_role': 'super_admin'},
            'jabatan_management': {'title': 'Jabatan Management', 'target_role': 'super_admin'}
        }
    
    def test_page_content(self, page_key, page_info):
        """Test content rendering halaman"""
        try:
            start_time = time.time()
            page_url = f"{self.base_url}/simple_root_system.php?page={page_key}"
            response = self.session.get(page_url, timeout=10)
            response_time = time.time() - start_time
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Extract content details
            content_analysis = {
                'page_key': page_key,
                'title': page_info['title'],
                'target_role': page_info['target_role'],
                'url': page_url,
                'http_status': response.status_code,
                'response_time': round(response_time, 3),
                'content_length': len(response.content),
                'page_title_tag': soup.title.string if soup.title else 'No title',
                
                # Layout components
                'has_header': bool(soup.find('header')),
                'has_main': bool(soup.find('main')),
                'has_breadcrumb': bool(soup.find('nav', class_='breadcrumb-container')),
                'has_page_header': bool(soup.find('div', class_='page-header')),
                'has_page_content': bool(soup.find('div', class_='page-content')),
                'has_footer': bool(soup.find('footer')),
                
                # Content elements
                'has_forms': len(soup.find_all('form')),
                'has_tables': len(soup.find_all('table')),
                'has_cards': len(soup.find_all('div', class_='card')),
                'has_buttons': len(soup.find_all('button')),
                'has_modals': len(soup.find_all('div', class_='modal')),
                
                # Error checking
                'has_errors': bool(soup.find('div', class_='alert-danger')),
                'error_messages': [],
                
                # Content quality
                'text_content': soup.get_text(strip=True),
                'text_content_length': len(soup.get_text(strip=True)),
                'has_meaningful_content': len(soup.get_text(strip=True)) > 100,
            }
            
            # Extract error messages
            error_alerts = soup.find_all('div', class_='alert-danger')
            for alert in error_alerts:
                error_text = alert.get_text(strip=True)
                if error_text:
                    content_analysis['error_messages'].append(error_text)
            
            # Extract displayed title
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
                'page_key': page_key,
                'title': page_info['title'],
                'status': 'ANALYSIS_ERROR',
                'error': str(e)
            }
    
    def extract_and_test_hrefs(self, page_key, page_info):
        """Extract hrefs dari halaman dan test accessibility"""
        try:
            page_url = f"{self.base_url}/simple_root_system.php?page={page_key}"
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
                        'source_page': page_key
                    })
            
            # Test href accessibility
            href_results = []
            permissions = self.get_page_permissions()
            
            for href_info in hrefs:
                href_url = href_info['url']
                href_text = href_info['text']
                
                try:
                    start_time = time.time()
                    response = self.session.get(href_url, timeout=5)
                    response_time = time.time() - start_time
                    
                    # Extract page key from URL
                    if 'simple_root_system.php?page=' in href_url:
                        href_page_key = href_url.split('page=')[1].split('&')[0]
                    elif 'login.php' in href_url:
                        href_page_key = 'login'
                    else:
                        href_page_key = 'unknown'
                    
                    # Check if href should be accessible for super_admin
                    expected_accessible = (
                        href_page_key == 'login' or
                        (href_page_key in permissions and 
                         permissions[href_page_key]['target_role'] in ['all', 'super_admin'])
                    )
                    
                    # Quick content check
                    has_content = len(response.text.strip()) > 50
                    has_error = 'error' in response.text.lower() or 'exception' in response.text.lower()
                    
                    result = {
                        'source_page': page_key,
                        'href_url': href_url,
                        'href_text': href_text,
                        'href_page_key': href_page_key,
                        'http_status': response.status_code,
                        'response_time': round(response_time, 3),
                        'content_length': len(response.content),
                        'has_content': has_content,
                        'has_error': has_error,
                        'expected_accessible': expected_accessible,
                        'accessible': response.status_code == 200 and has_content and not has_error,
                        'correct_access': (response.status_code == 200 and has_content and not has_error) == expected_accessible
                    }
                    
                    href_results.append(result)
                    
                except Exception as e:
                    href_results.append({
                        'source_page': page_key,
                        'href_url': href_url,
                        'href_text': href_text,
                        'status': 'ERROR',
                        'error': str(e),
                        'accessible': False,
                        'correct_access': False
                    })
            
            return href_results
            
        except Exception as e:
            print(f"❌ Error extracting hrefs from {page_key}: {str(e)}")
            return []
    
    def run_final_test(self):
        """Run final comprehensive test"""
        
        print("🧪 FINAL COMPREHENSIVE HREF & CONTENT TEST")
        print("=" * 60)
        
        # Login
        if not self.login():
            return
        
        # Get page permissions
        permissions = self.get_page_permissions()
        
        print(f"\n📋 TESTING {len(permissions)} PAGES FOR SUPER_ADMIN")
        print("=" * 60)
        
        all_results = {
            'page_contents': [],
            'href_tests': [],
            'summary': {}
        }
        
        # Test each page
        for page_key, page_info in permissions.items():
            print(f"\n📄 Testing Page: {page_info['title']} ({page_key})")
            print("-" * 50)
            
            # Test page content
            content_result = self.test_page_content(page_key, page_info)
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
            print(f"   🧩 Layout: Header={content_result['has_header']} | Main={content_result['has_main']} | Content={content_result['has_page_content']}")
            print(f"   📝 Elements: Forms={content_result['has_forms']} | Tables={content_result['has_tables']} | Cards={content_result['has_cards']}")
            
            # Extract and test hrefs
            if content_result['status'] in ['SUCCESS', 'NO_CONTENT']:
                href_results = self.extract_and_test_hrefs(page_key, page_info)
                all_results['href_tests'].extend(href_results)
                
                # Print href summary
                accessible_hrefs = [h for h in href_results if h['accessible']]
                correct_access_hrefs = [h for h in href_results if h['correct_access']]
                
                print(f"   🔗 Hrefs: {len(href_results)} total | {len(accessible_hrefs)} accessible | {len(correct_access_hrefs)} correct access")
            else:
                print(f"   🔗 Skipping href test due to {content_result['status']}")
        
        # Generate comprehensive summary
        self.generate_final_summary(all_results)
        
        # Save results
        self.save_final_results(all_results)
    
    def generate_final_summary(self, all_results):
        """Generate final comprehensive summary"""
        
        print(f"\n📊 FINAL COMPREHENSIVE SUMMARY")
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
        correct_access_hrefs = [h for h in href_tests if h['correct_access']]
        
        print(f"\n🔗 HREF ACCESSIBILITY SUMMARY:")
        print(f"   Total Hrefs: {total_hrefs}")
        print(f"   ✅ Accessible: {len(accessible_hrefs)} ({len(accessible_hrefs)/total_hrefs*100:.1f}%)" if total_hrefs > 0 else "   No hrefs found")
        print(f"   🎯 Correct Access: {len(correct_access_hrefs)} ({len(correct_access_hrefs)/total_hrefs*100:.1f}%)" if total_hrefs > 0 else "")
        
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
                print(f"   ❌ {page['page_key']:15} - {page['status']}")
                if 'error' in page:
                    print(f"      Error: {page['error'][:60]}...")
                elif 'error_messages' in page and page['error_messages']:
                    print(f"      Error: {page['error_messages'][0][:60]}...")
        
        # Incorrect href access
        incorrect_hrefs = [h for h in href_tests if not h['correct_access']]
        if incorrect_hrefs:
            print(f"\n⚠️ INCORRECT HREF ACCESS ({len(incorrect_hrefs)}):")
            for href in incorrect_hrefs[:10]:  # Show first 10
                reason = "Not accessible" if not href['accessible'] else "Accessible but shouldn't be"
                print(f"   ⚠️ {href['href_text']:25} - {reason} - {href.get('source_page', 'Unknown')}")
            if len(incorrect_hrefs) > 10:
                print(f"   ... and {len(incorrect_hrefs) - 10} more incorrect hrefs")
        
        # Store summary
        all_results['summary'] = {
            'total_pages': total_pages,
            'successful_pages': len(successful_pages),
            'failed_pages': len(failed_pages),
            'total_hrefs': total_hrefs,
            'accessible_hrefs': len(accessible_hrefs),
            'correct_access_hrefs': len(correct_access_hrefs),
            'incorrect_hrefs': len(incorrect_hrefs)
        }
    
    def save_final_results(self, all_results):
        """Save final results to file"""
        try:
            report_data = {
                'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
                'base_url': self.base_url,
                'role': 'super_admin',
                'test_type': 'final_href_content_comprehensive',
                'summary': all_results['summary'],
                'page_contents': all_results['page_contents'],
                'href_tests': all_results['href_tests']
            }
            
            with open('/var/www/html/bagops/test_results/final_href_content_test.json', 'w') as f:
                json.dump(report_data, f, indent=2, ensure_ascii=False)
            
            print(f"\n📁 Final results saved to: test_results/final_href_content_test.json")
            
        except Exception as e:
            print(f"❌ Error saving results: {str(e)}")

def main():
    """Main function"""
    tester = FinalHrefContentTester()
    tester.run_final_test()
    print(f"\n✅ FINAL COMPREHENSIVE HREF & CONTENT TEST COMPLETED!")

if __name__ == "__main__":
    main()
