#!/usr/bin/env python3
"""
Script untuk menganalisis struktur href yang sebenarnya
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import time
from urllib.parse import urljoin, urlparse
from bs4 import BeautifulSoup

def analyze_href_structure():
    """Analisis struktur href yang sebenarnya"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    print("🔍 ANALYZING HREF STRUCTURE")
    print("=" * 50)
    
    # Test main page
    page_url = f"{base_url}/simple_root_system.php?page=dashboard"
    response = session.get(page_url)
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print(f"📄 Analyzing hrefs from: {page_url}")
    print()
    
    # Extract all links with details
    hrefs = []
    for i, link in enumerate(soup.find_all('a', href=True), 1):
        href = link['href']
        text = link.get_text(strip=True)
        classes = link.get('class', [])
        
        print(f"{i:2d}. Href: '{href}'")
        print(f"    Text: '{text}'")
        print(f"    Classes: {classes}")
        print(f"    Full Element: {str(link)[:100]}...")
        
        # Try different URL constructions
        if href.startswith('/'):
            full_url = urljoin(base_url, href)
        elif href.startswith('http'):
            full_url = href
        elif href.startswith('#') or href.startswith('javascript'):
            full_url = 'SKIP'
        else:
            # Try relative to current page
            full_url = urljoin(page_url, href)
        
        if full_url != 'SKIP':
            print(f"    Full URL: {full_url}")
            
            # Test accessibility
            try:
                test_response = session.get(full_url, timeout=3)
                print(f"    Status: {test_response.status_code}")
                if test_response.status_code == 200:
                    content_length = len(test_response.content)
                    print(f"    Content: {content_length} bytes")
            except Exception as e:
                print(f"    Error: {str(e)[:50]}")
        
        print()
        hrefs.append({
            'href': href,
            'text': text,
            'classes': classes,
            'full_url': full_url if full_url != 'SKIP' else None
        })
    
    # Analyze navigation structure
    print("🧭 NAVIGATION STRUCTURE ANALYSIS")
    print("=" * 50)
    
    # Main navigation
    navbar = soup.find('nav', class_='navbar')
    if navbar:
        nav_links = navbar.find_all('a', href=True)
        print(f"Main Navbar Links: {len(nav_links)}")
        for link in nav_links:
            href = link['href']
            text = link.get_text(strip=True)
            print(f"  • {text:20} -> '{href}'")
    
    print()
    
    # Breadcrumb
    breadcrumb = soup.find('nav', class_='breadcrumb-container')
    if breadcrumb:
        breadcrumb_links = breadcrumb.find_all('a', href=True)
        print(f"Breadcrumb Links: {len(breadcrumb_links)}")
        for link in breadcrumb_links:
            href = link['href']
            text = link.get_text(strip=True)
            print(f"  • {text:20} -> '{href}'")
    
    print()
    
    # User dropdown
    dropdown = soup.find('div', class_='dropdown-menu')
    if dropdown:
        dropdown_links = dropdown.find_all('a', href=True)
        print(f"User Dropdown Links: {len(dropdown_links)}")
        for link in dropdown_links:
            href = link['href']
            text = link.get_text(strip=True)
            print(f"  • {text:20} -> '{href}'")
    
    print()
    
    # Test correct URL patterns
    print("🔧 TESTING CORRECT URL PATTERNS")
    print("=" * 50)
    
    correct_patterns = [
        f"{base_url}/simple_root_system.php?page=dashboard",
        f"{base_url}/simple_root_system.php?page=personel",
        f"{base_url}/simple_root_system.php?page=jabatan_management",
        f"{base_url}/simple_root_system.php?page=settings",
        f"{base_url}/simple_root_system.php?page=reports",
        f"{base_url}/simple_root_system.php?page=profile",
        f"{base_url}/simple_root_system.php?page=help",
        f"{base_url}/simple_root_system.php?page=assignments",
        f"{base_url}/simple_root_system.php?page=operations",
        f"{base_url}/logout",
    ]
    
    for pattern in correct_patterns:
        try:
            response = session.get(pattern, timeout=3)
            status_icon = "✅" if response.status_code == 200 else "❌"
            print(f"{status_icon} {pattern:50} - {response.status_code}")
        except Exception as e:
            print(f"❌ {pattern:50} - ERROR: {str(e)[:30]}")
    
    return hrefs

def test_specific_hrefs():
    """Test specific href patterns"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    print("\n🎯 TESTING SPECIFIC HREF PATTERNS")
    print("=" * 50)
    
    # Test different href patterns
    test_patterns = [
        # Original hrefs from HTML
        ("dashboard", "Original dashboard"),
        ("personel", "Original personel"),
        ("profile", "Original profile"),
        ("logout", "Original logout"),
        
        # With .php
        ("dashboard.php", "With .php extension"),
        ("personel.php", "With .php extension"),
        
        # With full path
        ("/bagops/simple_root_system.php?page=dashboard", "Full path"),
        ("/bagops/simple_root_system.php?page=personel", "Full path"),
        
        # Relative to root
        ("simple_root_system.php?page=dashboard", "Relative to root"),
        ("simple_root_system.php?page=personel", "Relative to root"),
        
        # Base URL patterns
        (f"{base_url}/simple_root_system.php?page=dashboard", "Full URL"),
        (f"{base_url}/simple_root_system.php?page=personel", "Full URL"),
    ]
    
    for href, description in test_patterns:
        try:
            if href.startswith('http'):
                test_url = href
            elif href.startswith('/'):
                test_url = f"http://localhost{href}"
            else:
                test_url = f"{base_url}/{href}"
            
            response = session.get(test_url, timeout=3)
            status_icon = "✅" if response.status_code == 200 else "❌"
            print(f"{status_icon} {description:25} - {href:40} -> {response.status_code}")
            
        except Exception as e:
            print(f"❌ {description:25} - {href:40} -> ERROR: {str(e)[:30]}")

if __name__ == "__main__":
    analyze_href_structure()
    test_specific_hrefs()
    print("\n✅ HREF STRUCTURE ANALYSIS COMPLETED!")
