#!/usr/bin/env python3
"""
Test personel card removal to eliminate duplication
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
from bs4 import BeautifulSoup

def test_card_removal():
    """Test personel card removal to eliminate duplication"""
    
    base_url = "http://localhost/bagops"
    session = requests.Session()
    
    # Login
    print("🔐 Login sebagai super_admin...")
    login_data = {'username': 'super_admin', 'password': 'admin123'}
    session.post(f"{base_url}/login.php", data=login_data)
    
    # Get personel page
    print("📄 Mengambil halaman personel_ultra...")
    page_url = f"{base_url}/simple_root_system.php?page=personel_ultra"
    response = session.get(page_url, timeout=10)
    
    print(f"📊 HTTP Status: {response.status_code}")
    
    # Parse HTML
    soup = BeautifulSoup(response.content, 'html.parser')
    
    print("\n🔍 TESTING PERSONEL CARD REMOVAL")
    print("=" * 60)
    
    # Check if Total Personel card exists
    print(f"\n📋 CHECKING TOTAL PERSONEL CARD")
    print("-" * 40)
    
    total_card_found = False
    
    # Look for the specific card
    cards = soup.find_all('div', class_='card')
    for card in cards:
        card_text = card.get_text(strip=True)
        if 'Total Personel' in card_text:
            total_card_found = True
            print("❌ Total Personel card still found")
            print(f"   Card content: {card_text[:100]}...")
            break
    
    if not total_card_found:
        print("✅ Total Personel card successfully removed")
    
    # Check header still exists
    print(f"\n📋 CHECKING TABLE HEADER")
    print("-" * 40)
    
    header = None
    headers = soup.find_all('h6', class_='font-weight-bold text-primary')
    for h in headers:
        if 'Daftar Personel' in h.get_text():
            header = h
            break
    
    if header:
        header_text = header.get_text(strip=True)
        print(f"✅ Table header found: '{header_text}'")
        
        # Extract count
        import re
        count_match = re.search(r'\((\d+(?:,\d+)*) records\)', header_text)
        if count_match:
            header_count = count_match.group(1)
            print(f"✅ Header count: {header_count}")
        else:
            print("⚠️ Could not extract count from header")
    else:
        print("❌ Table header not found")
    
    # Check page structure
    print(f"\n📋 CHECKING PAGE STRUCTURE")
    print("-" * 40)
    
    # Count cards on page
    all_cards = soup.find_all('div', class_='card')
    print(f"Total cards found: {len(all_cards)}")
    
    # Describe remaining cards
    for i, card in enumerate(all_cards, 1):
        card_classes = card.get('class', [])
        card_text = card.get_text(strip=True)[:50]
        print(f"   Card {i}: class={card_classes}, text='{card_text}...'")
    
    # Check main content area
    main_content = soup.find('main') or soup.find('div', {'id': 'main'})
    if main_content:
        print(f"\n✅ Main content area found")
        
        # Check for table
        table = main_content.find('table', {'id': 'personelTable'})
        if table:
            print("✅ Personel table found in main content")
        else:
            print("❌ Personel table not found")
    
    # Verify no duplication
    print(f"\n📋 VERIFICATION: NO DUPLICATION")
    print("-" * 40)
    
    duplication_found = False
    
    # Check for any element showing "257" besides header
    elements_with_count = soup.find_all(text=lambda text: text and '257' in text)
    
    header_count_elements = 0
    other_count_elements = 0
    
    for elem in elements_with_count:
        parent = elem.parent
        if parent and parent.name == 'h6' and 'Daftar Personel' in parent.get_text():
            header_count_elements += 1
        else:
            other_count_elements += 1
            if parent:
                parent_classes = parent.get('class', [])
                parent_tag = parent.name
                print(f"⚠️ Found '257' in: <{parent_tag}> class={parent_classes}")
                duplication_found = True
    
    print(f"Header count elements: {header_count_elements}")
    print(f"Other count elements: {other_count_elements}")
    
    if not duplication_found and other_count_elements == 0:
        print("✅ No duplication found - perfect!")
    elif duplication_found:
        print("❌ Duplication still exists")
    else:
        print("⚠️ Some other elements may contain count")
    
    # Summary
    print(f"\n📊 CARD REMOVAL SUMMARY")
    print("=" + 60)
    
    print(f"Total Personel Card: {'❌ Still exists' if total_card_found else '✅ Removed'}")
    print(f"Table Header: {'✅ Found' if header else '❌ Missing'}")
    print(f"Total Cards: {len(all_cards)}")
    print(f"Duplication: {'❌ Found' if duplication_found else '✅ None'}")
    
    print(f"\n🎯 EXPECTED BEHAVIOR")
    print("=" + 60)
    print("After card removal:")
    print("1. ✅ No Total Personel card")
    print("2. ✅ Only table header shows count")
    print("3. ✅ No duplication of information")
    print("4. ✅ Cleaner, less cluttered UI")
    
    print(f"\n✅ PERSONEL CARD REMOVAL TEST COMPLETED!")

if __name__ == "__main__":
    test_card_removal()
