#!/usr/bin/env python3
"""
Find unclosed LI tags in the HTML
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import re
from bs4 import BeautifulSoup

def find_unclosed_tags():
    """Find unclosed LI tags in the HTML"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Get dashboard page
    print("📄 Mengambil halaman dashboard...")
    page_url = f"{base_url}/simple_root_system.php?page=dashboard"
    response = session.get(page_url, timeout=10)
    
    html_content = response.text
    
    print("\n🔍 FINDING UNCLOSED LI TAGS")
    print("=" * 50)
    
    # Find all LI tags with their positions
    li_open_pattern = r'<li[^>]*>'
    li_close_pattern = r'</li>'
    
    li_opens = []
    li_closes = []
    
    # Find opening LI tags
    for match in re.finditer(li_open_pattern, html_content):
        li_opens.append({
            'pos': match.start(),
            'tag': match.group(),
            'line': html_content[:match.start()].count('\n') + 1
        })
    
    # Find closing LI tags
    for match in re.finditer(li_close_pattern, html_content):
        li_closes.append({
            'pos': match.start(),
            'tag': match.group(),
            'line': html_content[:match.start()].count('\n') + 1
        })
    
    print(f"📊 Found {len(li_opens)} opening LI tags")
    print(f"📊 Found {len(li_closes)} closing LI tags")
    
    # Show opening LI tags
    print(f"\n📋 OPENING LI TAGS:")
    print("-" * 40)
    for i, li in enumerate(li_opens[:10], 1):  # Show first 10
        # Get context around the tag
        start_pos = max(0, li['pos'] - 50)
        end_pos = min(len(html_content), li['pos'] + 100)
        context = html_content[start_pos:end_pos].replace('\n', ' ').strip()
        print(f"   {i:2d}. Line {li['line']:3d}: {li['tag']}")
        print(f"       Context: ...{context}...")
    
    if len(li_opens) > 10:
        print(f"   ... and {len(li_opens) - 10} more")
    
    # Show closing LI tags
    print(f"\n📋 CLOSING LI TAGS:")
    print("-" * 40)
    for i, li in enumerate(li_closes[:10], 1):  # Show first 10
        # Get context around the tag
        start_pos = max(0, li['pos'] - 50)
        end_pos = min(len(html_content), li['pos'] + 50)
        context = html_content[start_pos:end_pos].replace('\n', ' ').strip()
        print(f"   {i:2d}. Line {li['line']:3d}: {li['tag']}")
        print(f"       Context: ...{context}...")
    
    if len(li_closes) > 10:
        print(f"   ... and {len(li_closes) - 10} more")
    
    # Find potential unclosed tags
    print(f"\n🔍 ANALYZING TAG STRUCTURE:")
    print("-" * 40)
    
    # Use BeautifulSoup to parse and find issues
    soup = BeautifulSoup(html_content, 'html.parser')
    
    # Find all LI elements
    all_lis = soup.find_all('li')
    print(f"📊 BeautifulSoup found {len(all_lis)} LI elements")
    
    # Check each LI for proper structure
    problematic_lis = []
    
    for i, li in enumerate(all_lis):
        # Get the HTML of this LI
        li_html = str(li)
        
        # Check if it's self-closed (which is wrong for LI)
        if li_html.endswith('/>'):
            problematic_lis.append({
                'index': i,
                'html': li_html,
                'issue': 'Self-closed tag'
            })
        # Check if it has unclosed children
        elif li_html.count('<') > li_html.count('</') + 1:
            problematic_lis.append({
                'index': i,
                'html': li_html[:100] + '...' if len(li_html) > 100 else li_html,
                'issue': 'Unclosed children'
            })
    
    if problematic_lis:
        print(f"⚠️ Found {len(problematic_lis)} problematic LI tags:")
        for li in problematic_lis[:5]:  # Show first 5
            print(f"   LI {li['index']}: {li['issue']}")
            print(f"   HTML: {li['html']}")
            print()
    else:
        print("✅ No obvious LI structure issues found")
    
    # Look for specific patterns that might cause issues
    print(f"\n🔍 COMMON LI ISSUES:")
    print("-" * 40)
    
    # Check for LI tags with missing closing
    lines = html_content.split('\n')
    for line_num, line in enumerate(lines, 1):
        if '<li' in line and '</li>' not in line and line.strip().endswith('>'):
            # This might be an unclosed LI
            if 'dropdown' in line.lower() or 'nav' in line.lower():
                print(f"⚠️ Line {line_num}: Possible unclosed LI in navigation")
                print(f"   {line.strip()}")
    
    print(f"\n✅ UNCLOSED TAGS ANALYSIS COMPLETED!")

if __name__ == "__main__":
    find_unclosed_tags()
