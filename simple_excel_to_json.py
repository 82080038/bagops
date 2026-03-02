#!/usr/bin/env python3
"""
Script sederhana untuk konversi Excel ke JSON
"""

import pandas as pd
import json
from datetime import datetime

def main():
    # Baca Excel
    df = pd.read_excel('/home/petrick/Dokumen/code/bagops/docs/DATA PERS JANUARI 2026 NEW 2.xlsx', sheet_name=0)
    
    personel_data = []
    
    for index, row in df.iterrows():
        person = {
            "id": index + 1,
            "nomor": int(row['Nomor']) if pd.notna(row['Nomor']) else index + 1,
            "nama": str(row['Nama']).strip(),
            "pangkat": str(row['Pangkat']).strip(),
            "nrp": str(row['NRP']).strip(),
            "unit": str(row['Unit']).strip() if pd.notna(row['Unit']) else '',
            "jabatan": str(row['Jabatan']).strip(),
            "kantor": str(row['Kantor']).strip(),
            "status_aktif": True,
            "created_at": datetime.now().isoformat(),
            "updated_at": datetime.now().isoformat()
        }
        
        # Skip jika nama kosong
        if person['nama'] and person['nama'] != 'nan':
            personel_data.append(person)
    
    # Buat struktur JSON
    json_data = {
        "metadata": {
            "title": "Data Personel POLRES SAMOSIR",
            "version": "2.1",
            "created_date": datetime.now().strftime('%Y-%m-%d'),
            "description": "Data personel kepolisian yang dikonversi dari Excel",
            "source_file": "DATA PERS JANUARI 2026 NEW 2.xlsx",
            "total_records": len(personel_data),
            "last_updated": datetime.now().isoformat(),
            "conversion_method": "Python Pandas Excel to JSON Converter v2.1"
        },
        "data": personel_data
    }
    
    # Simpan JSON
    with open('/home/petrick/Dokumen/code/bagops/json/personel.json', 'w', encoding='utf-8') as f:
        json.dump(json_data, f, indent=2, ensure_ascii=False)
    
    print(f"✅ JSON berhasil dibuat: {len(personel_data)} records")
    
    # Print distribusi
    kantor_stats = {}
    for person in personel_data:
        kantor = person['kantor']
        kantor_stats[kantor] = kantor_stats.get(kantor, 0) + 1
    
    print("\n📍 Distribusi per Kantor:")
    for kantor, count in sorted(kantor_stats.items()):
        print(f"  {kantor}: {count} personel")

if __name__ == "__main__":
    main()
