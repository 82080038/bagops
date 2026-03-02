#!/usr/bin/env python3
"""
Script untuk memperbaiki parent_id jabatan berdasarkan hierarki organisasi POLRES
Author: BAGOPS System
Date: 2026-03-02
"""

import mysql.connector
import json
from collections import defaultdict

def load_jabatan_hierarchy_rules():
    """
    Load aturan hierarki jabatan POLRES
    """
    
    return {
        # Level 1 (Tingkat Atas) - Tidak punya parent
        'level_1': [],
        
        # Level 2 (Menengah Atas) - Parent dari Level 1
        'level_2': {
            'KAPOLRES SAMOSIR': None,  # Top level, no parent
        },
        
        # Level 3 (Menengah) - Parent dari Level 2
        'level_3': {
            # Wakil Kapolres
            'WAKAPOLRES': 'KAPOLRES SAMOSIR',
            'ADC KAPOLRES': 'KAPOLRES SAMOSIR',
            
            # Kepala Bagian (KABAG)
            'KABAG OPS': 'KAPOLRES SAMOSIR',
            'KABAG REN': 'KAPOLRES SAMOSIR', 
            'KABAG SDM': 'KAPOLRES SAMOSIR',
            'KABAG LOG': 'KAPOLRES SAMOSIR',
            'KABAG SUMDA': 'KAPOLRES SAMOSIR',
            
            # Kepala Satuan (KASAT)
            'KASAT RESKRIM': 'KAPOLRES SAMOSIR',
            'KASAT INTELKAM': 'KAPOLRES SAMOSIR',
            'KASAT LANTAS': 'KAPOLRES SAMOSIR',
            'KASAT SAMAPTA': 'KAPOLRES SAMOSIR',
            'KASAT NARKOBA': 'KAPOLRES SAMOSIR',
            'KASAT POLAIR': 'KAPOLRES SAMOSIR',
            'KASAT TAHTI': 'KAPOLRES SAMOSIR',
            'KASAT PAMOBVIT': 'KAPOLRES SAMOSIR',
            'KASAT BINMAS': 'KAPOLRES SAMOSIR',
            'KASAT SIBBAINMASPOL': 'KAPOLRES SAMOSIR',
            'KASAT SIUM': 'KAPOLRES SAMOSIR',
            
            # Kepala Seksi (KASI)
            'KASI RESKRIM': 'KASAT RESKRIM',
            'KASI INTELKAM': 'KASAT INTELKAM',
            'KASI LANTAS': 'KASAT LANTAS',
            'KASI SAMAPTA': 'KASAT SAMAPTA',
            'KASI NARKOBA': 'KASAT NARKOBA',
            'KASI POLAIR': 'KASAT POLAIR',
            'KASI TAHTI': 'KASAT TAHTI',
            'KASI PAMOBVIT': 'KASAT PAMOBVIT',
            'KASI BINMAS': 'KASAT BINMAS',
            
            # Kepala Polsek
            'KAPOLSEK ONANRUNGGU': 'KAPOLRES SAMOSIR',
            'KAPOLSEK PALIPI': 'KAPOLRES SAMOSIR',
            'KAPOLSEK PANGURURAN': 'KAPOLRES SAMOSIR',
            'KAPOLSEK SIMANINDO': 'KAPOLRES SAMOSIR',
            'KAPOLSEK HARIAN BOHO': 'KAPOLRES SAMOSIR',
            
            # Ka SPKT
            'KA SPKT': 'KAPOLRES SAMOSIR',
            
            # ASN positions
            'ASN BAG OPS': 'KABAG OPS',
            'ASN BAG REN': 'KABAG REN',
            'ASN BAG SDM': 'KABAG SDM',
            'ASN BAG LOG': 'KABAG LOG',
        },
        
        # Level 4 (Bawah) - Parent dari Level 3
        'level_4': {
            # Kanit (Kepala Unit Intel)
            'KANIT INTELKAM': 'KASAT INTELKAM',
            'KANIT RESKRIM': 'KASAT RESKRIM',
            'KANIT LANTAS': 'KASAT LANTAS',
            'KANIT SAMAPTA': 'KASAT SAMAPTA',
            'KANIT NARKOBA': 'KASAT NARKOBA',
            'KANIT POLAIR': 'KASAT POLAIR',
            'KANIT TAHTI': 'KASAT TAHTI',
            'KANIT PAMOBVIT': 'KASAT PAMOBVIT',
            'KANIT BINMAS': 'KASAT BINMAS',
            'KANITIDIK 1': 'KASAT INTELKAM',
            'KANITIDIK 2': 'KASAT INTELKAM',
            'KANITIDIK 3': 'KASAT INTELKAM',
            'KANITIDIK 4': 'KASAT INTELKAM',
            'KANITIDIK 5': 'KASAT INTELKAM',
            'KANITREGIDENT LANTAS': 'KASAT LANTAS',
            
            # PS. (Penjabat Sementara) positions
            'PS. KASUBBAGBINKARPOL': 'KABAG OPS',
            'PS. KASUBBAGBINKARPOL': 'KABAG OPS',
            'PS. KASUBBAGBINKARPOL': 'KABAG OPS',
            'PS. KA SPKT 1': 'KA SPKT',
            'PS. KA SPKT 2': 'KA SPKT',
            'PS. KA SPKT 3': 'KA SPKT',
            'PS. KANIT PROPAM': 'KASAT INTELKAM',
            'PS. KANIT BINMAS': 'KASAT BINMAS',
            'PS. KANIT INTELKAM': 'KASAT INTELKAM',
            'PS. KANIT LANTAS': 'KASAT LANTAS',
            'PS. KANIT RESKRIM': 'KASAT RESKRIM',
            'PS. KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS. KANIT TAHTI': 'KASAT TAHTI',
            'PS. PAUR SUBBAGBINOPS': 'KABAG OPS',
            'PS. PAUR SUBBAGRENMIN': 'KABAG REN',
            'PS. PAUR SUBBAGSUMDA': 'KABAG SUMDA',
            'PS. PAUR SUBBAGSIMAPOL': 'KASAT SAMAPTA',
            'PS. PAUR SUBBAGBINPOL': 'KASAT BINMAS',
            'PS. PAUR SUBBAGBINKARPOL': 'KABAG OPS',
            'PS. PAUR SUBBAGSIKDOK': 'KABAG SDM',
            'PS. PAUR SUBBAGLOG': 'KABAG LOG',
            'PS. PAUR SUBBAGRENMIN': 'KABAG REN',
            
            # Wakapolsek
            'WAKAPOLSEK ONANRUNGGU': 'KAPOLSEK ONANRUNGGU',
            'WAKAPOLSEK PALIPI': 'KAPOLSEK PALIPI',
            'WAKAPOLSEK PANGURURAN': 'KAPOLSEK PANGURURAN',
            'WAKAPOLSEK SIMANINDO': 'KAPOLSEK SIMANINDO',
            'WAKAPOLSEK HARIAN BOHO': 'KAPOLSEK HARIAN BOHO',
        },
        
        # Level 5 (Pelaksana) - Parent dari Level 4
        'level_5': {
            # BA (Bawahan) positions
            'BA MIN BAG OPS': 'KABAG OPS',
            'BA MIN BAG REN': 'KABAG REN',
            'BA MIN BAG SDM': 'KABAG SDM',
            'BA MIN BAG LOG': 'KABAG LOG',
            'BA MIN BAG SUMDA': 'KABAG SUMDA',
            'BA POLRES SAMOSIR': 'KAPOLRES SAMOSIR',
            'BA SIDOKKES': 'KABAG SDM',
            'BA PEMBINAAN': 'KABAG SDM',
            'BAMIN PAMAPTA 1': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 2': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 3': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 4': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 5': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 6': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 7': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 8': 'KASAT PAMOBVIT',
            
            # Bintara positions
            'BINTARA ADMINISTRASI': 'KABAG OPS',
            'BINTARA POLSEK': 'KAPOLSEK ONANRUNGGU',  # Will be updated per polsek
            'BINTARA SAT PAMOBVIT': 'KASAT PAMOBVIT',
            'BINTARA SATLANTAS': 'KASAT LANTAS',
            'BINTARA SAT RESKRIM': 'KASAT RESKRIM',
            'BINTARA SAT INTELKAM': 'KASAT INTELKAM',
            'BINTARA SAT SAMAPTA': 'KASAT SAMAPTA',
            'BINTARA SAT NARKOBA': 'KASAT NARKOBA',
            'BINTARA SAT POLAIR': 'KASAT POLAIR',
            'BINTARA SAT TAHTI': 'KASAT TAHTI',
            'BINTARA SAT BINMAS': 'KASAT BINMAS',
            'BINTARA SIUM': 'KASAT SIUM',
            
            # Other positions
            'BINTARA BAG OPS': 'KABAG OPS',
            'BINTARA BAG REN': 'KABAG REN',
            'BINTARA BAG SDM': 'KABAG SDM',
            'BINTARA BAG LOG': 'KABAG LOG',
            'BINTARA BAG SUMDA': 'KABAG SUMDA',
        }
    }

