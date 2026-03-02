#!/usr/bin/env python3
"""
Script untuk membaca Excel dan memperbaiki JSON personel
Source: docs/DATA PERS JANUARI 2026 NEW 2.xlsx
Target: json/personel.json
"""

import pandas as pd
import json
from datetime import datetime
import os

def read_excel_file():
    """Baca file Excel personel"""
    excel_path = '/home/petrick/Dokumen/code/bagops/docs/DATA PERS JANUARI 2026 NEW 2.xlsx'
    
    if not os.path.exists(excel_path):
        print(f"Error: File {excel_path} tidak ditemukan!")
        return None
    
    try:
        # Baca semua sheets
        excel_file = pd.ExcelFile(excel_path)
        print(f"Sheets yang ditemukan: {excel_file.sheet_names}")
        
        # Baca sheet pertama (biasanya data utama)
        df = pd.read_excel(excel_path, sheet_name=0)
        
        print(f"Total baris di Excel: {len(df)}")
        print(f"Kolom yang ditemukan: {list(df.columns)}")
        
        return df
    except Exception as e:
        print(f"Error membaca Excel: {e}")
        return None

def clean_data(df):
    """Bersihkan dan format data"""
    # Print sample data untuk debugging
    print("\nSample 5 baris pertama:")
    print(df.head())
    
    print("\nInfo kolom:")
    print(df.info())
    
    # Mapping kolom Excel ke field JSON
    column_mapping = {}
    
    # Deteksi kolom berdasarkan nama
    for col in df.columns:
        col_lower = col.lower().strip()
        if 'nama' in col_lower and 'nrp' not in col_lower:
            column_mapping[col] = 'nama'
        elif 'nrp' in col_lower:
            column_mapping[col] = 'nrp'
        elif 'pangkat' in col_lower:
            column_mapping[col] = 'pangkat'
        elif 'jabatan' in col_lower:
            column_mapping[col] = 'jabatan'
        elif 'unit' in col_lower or 'kantor' in col_lower or 'satuan' in col_lower:
            column_mapping[col] = 'kantor'
        elif 'status' in col_lower or 'aktif' in col_lower:
            column_mapping[col] = 'status_aktif'
        elif 'no' in col_lower or 'nomor' in col_lower:
            column_mapping[col] = 'nomor'
    
    print(f"\nMapping kolom: {column_mapping}")
    
    # Rename kolom
    df_clean = df.rename(columns=column_mapping)
    
    # Buat data JSON
    personel_data = []
    
    for index, row in df_clean.iterrows():
        person = {
            "id": index + 1,
            "nomor": int(row.get('nomor', index + 1)) if pd.notna(row.get('nomor', index + 1)) else index + 1,
            "nama": str(row.get('nama', '')).strip(),
            "pangkat": str(row.get('pangkat', '')).strip(),
            "nrp": str(row.get('nrp', '')).strip(),
            "jabatan": str(row.get('jabatan', '')).strip(),
            "kantor": str(row.get('kantor', '')).strip(),
            "status_aktif": True,  # Default aktif
            "created_at": datetime.now().isoformat(),
            "updated_at": datetime.now().isoformat()
        }
        
        # Handle status aktif
        status_col = None
        for col in df_clean.columns:
            if 'status' in col.lower() or 'aktif' in col.lower():
                status_col = col
                break
        
        if status_col and pd.notna(row.get(status_col)):
            status_value = str(row[status_col]).strip().lower()
            person['status_aktif'] = status_value in ['aktif', 'active', 'ya', 'yes', '1', 'true']
        
        # Skip jika nama kosong
        if person['nama'] and person['nama'] != 'nan':
            personel_data.append(person)
    
    return personel_data

def create_json_structure(personel_data):
    """Buat struktur JSON lengkap"""
    
    # Hitung statistik
    total_records = len(personel_data)
    aktif_records = sum(1 for p in personel_data if p.get('status_aktif', True))
    
    # Distribusi per kantor
    kantor_stats = {}
    pangkat_stats = {}
    
    for person in personel_data:
        kantor = person.get('kantor', 'Unknown')
        pangkat = person.get('pangkat', 'Unknown')
        
        kantor_stats[kantor] = kantor_stats.get(kantor, 0) + 1
        pangkat_stats[pangkat] = pangkat_stats.get(pangkat, 0) + 1
    
    json_structure = {
        "metadata": {
            "title": "Data Personel POLRES SAMOSIR",
            "version": "2.0",
            "created_date": datetime.now().strftime('%Y-%m-%d'),
            "description": "Data personel kepolisian yang dikonversi dari Excel",
            "source_file": "DATA PERS JANUARI 2026 NEW 2.xlsx",
            "total_records": total_records,
            "aktif_records": aktif_records,
            "last_updated": datetime.now().isoformat(),
            "conversion_method": "Python Pandas Excel to JSON Converter v2.0",
            "includes_structure": True,
            "structure_updated_date": datetime.now().isoformat(),
            "statistics": {
                "kantor_distribution": kantor_stats,
                "pangkat_distribution": pangkat_stats
            }
        },
        "data": personel_data
    }
    
    return json_structure

def save_json_file(json_data):
    """Simpan ke file JSON"""
    json_path = '/home/petrick/Dokumen/code/bagops/json/personel.json'
    
    try:
        with open(json_path, 'w', encoding='utf-8') as f:
            json.dump(json_data, f, indent=2, ensure_ascii=False)
        
        print(f"✅ JSON berhasil disimpan ke: {json_path}")
        print(f"📊 Total records: {json_data['metadata']['total_records']}")
        print(f"👮 Aktif records: {json_data['metadata']['aktif_records']}")
        
        # Print distribusi kantor
        print("\n📍 Distribusi per Kantor:")
        for kantor, count in json_data['metadata']['statistics']['kantor_distribution'].items():
            print(f"  {kantor}: {count} personel")
        
        return True
    except Exception as e:
        print(f"❌ Error menyimpan JSON: {e}")
        return False

def main():
    print("🔄 Update Personel JSON dari Excel")
    print("=" * 50)
    
    # 1. Baca Excel
    df = read_excel_file()
    if df is None:
        return
    
    # 2. Clean data
    print("\n🧹 Membersihkan data...")
    personel_data = clean_data(df)
    
    if not personel_data:
        print("❌ Tidak ada data yang diproses!")
        return
    
    print(f"✅ Data berhasil dibersihkan: {len(personel_data)} records")
    
    # 3. Buat struktur JSON
    print("\n📋 Membuat struktur JSON...")
    json_structure = create_json_structure(personel_data)
    
    # 4. Simpan JSON
    print("\n💾 Menyimpan JSON...")
    if save_json_file(json_structure):
        print("\n🎉 Update personel JSON berhasil!")
        
        # Backup old JSON
        old_json_path = '/home/petrick/Dokumen/code/bagops/json/personel.json'
        backup_path = f'/home/petrick/Dokumen/code/bagops/json/personel_backup_{datetime.now().strftime("%Y%m%d_%H%M%S")}.json'
        
        try:
            if os.path.exists(old_json_path):
                os.rename(old_json_path, backup_path)
                print(f"📦 Backup disimpan: {backup_path}")
        except:
            pass
    else:
        print("❌ Gagal menyimpan JSON!")

if __name__ == "__main__":
    main()
