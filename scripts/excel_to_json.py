#!/usr/bin/env python3
"""
Script untuk mengkonversi data personel dari Excel ke JSON
Author: BAGOPS System
Date: 2026-03-02
"""

import pandas as pd
import json
import os
from datetime import datetime

def convert_excel_to_json(excel_file_path, output_dir):
    """
    Konversi file Excel ke format JSON untuk data personel BAGOPS
    
    Args:
        excel_file_path (str): Path ke file Excel
        output_dir (str): Directory untuk menyimpan file JSON
    """
    
    print(f"🔍 Membaca file Excel: {excel_file_path}")
    
    try:
        # Baca file Excel
        df = pd.read_excel(excel_file_path)
        
        print(f"📊 Ditemukan {len(df)} baris data")
        print(f"📋 Kolom yang tersedia: {list(df.columns)}")
        
        # Bersihkan dan proses data
        personel_data = process_personel_data(df)
        
        # Buat struktur JSON lengkap
        json_output = {
            "metadata": {
                "title": "Data Personel POLRES SAMOSIR",
                "version": "1.0",
                "created_date": datetime.now().strftime("%Y-%m-%d"),
                "description": "Data personel kepolisian yang dikonversi dari Excel",
                "source_file": os.path.basename(excel_file_path),
                "total_records": len(personel_data),
                "last_updated": datetime.now().isoformat(),
                "conversion_method": "Python Pandas Excel to JSON Converter"
            },
            "data": personel_data
        }
        
        # Simpan ke file JSON
        output_file = os.path.join(output_dir, "personel.json")
        
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(json_output, f, indent=2, ensure_ascii=False, default=str)
        
        print(f"✅ Berhasil mengkonversi ke: {output_file}")
        print(f"📈 Total personel: {len(personel_data)}")
        
        return output_file
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return None

def process_personel_data(df):
    """
    Proses data personel dari DataFrame ke format yang sesuai
    
    Args:
        df (pd.DataFrame): DataFrame dari Excel
        
    Returns:
        list: List of personel data
    """
    
    personel_list = []
    
    for index, row in df.iterrows():
        # Mapping kolom Excel ke field personel (sesuai dengan struktur Excel)
        personel = {
            "id": index + 1,
            "nomor": int(row.get('Nomor', 0)) if pd.notna(row.get('Nomor')) else None,
            "nama": str(row.get('Nama', '')).strip(),
            "pangkat": str(row.get('Pangkat', '')).strip(),
            "nrp": str(row.get('NRP', '')).strip(),
            "unit": str(row.get('Unit', '')).strip(),
            "jabatan": str(row.get('Jabatan', '')).strip(),
            "kantor": str(row.get('Kantor', '')).strip(),
            "keterangan": str(row.get('Keterangan', '')).strip(),
            "status_aktif": True,
            "created_at": datetime.now().isoformat(),
            "updated_at": datetime.now().isoformat()
        }
        
        # Hapus field yang kosong atau NaN
        personel = {k: v for k, v in personel.items() 
                   if v is not None and v != '' and v != 'nan' and str(v).strip() != ''}
        
        personel_list.append(personel)
    
    return personel_list

def analyze_data_structure(df):
    """
    Analisis struktur data untuk debugging
    
    Args:
        df (pd.DataFrame): DataFrame dari Excel
    """
    
    print("\n📊 Analisis Struktur Data:")
    print(f"Shape: {df.shape}")
    print(f"\nKolom:")
    for i, col in enumerate(df.columns):
        print(f"  {i+1}. {col}")
    
    print(f"\nSample data (5 baris pertama):")
    print(df.head().to_string())
    
    print(f"\nTipe data:")
    print(df.dtypes)

def main():
    """
    Main function untuk menjalankan konversi
    """
    
    # Path configuration
    excel_file = "/var/www/html/bagops/docs/DATA PERS JANUARI 2026 NEW 2.xlsx"
    output_dir = "/var/www/html/bagops/json"
    
    # Check if file exists
    if not os.path.exists(excel_file):
        print(f"❌ File Excel tidak ditemukan: {excel_file}")
        return
    
    # Create output directory if not exists
    os.makedirs(output_dir, exist_ok=True)
    
    # Read and analyze Excel first
    print("🔍 Menganalisis struktur file Excel...")
    try:
        df = pd.read_excel(excel_file)
        analyze_data_structure(df)
    except Exception as e:
        print(f"❌ Error membaca Excel: {str(e)}")
        return
    
    # Convert to JSON
    print("\n🔄 Mengkonversi ke JSON...")
    result = convert_excel_to_json(excel_file, output_dir)
    
    if result:
        print(f"\n✅ Konversi berhasil!")
        print(f"📁 Output file: {result}")
        
        # Show file size
        file_size = os.path.getsize(result) / 1024  # KB
        print(f"📏 Ukuran file: {file_size:.2f} KB")
        
    else:
        print(f"\n❌ Konversi gagal!")

if __name__ == "__main__":
    main()
