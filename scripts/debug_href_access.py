#!/usr/bin/env python3
"""
Debug href access issues
Author: BAGOPS System
Date: 2026-03-02
"""

import json

def debug_href_access():
    """Debug href access issues"""
    
    with open('/var/www/html/bagops/test_results/final_href_content_test.json', 'r') as f:
        data = json.load(f)
    
    incorrect_hrefs = [h for h in data['href_tests'] if not h['correct_access']]
    
    print(f"DEBUG HREF ACCESS ISSUES")
    print("=" * 50)
    print(f"Total incorrect hrefs: {len(incorrect_hrefs)}")
    print()
    
    # Analyze first few incorrect hrefs
    for i, href in enumerate(incorrect_hrefs[:5], 1):
        print(f"{i}. {href['href_text']} from {href['source_page']}")
        print(f"   URL: {href['href_url']}")
        print(f"   HTTP Status: {href['http_status']}")
        print(f"   Content Length: {href['content_length']}")
        print(f"   Has Content: {href['has_content']}")
        print(f"   Has Error: {href['has_error']}")
        print(f"   Expected Accessible: {href['expected_accessible']}")
        print(f"   Accessible: {href['accessible']}")
        print(f"   Correct Access: {href['correct_access']}")
        print()
    
    # Check pattern
    print("PATTERN ANALYSIS:")
    print("=" * 50)
    
    # Group by source page
    by_source = {}
    for href in incorrect_hrefs:
        source = href['source_page']
        if source not in by_source:
            by_source[source] = []
        by_source[source].append(href)
    
    for source, hrefs in by_source.items():
        print(f"From {source}: {len(hrefs)} incorrect hrefs")
        for href in hrefs[:3]:
            print(f"  - {href['href_text']}: HTTP {href['http_status']}, Content {href['content_length']} bytes")
        if len(hrefs) > 3:
            print(f"  ... and {len(hrefs) - 3} more")
        print()

if __name__ == "__main__":
    debug_href_access()
