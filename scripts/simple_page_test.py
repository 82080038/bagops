#!/usr/bin/env python3
"""
Script sederhana untuk mengecek render halaman BAGOPS
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
import json
from urllib.parse import urljoin

def test_page_access():
    """Test akses halaman dengan curl-like approach"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Test pages to check
    pages = [
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
    
    print("🧪 SIMPLE PAGE ACCESS TEST")
    print("=" * 50)
    
    # Test login first
    print("1. Testing login...")
    try:
        # Get login page
        login_response = session.get(f"{base_url}/login.php")
        print(f"   Login page status: {login_response.status_code}")
        
        # Try login
        login_data = {'username': 'super_admin', 'password': 'admin123'}
        login_post = session.post(f"{base_url}/login.php", data=login_data)
        print(f"   Login POST status: {login_post.status_code}")
        
        # Check if login successful by trying to access index
        index_response = session.get(f"{base_url}/index.php")
        print(f"   Index page status: {index_response.status_code}")
        
        if index_response.status_code == 200 and 'dashboard' in index_response.text.lower():
            print("   ✅ Login successful")
        else:
            print("   ❌ Login failed")
            return
            
    except Exception as e:
        print(f"   ❌ Login error: {str(e)}")
        return
    
    print("\n2. Testing page access...")
    
    results = []
    
    for page_key, page_title in pages:
        try:
            start_time = time.time()
            
            # Test page access
            page_url = f"{base_url}/simple_root_system.php?page={page_key}"
            response = session.get(page_url, timeout=5)
            
            response_time = time.time() - start_time
            content_length = len(response.content)
            
            # Basic content checks
            has_content = len(response.text.strip()) > 100
            has_error = 'error' in response.text.lower() or 'exception' in response.text.lower()
            has_title = page_title.lower() in response.text.lower()
            
            # Determine status
            if response.status_code == 200 and has_content and not has_error:
                status = 'SUCCESS'
            elif response.status_code == 403:
                status = 'FORBIDDEN'
            elif response.status_code == 404:
                status = 'NOT_FOUND'
            elif has_error:
                status = 'ERROR'
            else:
                status = 'UNKNOWN'
            
            result = {
                'page': page_key,
                'title': page_title,
                'status': status,
                'response_time': round(response_time, 2),
                'content_length': content_length,
                'has_content': has_content,
                'has_error': has_error,
                'has_title': has_title
            }
            
            results.append(result)
            
            # Print result
            status_icon = {
                'SUCCESS': '✅',
                'FORBIDDEN': '🚫',
                'NOT_FOUND': '❓',
                'ERROR': '❌',
                'UNKNOWN': '❓'
            }.get(status, '❓')
            
            print(f"   {status_icon} {page_key:15} - {status:10} - {response_time:.2f}s - {content_length:5} bytes")
            
        except Exception as e:
            print(f"   ❌ {page_key:15} - ERROR - {str(e)[:30]}")
            results.append({
                'page': page_key,
                'title': page_title,
                'status': 'ERROR',
                'error': str(e)
            })
    
    # Summary
    print(f"\n3. Summary:")
    total = len(results)
    success = sum(1 for r in results if r['status'] == 'SUCCESS')
    errors = sum(1 for r in results if r['status'] in ['ERROR', 'FORBIDDEN', 'NOT_FOUND'])
    
    print(f"   Total pages: {total}")
    print(f"   ✅ Success: {success}")
    print(f"   ❌ Errors: {errors}")
    print(f"   📊 Success rate: {success/total*100:.1f}%")
    
    # Save results
    try:
        with open('/var/www/html/bagops/test_results/simple_page_test.json', 'w') as f:
            json.dump({
                'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
                'total_pages': total,
                'success_count': success,
                'error_count': errors,
                'results': results
            }, f, indent=2)
        print(f"   📁 Results saved to: test_results/simple_page_test.json")
    except Exception as e:
        print(f"   ❌ Error saving results: {str(e)}")
    
    print(f"\n✅ SIMPLE PAGE TEST COMPLETED!")

if __name__ == "__main__":
    test_page_access()
