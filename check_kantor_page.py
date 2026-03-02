#!/usr/bin/env python3
"""
Script untuk memeriksa dan menganalisis halaman kantor
Mendeteksi masalah rendering dan memberikan solusi
"""

import requests
import json
import re
from bs4 import BeautifulSoup
import mysql.connector

def get_page_content():
    """Ambil konten halaman kantor"""
    url = "http://localhost/bagops/simple_root_system.php?page=kantor"
    
    # Session cookies
    cookies = {
        'PHPSESSID': 'test_session'
    }
    
    try:
        response = requests.get(url, cookies=cookies, timeout=10)
        if response.status_code == 200:
            return response.text
        else:
            print(f"❌ Error: Status code {response.status_code}")
            return None
    except Exception as e:
        print(f"❌ Error getting page: {e}")
        return None

def analyze_html_structure(html_content):
    """Analisis struktur HTML halaman"""
    if not html_content:
        return
    
    soup = BeautifulSoup(html_content, 'html.parser')
    
    print("🔍 Analisis Struktur HTML:")
    print("=" * 50)
    
    # Cek judul halaman
    title = soup.find('title')
    if title:
        print(f"📄 Title: {title.text.strip()}")
    
    # Cek heading
    h2 = soup.find('h2')
    if h2:
        print(f"📋 Heading: {h2.text.strip()}")
    
    # Cek table
    tables = soup.find_all('table')
    print(f"📊 Jumlah tables: {len(tables)}")
    
    for i, table in enumerate(tables):
        if 'kantorTable' in str(table.get('id', '')):
            print(f"  📈 Table {i+1}: Kantor Table (ID: {table.get('id')})")
            
            # Cek header
            thead = table.find('thead')
            if thead:
                headers = thead.find_all('th')
                print(f"    📋 Headers: {[h.text.strip() for h in headers]}")
            
            # Cek body
            tbody = table.find('tbody')
            if tbody:
                rows = tbody.find_all('tr')
                print(f"    📏 Rows: {len(rows)}")
                
                # Analisis 3 rows pertama
                for j, row in enumerate(rows[:3]):
                    cells = row.find_all('td')
                    if cells:
                        print(f"      Row {j+1}: {[cell.text.strip() for cell in cells]}")

def check_database_data():
    """Cek data di database"""
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='rootpassword',
            database='bagops_db'
        )
        cursor = conn.cursor(dictionary=True)
        
        print("\n🗄️ Analisis Database:")
        print("=" * 50)
        
        # Cek total kantor
        cursor.execute("SELECT COUNT(*) as total FROM kantor")
        total_kantor = cursor.fetchone()['total']
        print(f"📊 Total Kantor: {total_kantor}")
        
        # Cek data kantor
        cursor.execute("""
            SELECT id, nama_kantor, tipe_kantor_polisi, 
                   (SELECT COUNT(*) FROM personel p WHERE p.unit = k.nama_kantor) as jumlah_personel
            FROM kantor k 
            ORDER BY id
        """)
        kantor_data = cursor.fetchall()
        
        for kantor in kantor_data:
            print(f"  🏢 {kantor['nama_kantor']}: {kantor['jumlah_personel']} personel")
        
        # Cek pimpinan per kantor
        cursor.execute("""
            SELECT k.nama_kantor, p.nama, p.pangkat, p.nrp
            FROM kantor k
            LEFT JOIN personel p ON k.nama_kantor = p.unit
            WHERE p.pangkat IN ('AKBP', 'KOMPOL', 'AKP', 'IPTU', 'IPDA')
            AND p.is_active = 1
            ORDER BY k.nama_kantor, 
                CASE 
                    WHEN p.pangkat = 'AKBP' THEN 1
                    WHEN p.pangkat LIKE 'KOMPOL%' THEN 2
                    WHEN p.pangkat LIKE 'AKP%' THEN 2
                    WHEN p.pangkat LIKE 'IPTU%' THEN 3
                    WHEN p.pangkat LIKE 'IPDA%' THEN 3
                    ELSE 4
                END
        """)
        
        pimpinan_data = cursor.fetchall()
        current_kantor = None
        
        for pimpinan in pimpinan_data:
            if pimpinan['nama_kantor'] != current_kantor:
                current_kantor = pimpinan['nama_kantor']
                print(f"  👮 Pimpinan {current_kantor}:")
            
            if pimpinan['nama']:
                print(f"    - {pimpinan['pangkat']} {pimpinan['nama']} ({pimpinan['nrp']})")
        
        cursor.close()
        conn.close()
        
    except Exception as e:
        print(f"❌ Database error: {e}")

def check_javascript_errors(html_content):
    """Cek potensi error JavaScript"""
    if not html_content:
        return
    
    print("\n🔍 Analisis JavaScript:")
    print("=" * 50)
    
    # Cek fungsi JavaScript
    js_functions = re.findall(r'function\s+(\w+)\s*\(', html_content)
    if js_functions:
        print(f"⚙️ JavaScript functions: {js_functions}")
    
    # Cek event handlers
    onclick_handlers = re.findall(r'onclick="([^"]*)"', html_content)
    if onclick_handlers:
        print(f"🖱️ Onclick handlers: {len(onclick_handlers)}")
        for handler in onclick_handlers[:5]:  # Show first 5
            print(f"  - {handler}")
    
    # Cek DataTables
    if 'DataTables' in html_content:
        print("📊 DataTables detected")
    
    # Cek Bootstrap components
    if 'bootstrap' in html_content.lower():
        print("🎨 Bootstrap components detected")

