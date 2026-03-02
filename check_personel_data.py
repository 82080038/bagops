#!/usr/bin/env python3
"""
Script untuk memeriksa dan memperbaiki data personel database
berdasarkan data yang ada di json/personel.json
"""

import json
import mysql.connector
from collections import defaultdict

# Database connection
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'rootpassword',
    'database': 'bagops_db'
}

def load_json_data():
    """Load data dari json/personel.json"""
    try:
        with open('/home/petrick/Dokumen/code/bagops/json/personel.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
        return data['data']
    except Exception as e:
        print(f"Error loading JSON: {e}")
        return []

def get_database_data():
    """Get current data dari database"""
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("SELECT id, nama, pangkat, nrp, unit, jabatan, is_active FROM personel ORDER BY id")
        db_data = cursor.fetchall()
        
        cursor.close()
        conn.close()
        return db_data
    except Exception as e:
        print(f"Error getting database data: {e}")
        return []

def analyze_data():
    """Analisis perbedaan data JSON vs Database"""
    json_data = load_json_data()
    db_data = get_database_data()
    
    print(f"JSON Records: {len(json_data)}")
    print(f"Database Records: {len(db_data)}")
    print("=" * 50)
    
    # Analisis kantor/unit
    json_units = defaultdict(int)
    db_units = defaultdict(int)
    
    for person in json_data:
        kantor = person.get('kantor', 'Unknown')
        json_units[kantor] += 1
    
    for person in db_data:
        unit = person.get('unit', 'Unknown')
        db_units[unit] += 1
    
    print("Distribusi Kantor di JSON:")
    for kantor, count in sorted(json_units.items()):
        print(f"  {kantor}: {count}")
    
    print("\nDistribusi Unit di Database:")
    for unit, count in sorted(db_units.items()):
        print(f"  {unit}: {count}")
    
    print("\n" + "=" * 50)
    
    # Cari personel dengan pangkat tinggi per kantor
    json_pimpinan = defaultdict(list)
    db_pimpinan = defaultdict(list)
    
    for person in json_data:
        if person.get('status_aktif', False):
            kantor = person.get('kantor', 'Unknown')
            pangkat = person.get('pangkat', '')
            if pangkat in ['AKBP', 'KOMPOL', 'AKP', 'IPTU', 'IPDA']:
                json_pimpinan[kantor].append({
                    'nama': person.get('nama', ''),
                    'pangkat': pangkat,
                    'nrp': person.get('nrp', ''),
                    'jabatan': person.get('jabatan', '')
                })
    
    for person in db_data:
        if person.get('is_active', False):
            unit = person.get('unit', 'Unknown')
            pangkat = person.get('pangkat', '')
            if pangkat in ['AKBP', 'KOMPOL', 'AKP', 'IPTU', 'IPDA']:
                db_pimpinan[unit].append({
                    'nama': person.get('nama', ''),
                    'pangkat': pangkat,
                    'nrp': person.get('nrp', ''),
                    'jabatan': person.get('jabatan', '')
                })
    
    print("Pimpinan per Kantor (JSON):")
    for kantor, pimpinan in json_pimpinan.items():
        print(f"  {kantor}:")
        for p in sorted(pimpinan, key=lambda x: x['pangkat']):
            print(f"    - {p['pangkat']} {p['nama']} ({p['nrp']})")
    
    print("\nPimpinan per Unit (Database):")
    for unit, pimpinan in db_pimpinan.items():
        print(f"  {unit}:")
        for p in sorted(pimpinan, key=lambda x: x['pangkat']):
            print(f"    - {p['pangkat']} {p['nama']} ({p['nrp']})")
    
    return json_data, db_data, json_units, db_units

def fix_unit_mapping():
    """Perbaiki mapping unit personel di database"""
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Mapping dari JSON ke unit yang benar
        unit_mapping = {
            'POLRES SAMOSIR': 'POLRES SAMOSIR',
            'POLSEK SIMANINDO': 'POLSEK SIMANINDO',
            'POLSEK HARIAN BOHO': 'POLSEK HARIAN BOHO',
            'POLSEK PALIPI': 'POLSEK PALIPI',
            'POLSEK ONAN RUNGGU': 'POLSEK ONAN RUNGGU',
            'POLSEK PANGURURAN': 'POLSEK PANGURURAN'
        }
        
        json_data = load_json_data()
        
        print("Memperbaiki mapping unit personel...")
        
        for person in json_data:
            if person.get('status_aktif', False):
                kantor = person.get('kantor', '')
                nrp = person.get('nrp', '')
                
                if kantor in unit_mapping and nrp:
                    # Update unit berdasarkan kantor di JSON
                    new_unit = unit_mapping[kantor]
                    cursor.execute(
                        "UPDATE personel SET unit = %s WHERE nrp = %s",
                        (new_unit, nrp)
                    )
                    print(f"Updated {nrp} -> {new_unit}")
        
        conn.commit()
        cursor.close()
        conn.close()
        
        print("Unit mapping berhasil diperbaiki!")
        
    except Exception as e:
        print(f"Error fixing unit mapping: {e}")

if __name__ == "__main__":
    print("Analisis Data Personel POLRES SAMOSIR")
    print("=" * 50)
    
    # Analisis data
    json_data, db_data, json_units, db_units = analyze_data()
    
    print("\n" + "=" * 50)
    print("REKOMENDASI PERBAIKAN:")
    print("1. Mapping unit personel harus disesuaikan dengan data JSON")
    print("2. Unit di database harus mengikuti 'kantor' dari JSON")
    print("3. Personel harus di-update ke unit yang sesuai")
    
    # Tanya user apakah mau fix
    response = input("\nApakah ingin memperbaiki mapping unit? (y/n): ")
    if response.lower() == 'y':
        fix_unit_mapping()
        print("\nData setelah perbaikan:")
        analyze_data()
    else:
        print("Perbaikan dibatalkan.")
