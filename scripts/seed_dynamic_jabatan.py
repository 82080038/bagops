#!/usr/bin/env python3
"""
Script untuk seeding data jabatan dinamis dari JSON ke database
Author: BAGOPS System
Date: 2026-03-02
"""

import json
import mysql.connector
from datetime import datetime
import sys

def create_dynamic_jabatan_table():
    """
    Buat tabel dynamic_jabatan jika belum ada
    """
    
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        # Create table if not exists
        create_table_query = """
        CREATE TABLE IF NOT EXISTS dynamic_jabatan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_jabatan VARCHAR(100) NOT NULL,
            kode_jabatan VARCHAR(20) UNIQUE,
            level_jabatan INT DEFAULT 3,
            parent_id INT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_nama_jabatan (nama_jabatan),
            INDEX idx_kode_jabatan (kode_jabatan),
            INDEX idx_parent_id (parent_id),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        """
        
        cursor.execute(create_table_query)
        connection.commit()
        
        print("✅ Tabel dynamic_jabatan siap")
        
        return True
        
    except Exception as e:
        print(f"❌ Error creating table: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def seed_jabatan_from_json():
    """
    Seed data jabatan dari personel.json
    """
    
    try:
        # Load personel data
        with open('/var/www/html/bagops/json/personel.json', 'r', encoding='utf-8') as f:
            personel_data = json.load(f)
        
        # Extract unique jabatans
        jabatans = {}
        personel_list = personel_data.get('data', [])
        
        print(f"📊 Memproses {len(personel_list)} personel...")
        
        for person in personel_list:
            jabatan = person.get('jabatan', '').strip()
            pangkat = person.get('pangkat', '').strip()
            
            if jabatan and jabatan != '' and jabatan != '-':
                if jabatan not in jabatans:
                    jabatans[jabatan] = {
                        'nama': jabatan,
                        'kode': generate_kode_from_nama(jabatan),
                        'level': determine_level_from_pangkat(pangkat),
                        'count': 0
                    }
                
                jabatans[jabatan]['count'] += 1
        
        print(f"📈 Ditemukan {len(jabatans)} jabatan unik")
        
        # Insert to database
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        # Get existing kodes to avoid duplicates
        cursor.execute("SELECT kode_jabatan FROM dynamic_jabatan WHERE is_active = 1")
        existing_kodes = set([row[0] for row in cursor.fetchall()])
        
        inserted_count = 0
        skipped_count = 0
        
        for jabatan_data in jabatans.values():
            # Check if already exists
            check_query = "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = %s AND is_active = 1"
            cursor.execute(check_query, (jabatan_data['nama'],))
            
            if cursor.fetchone():
                skipped_count += 1
                continue
            
            # Generate unique kode
            unique_kode = generate_kode_from_nama(jabatan_data['nama'], existing_kodes)
            existing_kodes.add(unique_kode)
            
            # Insert new jabatan
            insert_query = """
            INSERT INTO dynamic_jabatan 
            (nama_jabatan, kode_jabatan, level_jabatan, parent_id, created_by, created_at, updated_at)
            VALUES (%s, %s, %s, %s, 1, NOW(), NOW())
            """
            
            cursor.execute(insert_query, (
                jabatan_data['nama'],
                unique_kode,
                jabatan_data['level'],
                None  # parent_id
            ))
            
            inserted_count += 1
        
        connection.commit()
        
        print(f"✅ Berhasil insert: {inserted_count} jabatan")
        print(f"⏭️ Dilewati (sudah ada): {skipped_count} jabatan")
        
        return True
        
    except Exception as e:
        print(f"❌ Error seeding data: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def generate_kode_from_nama(nama, existing_kodes=None):
    """
    Generate kode dari nama jabatan dengan pengecekan duplikasi
    """
    words = nama.upper().split()
    base_kode = ''
    
    for word in words:
        if len(word) >= 3:
            base_kode += word[:3]
        else:
            base_kode += word
        
        if len(base_kode) >= 10:
            break
    
    base_kode = base_kode[:10] or 'JBT'
    
    # Check for duplicates and add suffix if needed
    if existing_kodes and base_kode in existing_kodes:
        counter = 1
        while f"{base_kode}{counter}" in existing_kodes:
            counter += 1
        return f"{base_kode}{counter}"
    
    return base_kode

def determine_level_from_pangkat(pangkat):
    """
    Tentukan level jabatan dari pangkat
    """
    if not pangkat or pangkat == '-':
        return 3  # Default level
    
    pangkat = pangkat.upper()
    
    # Level mapping berdasarkan pangkat POLRI
    level_mapping = {
        # Level 1 - Tingkat Atas
        'JENDRAL': 1, 'KOMJEN': 1, 'IRJEN': 1, 'KOMJEN POL': 1,
        
        # Level 2 - Tingkat Menengah Atas  
        'BRIGJEN': 2, 'KOMBES': 2, 'KOMBES POL': 2, 'AKBP': 2,
        
        # Level 3 - Tingkat Menengah
        'KOMPOL': 3, 'AKP': 3, 'IPDA': 3, 'AIPTU': 3,
        
        # Level 4 - Tingkat Bawah
        'AIPDA': 4, 'BRIPKA': 4, 'BRIGPOL': 4, 'BRIPTU': 4,
        
        # Level 5 - Tingkat Pelaksana
        'BRIPDA': 5, 'BRIPTU': 5,
        
        # PNS Levels
        'PENATA': 3, 'PENATA TINGKAT': 2, 'PENGADIL': 2, 'ANALIS': 3,
        'JURU': 5, 'JURU MUDA': 5, 'JURU MUDA TINGKAT': 4,
    }
    
    for key, level in level_mapping.items():
        if key in pangkat:
            return level
    
    # Default level
    return 3

def show_seeding_report():
    """
    Tampilkan laporan hasil seeding
    """
    
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        # Get statistics
        stats_query = """
        SELECT 
            COUNT(*) as total_jabatan,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_jabatan,
            COUNT(CASE WHEN level_jabatan = 1 THEN 1 END) as level_1,
            COUNT(CASE WHEN level_jabatan = 2 THEN 1 END) as level_2,
            COUNT(CASE WHEN level_jabatan = 3 THEN 1 END) as level_3,
            COUNT(CASE WHEN level_jabatan = 4 THEN 1 END) as level_4,
            COUNT(CASE WHEN level_jabatan = 5 THEN 1 END) as level_5
        FROM dynamic_jabatan
        """
        
        cursor.execute(stats_query)
        stats = cursor.fetchone()
        
        print("\n📊 LAPORAN SEEDING JABATAN DINAMIS")
        print("=" * 50)
        print(f"📈 Total Jabatan: {stats[0]}")
        print(f"✅ Jabatan Aktif: {stats[1]}")
        print(f"🔢 Level 1 (Tingkat Atas): {stats[2]}")
        print(f"🔢 Level 2 (Menengah Atas): {stats[3]}")
        print(f"🔢 Level 3 (Menengah): {stats[4]}")
        print(f"🔢 Level 4 (Bawah): {stats[5]}")
        print(f"🔢 Level 5 (Pelaksana): {stats[6]}")
        
        # Show sample data
        sample_query = """
        SELECT nama_jabatan, kode_jabatan, level_jabatan 
        FROM dynamic_jabatan 
        WHERE is_active = 1 
        ORDER BY level_jabatan, nama_jabatan 
        LIMIT 10
        """
        
        cursor.execute(sample_query)
        samples = cursor.fetchall()
        
        print("\n📋 CONTOH DATA JABATAN:")
        print("-" * 50)
        for sample in samples:
            print(f"• {sample[0]} ({sample[1]}) - Level {sample[2]}")
        
        return True
        
    except Exception as e:
        print(f"❌ Error generating report: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def main():
    """
    Main function
    """
    
    print("🔄 MEMULAI SEEDING JABATAN DINAMIS")
    print("=" * 50)
    
    # Step 1: Create table
    print("\n1. Membuat tabel dynamic_jabatan...")
    if not create_dynamic_jabatan_table():
        print("❌ Gagal membuat tabel")
        return
    
    # Step 2: Seed data
    print("\n2. Seeding data dari JSON...")
    if not seed_jabatan_from_json():
        print("❌ Gagal seeding data")
        return
    
    # Step 3: Show report
    print("\n3. Generate laporan...")
    show_seeding_report()
    
    print("\n✅ SEEDING SELESAI!")
    print("\n📁 Tabel yang dibuat: dynamic_jabatan")
    print("📊 Sumber data: personel.json")
    print("🔧 Gunakan halaman jabatan_management.php untuk mengelola data")

if __name__ == "__main__":
    main()
