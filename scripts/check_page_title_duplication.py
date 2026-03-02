#!/usr/bin/env python3
"""
Check for page title duplication across all pages
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup
import re

def check_page_title_duplication():
    """Check for page title duplication across all pages"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    print("\n🔍 CHECKING PAGE TITLE DUPLICATION")
    print("=" * 60)
    
    # Pages to check
    pages_to_check = [
        'dashboard',
        'personel_ultra', 
        'jabatan_management',
        'operations',
        'reports',
        'assignments',
        'settings',
        'profile',
        'help'
    ]
    
    duplication_results = []
    
    for page in pages_to_check:
        print(f"\n📋 CHECKING: {page}")
        print("-" * 40)
        
        try:
            page_url = f"{base_url}/simple_root_system.php?page={page}"
            response = session.get(page_url, timeout=10)
            
            if response.status_code == 200:
                soup = BeautifulSoup(response.content, 'html.parser')
                
                # Find all title elements
                title_elements = []
                
                # Look for h1, h2, h3, h4, h5, h6 with page-related content
                for tag in ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']:
                    elements = soup.find_all(tag)
                    for elem in elements:
                        text = elem.get_text(strip=True)
                        if text and len(text) > 3:  # Skip very short texts
                            # Check if it's a page title (contains page name or common title words)
                            page_keywords = ['dashboard', 'personel', 'jabatan', 'data', 'manajemen', 'laporan', 'tugas', 'pengaturan', 'profile', 'bantuan']
                            if any(keyword.lower() in text.lower() for keyword in page_keywords) or 'page' in text.lower():
                                title_elements.append({
                                    'tag': tag,
                                    'text': text,
                                    'class': elem.get('class', []),
                                    'parent_class': elem.parent.get('class', []) if elem.parent else []
                                })
                
                print(f"   Found {len(title_elements)} title elements:")
                
                if len(title_elements) > 1:
                    print(f"   ⚠️ POTENTIAL DUPLICATION:")
                    for i, elem in enumerate(title_elements, 1):
                        print(f"      {i}. <{elem['tag']}> class={elem['class']} text='{elem['text'][:50]}...'")
                    
                    duplication_results.append({
                        'page': page,
                        'count': len(title_elements),
                        'elements': title_elements,
                        'has_duplication': True
                    })
                elif len(title_elements) == 1:
                    elem = title_elements[0]
                    print(f"   ✅ SINGLE TITLE: <{elem['tag']}> '{elem['text']}'")
                    duplication_results.append({
                        'page': page,
                        'count': 1,
                        'elements': [elem],
                        'has_duplication': False
                    })
                else:
                    print(f"   ⚠️ NO TITLES FOUND")
                    duplication_results.append({
                        'page': page,
                        'count': 0,
                        'elements': [],
                        'has_duplication': False
                    })
                
            else:
                print(f"   ❌ HTTP {response.status_code}")
                duplication_results.append({
                    'page': page,
                    'count': 0,
                    'elements': [],
                    'has_duplication': False,
                    'error': response.status_code
                })
        
        except Exception as e:
            print(f"   ❌ Error: {str(e)[:50]}")
            duplication_results.append({
                'page': page,
                'count': 0,
                'elements': [],
                'has_duplication': False,
                'error': str(e)
            })
    
    # Summary
    print(f"\n📊 DUPLICATION SUMMARY")
    print("=" * 60)
    
    pages_with_duplication = [r for r in duplication_results if r.get('has_duplication')]
    pages_without_duplication = [r for r in duplication_results if not r.get('has_duplication') and not r.get('error')]
    pages_with_error = [r for r in duplication_results if r.get('error')]
    
    print(f"Total pages checked: {len(pages_to_check)}")
    print(f"Pages with duplication: {len(pages_with_duplication)}")
    print(f"Pages without duplication: {len(pages_without_duplication)}")
    print(f"Pages with errors: {len(pages_with_error)}")
    
    if pages_with_duplication:
        print(f"\n⚠️ PAGES WITH DUPLICATION:")
        for result in pages_with_duplication:
            print(f"   - {result['page']}: {result['count']} titles")
    
    if pages_without_duplication:
        print(f"\n✅ PAGES WITHOUT DUPLICATION:")
        for result in pages_without_duplication:
            if result['count'] == 1:
                elem = result['elements'][0]
                print(f"   - {result['page']}: 1 title ('{elem['text'][:30]}...')")
            else:
                print(f"   - {result['page']}: No titles")
    
    if pages_with_error:
        print(f"\n❌ PAGES WITH ERRORS:")
        for result in pages_with_error:
            print(f"   - {result['page']}: {result['error']}")
    
    # Recommendations
    print(f"\n🔧 RECOMMENDATIONS")
    print("=" * 60)
    
    if pages_with_duplication:
        print("Pages with title duplication need fixes:")
        for result in pages_with_duplication:
            print(f"   - {result['page']}: Remove duplicate titles, keep only one")
    
    if pages_without_duplication:
        print("Pages without duplication are good:")
        for result in pages_without_duplication:
            if result['count'] == 1:
                print(f"   - {result['page']}: Title is unique")
    
    print(f"\n✅ PAGE TITLE DUPLICATION CHECK COMPLETED!")

if __name__ == "__main__":
    check_page_title_duplication()