def check_css_issues(html_content):
    """Cek masalah CSS"""
    if not html_content:
        return
    
    print("\n🎨 Analisis CSS:")
    print("=" * 50)
    
    # Cek class Bootstrap
    bootstrap_classes = re.findall(r'class="([^"]*bootstrap[^"]*)"', html_content.lower())
    if bootstrap_classes:
        print(f"🎯 Bootstrap classes found: {len(bootstrap_classes)}")
    
    # Cek table classes
    table_classes = re.findall(r'<table[^>]*class="([^"]*)"[^>]*>', html_content)
    if table_classes:
        print(f"📊 Table classes: {table_classes}")
    
    # Cek responsive elements
    responsive_elements = re.findall(r'class="[^"]*responsive[^"]*"', html_content.lower())
    if responsive_elements:
        print(f"📱 Responsive elements: {len(responsive_elements)}")

def identify_issues():
    """Identifikasi masalah dan berikan solusi"""
    print("\n🚨 Analisis Masalah & Solusi:")
    print("=" * 50)
    
    issues = []
    solutions = []
    
    # Cek halaman
    html_content = get_page_content()
    
    if not html_content:
        issues.append("❌ Halaman tidak dapat diakses")
        solutions.append("🔧 Periksa server dan routing")
        return issues, solutions
    
    # Analisis HTML
    soup = BeautifulSoup(html_content, 'html.parser')
    
    # Cek table
    kantor_table = soup.find('table', {'id': 'kantorTable'})
    if not kantor_table:
        issues.append("❌ Table kantor tidak ditemukan")
        solutions.append("🔧 Pastikan table dengan id='kantorTable' ada")
    else:
        tbody = kantor_table.find('tbody')
        if tbody:
            rows = tbody.find_all('tr')
            if len(rows) == 0:
                issues.append("❌ Table kosong")
                solutions.append("🔧 Periksa data dan query database")
            elif len(rows) == 1 and 'Belum ada data' in str(rows[0]):
                issues.append("⚠️ Tidak ada data kantor")
                solutions.append("🔧 Tambah data kantor atau periksa query")
    
    # Cek JavaScript
    if 'function editKantor' not in html_content:
        issues.append("❌ Function editKantor tidak ditemukan")
        solutions.append("🔧 Tambahkan function editKantor di JavaScript")
    
    if 'function deleteKantor' not in html_content:
        issues.append("❌ Function deleteKantor tidak ditemukan")
        solutions.append("🔧 Tambahkan function deleteKantor di JavaScript")
    
    # Cek CSS
    if 'table-sm' not in html_content:
        issues.append("⚠️ Table tidak compact")
        solutions.append("🔧 Tambahkan class 'table-sm' untuk table yang lebih compact")
    
    if 'py-1' not in html_content:
        issues.append("⚠️ Padding table terlalu besar")
        solutions.append("🔧 Tambahkan class 'py-1' untuk mengurangi padding")
    
    return issues, solutions

def generate_fixes():
    """Generate perbaikan yang diperlukan"""
    print("\n🔧 Generate Perbaikan:")
    print("=" * 50)
    
    fixes = {
        'css_fixes': [
            "Tambahkan class 'table-sm' pada table untuk compact design",
            "Tambahkan class 'py-1' pada td untuk mengurangi padding",
            "Tambahkan class 'align-middle' pada tr untuk vertical align"
        ],
        'js_fixes': [
            "Pastikan semua CRUD functions ada (create, read, update, delete)",
            "Tambahkan error handling untuk AJAX calls",
            "Pastikan DataTables initialization benar"
        ],
        'data_fixes': [
            "Pastikan query database mengembalikan data yang benar",
            "Cek mapping field database ke template",
            "Validasi data sebelum display"
        ]
    }
    
    for category, items in fixes.items():
        print(f"\n📋 {category.upper()}:")
        for item in items:
            print(f"  - {item}")

def main():
    print("🔍 Pemeriksaan Halaman Kantor")
    print("=" * 50)
    
    # 1. Ambil konten halaman
    html_content = get_page_content()
    
    # 2. Analisis struktur
    analyze_html_structure(html_content)
    
    # 3. Cek database
    check_database_data()
    
    # 4. Cek JavaScript
    check_javascript_errors(html_content)
    
    # 5. Cek CSS
    check_css_issues(html_content)
    
    # 6. Identifikasi masalah
    issues, solutions = identify_issues()
    
    if issues:
        print(f"\n🚨 Ditemukan {len(issues)} masalah:")
        for issue in issues:
            print(f"  {issue}")
        
        print(f"\n💡 Solusi yang disarankan:")
        for solution in solutions:
            print(f"  {solution}")
    else:
        print("\n✅ Tidak ada masalah yang ditemukan")
    
    # 7. Generate perbaikan
    generate_fixes()

if __name__ == "__main__":
    main()