def get_jabatan_mapping():
    """
    Mapping jabatan dari database ke format standar
    """
    
    return {
        # Variations and abbreviations
        'KAPOLRES': 'KAPOLRES SAMOSIR',
        'WAKAPOLRES': 'WAKAPOLRES',
        'KABAG OPS': 'KABAG OPS',
        'KABAG REN': 'KABAG REN',
        'KABAG SDM': 'KABAG SDM',
        'KABAG LOG': 'KABAG LOG',
        'KABAG SUMDA': 'KABAG SUMDA',
        'KASAT RESKRIM': 'KASAT RESKRIM',
        'KASAT INTELKAM': 'KASAT INTELKAM',
        'KASAT LANTAS': 'KASAT LANTAS',
        'KASAT SAMAPTA': 'KASAT SAMAPTA',
        'KASAT NARKOBA': 'KASAT NARKOBA',
        'KASAT POLAIR': 'KASAT POLAIR',
        'KASAT TAHTI': 'KASAT TAHTI',
        'KASAT PAMOBVIT': 'KASAT PAMOBVIT',
        'KASAT BINMAS': 'KASAT BINMAS',
        'KASAT SIUM': 'KASAT SIUM',
        'KAPOLSEK': 'KAPOLSEK ONANRUNGGU',  # Default, will be updated per polsek
        'KA SPKT': 'KA SPKT',
        'KANIT RESKRIM': 'KANIT RESKRIM',
        'KANIT INTELKAM': 'KANIT INTELKAM',
        'KANIT LANTAS': 'KANIT LANTAS',
        'KANIT SAMAPTA': 'KANIT SAMAPTA',
        'KANIT NARKOBA': 'KANIT NARKOBA',
        'KANIT POLAIR': 'KANIT POLAIR',
        'KANIT TAHTI': 'KANIT TAHTI',
        'KANIT PAMOBVIT': 'KANIT PAMOBVIT',
        'KANIT BINMAS': 'KANIT BINMAS',
        'BA MIN BAG OPS': 'BA MIN BAG OPS',
        'BA MIN BAG REN': 'BA MIN BAG REN',
        'BA MIN BAG SDM': 'BA MIN BAG SDM',
        'BA MIN BAG LOG': 'BA MIN BAG LOG',
        'BA MIN BAG SUMDA': 'BA MIN BAG SUMDA',
        'BINTARA POLSEK': 'BINTARA POLSEK',
        'BINTARA SAT PAMOBVIT': 'BINTARA SAT PAMOBVIT',
        'BINTARA SATLANTAS': 'BINTARA SATLANTAS',
        'BINTARA SAT RESKRIM': 'BINTARA SAT RESKRIM',
        'BINTARA SAT INTELKAM': 'BINTARA SAT INTELKAM',
        'BINTARA SAT SAMAPTA': 'BINTARA SAT SAMAPTA',
        'BINTARA SAT NARKOBA': 'BINTARA SAT NARKOBA',
        'BINTARA SAT POLAIR': 'BINTARA SAT POLAIR',
        'BINTARA SAT TAHTI': 'BINTARA SAT TAHTI',
        'BINTARA SAT BINMAS': 'BINTARA SAT BINMAS',
        'BINTARA SIUM': 'BINTARA SIUM',
    }

