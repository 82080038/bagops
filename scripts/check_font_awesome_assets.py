#!/usr/bin/env python3
"""
Check Font Awesome assets and fix missing files
Author: BAGOPS System
Date: 2026-03-02
"""

import requests
import os
from urllib.parse import urlparse

def check_font_awesome_assets():
    """Check Font Awesome assets and fix missing files"""
    
    base_url = "http://localhost/bagops"
    
    print("🔍 CHECKING FONT AWESOME ASSETS")
    print("=" * 50)
    
    # Test 1: Check Font Awesome CSS
    print("\n📋 TEST 1: Font Awesome CSS")
    print("-" * 40)
    
    try:
        css_response = requests.get(f"{base_url}/assets/css/fontawesome.min.css", timeout=10)
        
        if css_response.status_code == 200:
            print("✅ Font Awesome CSS accessible")
            css_content = css_response.text
            
            # Extract font URLs from CSS
            print(f"📄 CSS size: {len(css_content)} bytes")
            
            # Look for font file references
            import re
            
            # Pattern to find font file URLs
            font_patterns = [
                r'url\(["\']?([^"\')]+\.(?:woff2|woff|ttf|eot|svg))["\']?\)',
                r'src:\s*url\(["\']?([^"\')]+\.(?:woff2|woff|ttf|eot|svg))["\']?\)',
                r'@font-face[^}]+src:[^;]+url\(["\']?([^"\')]+\.(?:woff2|woff|ttf|eot|svg))["\']?\)'
            ]
            
            font_files = set()
            for pattern in font_patterns:
                matches = re.findall(pattern, css_content, re.IGNORECASE)
                font_files.update(matches)
            
            print(f"📋 Font files referenced in CSS: {len(font_files)}")
            
            for font_file in sorted(font_files):
                print(f"   - {font_file}")
            
            # Test each font file
            print(f"\n📋 TEST 2: Font File Accessibility")
            print("-" * 40)
            
            missing_fonts = []
            accessible_fonts = []
            
            for font_file in font_files:
                # Handle relative URLs
                if font_file.startswith('../'):
                    font_url = f"{base_url}/assets/{font_file[3:]}"
                elif font_file.startswith('/'):
                    font_url = f"{base_url}{font_file}"
                elif font_file.startswith('http'):
                    font_url = font_file
                else:
                    font_url = f"{base_url}/assets/webfonts/{font_file}"
                
                try:
                    font_response = requests.get(font_url, timeout=5)
                    if font_response.status_code == 200:
                        accessible_fonts.append((font_file, font_url, len(font_response.content)))
                        print(f"✅ {font_file} - {len(font_response.content)} bytes")
                    else:
                        missing_fonts.append((font_file, font_url, font_response.status_code))
                        print(f"❌ {font_file} - HTTP {font_response.status_code}")
                except Exception as e:
                    missing_fonts.append((font_file, font_url, str(e)))
                    print(f"❌ {font_file} - Error: {str(e)[:50]}")
            
            # Summary
            print(f"\n📊 FONT ASSETS SUMMARY")
            print("-" * 40)
            print(f"Total fonts referenced: {len(font_files)}")
            print(f"Accessible: {len(accessible_fonts)}")
            print(f"Missing: {len(missing_fonts)}")
            
            if missing_fonts:
                print(f"\n⚠️ MISSING FONT FILES:")
                for font_file, font_url, error in missing_fonts:
                    print(f"   - {font_file} ({font_url}) - {error}")
                
                # Try to download missing fonts
                print(f"\n🔧 ATTEMPTING TO DOWNLOAD MISSING FONTS")
                print("-" * 40)
                
                download_missing_fonts(base_url, missing_fonts)
            
            # Check if we need to create webfonts directory
            check_webfonts_directory(base_url)
            
        else:
            print(f"❌ Font Awesome CSS not accessible: {css_response.status_code}")
    
    except Exception as e:
        print(f"❌ Error checking Font Awesome: {str(e)}")
    
    print(f"\n✅ FONT AWESOME ASSETS CHECK COMPLETED!")

def download_missing_fonts(base_url, missing_fonts):
    """Download missing font files"""
    
    base_dir = "/var/www/html/bagops"
    
    # Font Awesome CDN URLs
    font_awesome_cdn = {
        "fa-solid-900.woff2": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2",
        "fa-solid-900.ttf": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.ttf",
        "fa-regular-400.woff2": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-regular-400.woff2",
        "fa-regular-400.ttf": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-regular-400.ttf",
        "fa-brands-400.woff2": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-brands-400.woff2",
        "fa-brands-400.ttf": "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-brands-400.ttf"
    }
    
    # Create webfonts directory
    webfonts_dir = f"{base_dir}/assets/webfonts"
    os.makedirs(webfonts_dir, exist_ok=True)
    
    downloaded_count = 0
    
    for font_file, font_url, error in missing_fonts:
        font_name = font_file.split('/')[-1]
        
        if font_name in font_awesome_cdn:
            cdn_url = font_awesome_cdn[font_name]
            local_path = f"{webfonts_dir}/{font_name}"
            
            try:
                print(f"📥 Downloading {font_name}...")
                response = requests.get(cdn_url, timeout=30)
                response.raise_for_status()
                
                with open(local_path, 'wb') as f:
                    f.write(response.content)
                
                print(f"   ✅ Downloaded {len(response.content)} bytes to {local_path}")
                downloaded_count += 1
                
            except Exception as e:
                print(f"   ❌ Failed to download {font_name}: {str(e)}")
        else:
            print(f"⚠️ No CDN URL found for {font_name}")
    
    if downloaded_count > 0:
        print(f"\n✅ Successfully downloaded {downloaded_count} font files")
    else:
        print(f"\n❌ No font files were downloaded")

def check_webfonts_directory(base_url):
    """Check and create webfonts directory if needed"""
    
    base_dir = "/var/www/html/bagops"
    webfonts_dir = f"{base_dir}/assets/webfonts"
    
    print(f"\n📁 CHECKING WEBFONTS DIRECTORY")
    print("-" * 40)
    
    if os.path.exists(webfonts_dir):
        print(f"✅ Webfonts directory exists: {webfonts_dir}")
        
        # List existing font files
        try:
            font_files = [f for f in os.listdir(webfonts_dir) if f.endswith(('.woff2', '.woff', '.ttf', '.eot', '.svg'))]
            print(f"📋 Existing font files: {len(font_files)}")
            
            for font_file in sorted(font_files):
                file_path = os.path.join(webfonts_dir, font_file)
                file_size = os.path.getsize(file_path)
                print(f"   - {font_file} ({file_size} bytes)")
                
        except Exception as e:
            print(f"❌ Error listing webfonts directory: {str(e)}")
    else:
        print(f"❌ Webfonts directory does not exist: {webfonts_dir}")
        
        # Create it
        try:
            os.makedirs(webfonts_dir, exist_ok=True)
            print(f"✅ Created webfonts directory: {webfonts_dir}")
        except Exception as e:
            print(f"❌ Failed to create webfonts directory: {str(e)}")

if __name__ == "__main__":
    check_font_awesome_assets()
