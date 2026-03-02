#!/usr/bin/env python3
"""
Simple Menu Testing for BAGOPS
"""

import requests
from urllib.parse import urljoin

def test_login_and_pages():
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    print("🔐 Testing login...")
    
    # Test login page
    try:
        response = session.get(f"{base_url}/login.php")
        print(f"Login page status: {response.status_code}")
        
        if response.status_code == 200:
            # Try login
            login_data = {'username': 'super_admin', 'password': 'admin123'}
            response = session.post(f"{base_url}/login.php", data=login_data, allow_redirects=False)
            
            print(f"Login POST status: {response.status_code}")
            
            if response.status_code == 302:
                # Follow redirect
                redirect_url = response.headers.get('Location', '')
                print(f"Redirect to: {redirect_url}")
                
                response = session.get(f"{base_url}/{redirect_url}")
                print(f"Dashboard status: {response.status_code}")
                
                if response.status_code == 200:
                    print("✅ Login successful!")
                    
                    # Test menu pages
                    pages = [
                        ('Dashboard', 'dashboard'),
                        ('Personel', 'personel_ultra'),
                        ('Operations', 'operations'),
                        ('Reports', 'reports'),
                        ('Assignments', 'assignments'),
                        ('Settings', 'settings'),
                        ('Profile', 'profile'),
                    ]
                    
                    print("\n📋 Testing menu pages...")
                    
                    for name, page in pages:
                        try:
                            # Test direct page
                            response = session.get(f"{base_url}/simple_root_system.php?page={page}")
                            print(f"{name}: {response.status_code} ({len(response.text)} bytes)")
                            
                            if response.status_code == 200 and len(response.text) > 1000:
                                print(f"  ✅ {name} - OK")
                            else:
                                print(f"  ❌ {name} - FAILED")
                                
                        except Exception as e:
                            print(f"  ❌ {name} - ERROR: {e}")
                    
                    # Test AJAX content
                    print("\n🔄 Testing AJAX content...")
                    
                    for page in ['dashboard', 'personel_ultra', 'operations', 'reports']:
                        try:
                            response = session.post(f"{base_url}/ajax/content.php", data={'page': page})
                            if response.status_code == 200:
                                try:
                                    data = response.json()
                                    if data.get('success'):
                                        print(f"  ✅ AJAX {page} - OK")
                                    else:
                                        print(f"  ❌ AJAX {page} - {data.get('message', 'Failed')}")
                                except:
                                    print(f"  ❌ AJAX {page} - Invalid JSON")
                            else:
                                print(f"  ❌ AJAX {page} - HTTP {response.status_code}")
                        except Exception as e:
                            print(f"  ❌ AJAX {page} - ERROR: {e}")
                    
                    return True
                else:
                    print("❌ Dashboard load failed")
            else:
                print(f"❌ Login failed - status {response.status_code}")
        else:
            print(f"❌ Login page not found - status {response.status_code}")
            
    except Exception as e:
        print(f"❌ Error: {e}")
    
    return False

if __name__ == "__main__":
    success = test_login_and_pages()
    print(f"\n{'🎉 SUCCESS' if success else '💥 FAILED'}")