def update_jabatan_hierarchy():
    """
    Update parent_id untuk semua jabatan
    """
    
    try:
        # Load hierarchy rules
        hierarchy = load_jabatan_hierarchy_rules()
        mapping = get_jabatan_mapping()
        
        # Connect to database
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        # Get all jabatan
        cursor.execute("SELECT id, nama_jabatan, level_jabatan FROM dynamic_jabatan WHERE is_active = 1")
        jabatans = cursor.fetchall()
        
        print(f"📊 Memproses {len(jabatans)} jabatan...")
        
        updated_count = 0
        not_found_count = 0
        
        for jabatan_id, nama_jabatan, level_jabatan in jabatans:
            parent_nama = None
            
            # Normalize jabatan name
            normalized_nama = mapping.get(nama_jabatan, nama_jabatan)
            
            # Find parent based on level
            if level_jabatan == 2:
                parent_nama = hierarchy['level_2'].get(normalized_nama)
            elif level_jabatan == 3:
                parent_nama = hierarchy['level_3'].get(normalized_nama)
            elif level_jabatan == 4:
                parent_nama = hierarchy['level_4'].get(normalized_nama)
            elif level_jabatan == 5:
                parent_nama = hierarchy['level_5'].get(normalized_nama)
            
            # Special handling for BINTARA POLSEK - assign to appropriate KAPOLSEK
            if normalized_nama == 'BINTARA POLSEK':
                # This will be handled separately based on personel assignment
                parent_nama = 'KAPOLRES SAMOSIR'  # Default for now
            
            if parent_nama:
                # Find parent ID
                cursor.execute(
                    "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = %s AND is_active = 1",
                    (parent_nama,)
                )
                parent_result = cursor.fetchone()
                
                if parent_result:
                    parent_id = parent_result[0]
                    
                    # Update parent_id
                    cursor.execute(
                        "UPDATE dynamic_jabatan SET parent_id = %s WHERE id = %s",
                        (parent_id, jabatan_id)
                    )
                    updated_count += 1
                    
                    print(f"✅ {nama_jabatan} → parent: {parent_nama} (ID: {parent_id})")
                else:
                    not_found_count += 1
                    print(f"⚠️ Parent not found: {parent_nama} for {nama_jabatan}")
            else:
                not_found_count += 1
                print(f"⚠️ No parent rule found: {nama_jabatan} (Level {level_jabatan})")
        
        connection.commit()
        
        print(f"\n📊 SUMMARY UPDATE:")
        print(f"✅ Berhasil update: {updated_count} jabatan")
        print(f"⚠️ Tidak ditemukan parent: {not_found_count} jabatan")
        
        return True
        
    except Exception as e:
        print(f"❌ Error updating hierarchy: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def show_hierarchy_report():
    """
    Tampilkan laporan hierarki
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
        
        # Get hierarchy statistics
        cursor.execute("""
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN parent_id IS NULL THEN 1 END) as no_parent,
                COUNT(CASE WHEN parent_id IS NOT NULL THEN 1 END) as has_parent,
                COUNT(CASE WHEN level_jabatan = 1 THEN 1 END) as level_1,
                COUNT(CASE WHEN level_jabatan = 2 THEN 1 END) as level_2,
                COUNT(CASE WHEN level_jabatan = 3 THEN 1 END) as level_3,
                COUNT(CASE WHEN level_jabatan = 4 THEN 1 END) as level_4,
                COUNT(CASE WHEN level_jabatan = 5 THEN 1 END) as level_5
            FROM dynamic_jabatan 
            WHERE is_active = 1
        """)
        
        stats = cursor.fetchone()
        
        print("\n📊 LAPORAN HIERARKI JABATAN")
        print("=" * 50)
        print(f"📈 Total Jabatan: {stats[0]}")
        print(f"🔗 Punya Parent: {stats[2]}")
        print(f"🚫 Tidak Punya Parent: {stats[1]}")
        print(f"📊 Level 1: {stats[3]}")
        print(f"📊 Level 2: {stats[4]}")
        print(f"📊 Level 3: {stats[5]}")
        print(f"📊 Level 4: {stats[6]}")
        print(f"📊 Level 5: {stats[7]}")
        
        # Show hierarchy tree
        print("\n🌳 CONTOH HIERARKI:")
        print("-" * 50)
        
        cursor.execute("""
            SELECT 
                j1.nama_jabatan,
                j2.nama_jabatan as parent_nama,
                j1.level_jabatan
            FROM dynamic_jabatan j1
            LEFT JOIN dynamic_jabatan j2 ON j1.parent_id = j2.id
            WHERE j1.is_active = 1
            ORDER BY j1.level_jabatan, j1.nama_jabatan
            LIMIT 15
        """)
        
        results = cursor.fetchall()
        for jabatan, parent, level in results:
            indent = "  " * (level - 1)
            parent_text = f" ← {parent}" if parent else " (TOP LEVEL)"
            print(f"{indent}• {jabatan}{parent_text}")
        
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
    
    print("🔄 MEMPERBAIKI HIERARKI JABATAN")
    print("=" * 50)
    
    # Step 1: Update hierarchy
    print("\n1. Update parent_id berdasarkan hierarki...")
    if not update_jabatan_hierarchy():
        print("❌ Gagal update hierarki")
        return
    
    # Step 2: Show report
    print("\n2. Generate laporan hierarki...")
    show_hierarchy_report()
    
    print("\n✅ UPDATE HIERARKI SELESAI!")
    print("\n📋 Jabatan sekarang memiliki parent_id yang sesuai")
    print("🔧 Hierarki mengikuti struktur organisasi POLRES SAMOSIR")

if __name__ == "__main__":
    main()
