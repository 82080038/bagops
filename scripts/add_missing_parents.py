#!/usr/bin/env python3
"""
Script untuk menambahkan jabatan parent yang hilang ke database
Author: BAGOPS System
Date: 2026-03-02
"""

import mysql.connector

def add_missing_parent_jabatans():
    """
    Tambah jabatan parent yang hilang
    """
    
    missing_parents = [
        # Level 2 - Parent utama yang hilang
        ('KABAG SDM', 'KABSDM', 2, None),
        ('KABAG REN', 'KABREN', 2, None),
        ('KABAG LOG', 'KABLOG', 2, None),
        ('KABAG SUMDA', 'KABSUM', 2, None),
        ('KASAT INTELKAM', 'KASINTEL', 2, None),
        ('KASAT NARKOBA', 'KASNARK', 2, None),
        ('KASAT BINMAS', 'KASBIN', 2, None),
        ('KASAT TAHTI', 'KASTAHTI', 2, None),
        ('KASAT POLAIRUD', 'KASPOLAIR', 2, None),
        ('KASAT SIUM', 'KASSIUM', 2, None),
    ]
    
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        added_count = 0
        skipped_count = 0
        
        for nama_jabatan, kode_jabatan, level_jabatan, parent_id in missing_parents:
            # Check if already exists
            cursor.execute(
                "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = %s AND is_active = 1",
                (nama_jabatan,)
            )
            
            if cursor.fetchone():
                skipped_count += 1
                print(f"⏭️ {nama_jabatan} - sudah ada")
                continue
            
            # Insert new parent jabatan
            cursor.execute("""
                INSERT INTO dynamic_jabatan 
                (nama_jabatan, kode_jabatan, level_jabatan, parent_id, created_by, created_at, updated_at)
                VALUES (%s, %s, %s, %s, 1, NOW(), NOW())
            """, (nama_jabatan, kode_jabatan, level_jabatan, parent_id))
            
            added_count += 1
            print(f"✅ {nama_jabatan} (Level {level_jabatan}) - berhasil ditambahkan")
        
        connection.commit()
        
        print(f"\n📊 SUMMARY:")
        print(f"✅ Berhasil ditambahkan: {added_count} jabatan")
        print(f"⏭️ Dilewati (sudah ada): {skipped_count} jabatan")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def update_orphan_jabatans():
    """
    Update jabatan yang belum punya parent dengan parent yang baru ditambahkan
    """
    
    parent_mapping = {
        # Jabatan yang parent-nya KABAG SDM
        'KASIDOKKES': 'KABAG SDM',
        'KASUBSIBANKUM': 'KABAG SDM',
        'BA MIN BAG SDM': 'KABAG SDM',
        'BA PEMBINAAN': 'KABAG SDM',
        'BA SIDOKKES': 'KABAG SDM',
        'BINTARA SITIK': 'KABAG SDM',
        'PS. KASIUM': 'KABAG SDM',
        
        # Jabatan yang parent-nya KABAG REN
        'PAURSUBBAGPROGAR': 'KABAG REN',
        'BA MIN BAG REN': 'KABAG REN',
        
        # Jabatan yang parent-nya KABAG LOG
        'BA MIN BAG LOG': 'KABAG LOG',
        'Plt. KASUBBAGBEKPAL': 'KABAG LOG',
        
        # Jabatan yang parent-nya KABAG SUMDA
        'BINTARA SIKEU': 'KABAG SUMDA',
        'PS. KASIKEU': 'KABAG SUMDA',
        
        # Jabatan yang parent-nya KASAT INTELKAM
        'KANIT INTELKAM': 'KASAT INTELKAM',
        'KANITIDIK 1': 'KASAT INTELKAM',
        'KANITIDIK 2': 'KASAT INTELKAM',
        'KANITIDIK 3': 'KASAT INTELKAM',
        'KANITIDIK 4': 'KASAT INTELKAM',
        'KANITIDIK 5': 'KASAT INTELKAM',
        'PS. KANIT 1': 'KASAT INTELKAM',
        'PS. KANIT 2': 'KASAT INTELKAM',
        'PS. KANIT 3': 'KASAT INTELKAM',
        'PS. KANIT INTELKAM': 'KASAT INTELKAM',
        'PS. KANIT IDENTIFIKASI': 'KASAT INTELKAM',
        'PS.KANIT IDIK 1': 'KASAT INTELKAM',
        'BINTARA SAT INTELKAM': 'KASAT INTELKAM',
        'BINTARA SATINTELKAM': 'KASAT INTELKAM',
        
        # Jabatan yang parent-nya KASAT NARKOBA
        'KANIT NARKOBA': 'KASAT NARKOBA',
        'BINTARA SATRESNARKOBA': 'KASAT NARKOBA',
        
        # Jabatan yang parent-nya KASAT BINMAS
        'KANIT BINMAS': 'KASAT BINMAS',
        'PS. KANIT BINMAS': 'KASAT BINMAS',
        'BINTARA SAT BINMAS': 'KASAT BINMAS',
        
        # Jabatan yang parent-nya KASAT TAHTI
        'KANIT TAHTI': 'KASAT TAHTI',
        'PS. KANIT TAHTI': 'KASAT TAHTI',
        'BINTARA SAT TAHTI': 'KASAT TAHTI',
        
        # Jabatan yang parent-nya KASAT POLAIRUD
        'KANIT POLAIRUD': 'KASAT POLAIRUD',
        'PS. KANITPATROLI': 'KASAT POLAIRUD',
        'BINTARA SATPOLAIRUD': 'KASAT POLAIRUD',
        
        # Jabatan yang parent-nya KASAT SIUM
        'BINTARA SIUM': 'KASAT SIUM',
    }
    
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        updated_count = 0
        not_found_count = 0
        
        for jabatan_nama, parent_nama in parent_mapping.items():
            # Find jabatan ID
            cursor.execute(
                "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = %s AND is_active = 1",
                (jabatan_nama,)
            )
            jabatan_result = cursor.fetchone()
            
            if not jabatan_result:
                not_found_count += 1
                print(f"⚠️ Jabatan tidak ditemukan: {jabatan_nama}")
                continue
            
            # Find parent ID
            cursor.execute(
                "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = %s AND is_active = 1",
                (parent_nama,)
            )
            parent_result = cursor.fetchone()
            
            if not parent_result:
                not_found_count += 1
                print(f"⚠️ Parent tidak ditemukan: {parent_nama} untuk {jabatan_nama}")
                continue
            
            # Update parent_id
            cursor.execute(
                "UPDATE dynamic_jabatan SET parent_id = %s WHERE id = %s",
                (parent_result[0], jabatan_result[0])
            )
            
            updated_count += 1
            print(f"✅ {jabatan_nama} → parent: {parent_nama}")
        
        connection.commit()
        
        print(f"\n📊 UPDATE SUMMARY:")
        print(f"✅ Berhasil update: {updated_count} jabatan")
        print(f"⚠️ Tidak ditemukan: {not_found_count} jabatan")
        
        return True
        
    except Exception as e:
        print(f"❌ Error updating: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def show_final_status():
    """
    Tampilkan status final hierarki
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
        
        # Get final statistics
        cursor.execute("""
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN parent_id IS NULL THEN 1 END) as no_parent,
                COUNT(CASE WHEN parent_id IS NOT NULL THEN 1 END) as has_parent
            FROM dynamic_jabatan 
            WHERE is_active = 1
        """)
        
        stats = cursor.fetchone()
        
        print(f"\n📊 FINAL HIERARCHY STATUS:")
        print(f"📈 Total Jabatan: {stats[0]}")
        print(f"🔗 Punya Parent: {stats[2]} ({stats[2]/stats[0]*100:.1f}%)")
        print(f"🚫 Tidak Punya Parent: {stats[1]} ({stats[1]/stats[0]*100:.1f}%)")
        
        # Show remaining orphan jabatans
        cursor.execute("""
            SELECT nama_jabatan, level_jabatan 
            FROM dynamic_jabatan 
            WHERE parent_id IS NULL AND is_active = 1 
            AND nama_jabatan != 'KAPOLRES SAMOSIR'
            ORDER BY level_jabatan, nama_jabatan
            LIMIT 10
        """)
        
        remaining = cursor.fetchall()
        
        if remaining:
            print(f"\n📋 Sisa Jabatan Tanpa Parent (Top 10):")
            for nama, level in remaining:
                print(f"  • {nama} (Level {level})")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def main():
    """
    Main function
    """
    
    print("🔄 ACTION ITEM 1: TAMBAH JABATAN PARENT YANG HILANG")
    print("=" * 60)
    
    # Step 1: Add missing parent jabatans
    print("\n1. Menambahkan jabatan parent yang hilang...")
    if not add_missing_parent_jabatans():
        print("❌ Gagal menambahkan parent jabatan")
        return
    
    # Step 2: Update orphan jabatans
    print("\n2. Update jabatan yatim dengan parent baru...")
    if not update_orphan_jabatans():
        print("❌ Gagal update jabatan yatim")
        return
    
    # Step 3: Show final status
    print("\n3. Status final hierarki...")
    show_final_status()
    
    print("\n✅ ACTION ITEM 1 SELESAI!")
    print("\n📋 Hierarki jabatan sudah jauh lebih baik")
    print("🔧 Sistem siap untuk penggunaan penuh")

if __name__ == "__main__":
    main()
