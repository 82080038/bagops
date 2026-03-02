#!/usr/bin/env python3
"""
Detailed Menu and Content Analysis for BAGOPS Super Admin
"""

import requests
from urllib.parse import urljoin
from bs4 import BeautifulSoup
import json

def analyze_menu_and_content():
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    print("🔐 Login sebagai Super Admin...")
    
    # Login
    session.post(f"{base_url}/login.php", data={'username': 'super_admin', 'password': 'admin123'})
    
    # Get dashboard to extract menu
    response = session.get(f"{base_url}/simple_root_system.php?page=dashboard")
    
    if response.status_code != 200:
        print("❌ Gagal load dashboard")
        return
    
    soup = BeautifulSoup(response.text, 'html.parser')
    
    print("\n📋 ANALISIS MENU DAN NAVIGASI")
    print("=" * 50)
    
    # Extract navigation menu
    nav_items = []
    
    # Find navbar
    navbar = soup.find('nav') or soup.find(class_='navbar')
    if navbar:
        # Find all links in navbar
        links = navbar.find_all('a', href=True)
        for link in links:
            href = link.get('href', '')
            text = link.get_text(strip=True)
            if text and href and not href.startswith('#'):
                nav_items.append({'text': text, 'href': href})
    
    print(f"📱 Ditemukan {len(nav_items)} menu items:")
    for i, item in enumerate(nav_items, 1):
        print(f"  {i}. {item['text']} -> {item['href']}")
    
    print("\n📄 ANALISIS HALAMAN DETAIL")
    print("=" * 50)
    
    # Test each page in detail
    pages = [
        ('Dashboard', 'dashboard'),
        ('Personel', 'personel_ultra'), 
        ('Operations', 'operations'),
        ('Reports', 'reports'),
        ('Assignments', 'assignments'),
        ('Settings', 'settings'),
        ('Profile', 'profile'),
        ('Jabatan Management', 'jabatan_management'),
        ('Kantor', 'kantor'),
    ]
    
    successful_pages = 0
    failed_pages = 0
    
    for name, page in pages:
        try:
            response = session.get(f"{base_url}/simple_root_system.php?page={page}")
            
            print(f"\n🔍 {name}")
            print(f"   Status: {response.status_code}")
            print(f"   Size: {len(response.text)} bytes")
            
            if response.status_code == 200:
                soup = BeautifulSoup(response.text, 'html.parser')
                
                # Check for key elements
                has_title = soup.find('title') is not None
                has_content = soup.find('div', class_='container') is not None or soup.find('main') is not None
                has_cards = len(soup.find_all('div', class_='card')) > 0
                has_tables = len(soup.find_all('table')) > 0
                has_forms = len(soup.find_all('form')) > 0
                has_buttons = len(soup.find_all('button')) > 0
                
                print(f"   ✅ Title: {'Yes' if has_title else 'No'}")
                print(f"   ✅ Content: {'Yes' if has_content else 'No'}")
                print(f"   ✅ Cards: {len(soup.find_all('div', class_='card'))}")
                print(f"   ✅ Tables: {len(soup.find_all('table'))}")
                print(f"   ✅ Forms: {len(soup.find_all('form'))}")
                print(f"   ✅ Buttons: {len(soup.find_all('button'))}")
                
                # Check for errors
                page_text = response.text.lower()
                has_error = 'error' in page_text or 'fatal' in page_text or 'warning' in page_text
                
                if has_error:
                    print(f"   ⚠️  Contains errors/warnings")
                    failed_pages += 1
                else:
                    print(f"   ✅ Page loaded successfully")
                    successful_pages += 1
                    
            else:
                print(f"   ❌ Failed to load")
                failed_pages += 1
                
        except Exception as e:
            print(f"   ❌ Error: {e}")
            failed_pages += 1
    
    print(f"\n📊 SUMMARY")
    print("=" * 30)
    print(f"✅ Successful: {successful_pages}")
    print(f"❌ Failed: {failed_pages}")
    print(f"📈 Success Rate: {(successful_pages/(successful_pages+failed_pages)*100):.1f}%")
    
    print("\n🔄 AJAX CONTENT TESTING")
    print("=" * 30)
    
    # Test AJAX content loading
    ajax_pages = ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'settings']
    
    for page in ajax_pages:
        try:
            response = session.post(f"{base_url}/ajax/content.php", data={'page': page})
            
            if response.status_code == 200:
                # Try to parse as JSON
                try:
                    data = response.json()
                    if data.get('success'):
                        content = data.get('content', '')
                        print(f"✅ AJAX {page}: {len(content)} chars")
                    else:
                        print(f"❌ AJAX {page}: {data.get('message', 'Unknown error')}")
                except:
                    # Check if it's HTML instead of JSON
                    if '<html' in response.text:
                        print(f"⚠️  AJAX {page}: Returned HTML instead of JSON")
                    else:
                        print(f"❌ AJAX {page}: Invalid response")
            else:
                print(f"❌ AJAX {page}: HTTP {response.status_code}")
                
        except Exception as e:
            print(f"❌ AJAX {page}: {e}")
    
    print("\n🎯 SUPER ADMIN ACCESS VERIFICATION")
    print("=" * 40)
    
    # Test super admin specific features
    admin_features = [
        ('User Management', 'simple_root_system.php?page=users'),
        ('System Settings', 'simple_root_system.php?page=settings'),
        ('Audit Logs', 'simple_root_system.php?page=audit'),
    ]
    
    for feature, url in admin_features:
        try:
            response = session.get(f"{base_url}/{url}")
            if response.status_code == 200:
                print(f"✅ {feature}: Accessible")
            else:
                print(f"❌ {feature}: Not accessible ({response.status_code})")
        except Exception as e:
            print(f"❌ {feature}: Error - {e}")

if __name__ == "__main__":
    analyze_menu_and_content()
    print("\n🎉 ANALYSIS COMPLETED!")
