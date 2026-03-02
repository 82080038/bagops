#!/usr/bin/env python3
"""
Script untuk mengekstrak data unit, jabatan, kantor, dan keterangan dari personel.json
Author: BAGOPS System
Date: 2026-03-02
"""

import json
from collections import defaultdict

def extract_personel_structure(personel_file):
    """
    Ekstrak data unit, jabatan, kantor, dan keterangan dari file personel.json
    
    Args:
        personel_file (str): Path ke file personel.json
    """
    
    print(f"🔍 Membaca file: {personel_file}")
    
    try:
        with open(personel_file, 'r', encoding='utf-8') as f:
            data = json.load(f)
        
        personel_data = data.get('data', [])
        print(f"📊 Total personel: {len(personel_data)}")
        
        # Ekstrak data unik
        units = set()
        jabatans = set()
        kantors = set()
        keterangans = set()
        
        # Struktur untuk menyimpan detail
        unit_details = defaultdict(list)
        jabatan_details = defaultdict(list)
        kantor_details = defaultdict(list)
        
        print("🔄 Memproses data...")
        
        for person in personel_data:
            # Collect units
            if 'unit' in person and person['unit'] and person['unit'] != '':
                unit = person['unit'].strip()
                units.add(unit)
                unit_details[unit].append({
                    'nama': person.get('nama', ''),
                    'nrp': person.get('nrp', ''),
                    'pangkat': person.get('pangkat', ''),
                    'jabatan': person.get('jabatan', '')
                })
            
            # Collect jabatans
            if 'jabatan' in person and person['jabatan'] and person['jabatan'] != '':
                jabatan = person['jabatan'].strip()
                jabatans.add(jabatan)
                jabatan_details[jabatan].append({
                    'nama': person.get('nama', ''),
                    'nrp': person.get('nrp', ''),
                    'pangkat': person.get('pangkat', ''),
                    'unit': person.get('unit', '')
                })
            
            # Collect kantors
            if 'kantor' in person and person['kantor'] and person['kantor'] != '':
                kantor = person['kantor'].strip()
                kantors.add(kantor)
                kantor_details[kantor].append({
                    'nama': person.get('nama', ''),
                    'nrp': person.get('nrp', ''),
                    'pangkat': person.get('pangkat', ''),
                    'jabatan': person.get('jabatan', '')
                })
            
            # Collect keterangans
            if 'keterangan' in person and person['keterangan'] and person['keterangan'] != '':
                keterangan = person['keterangan'].strip()
                keterangans.add(keterangan)
        
        # Buat struktur output
        output_data = {
            "metadata": {
                "title": "Struktur Organisasi Personel POLRES SAMOSIR",
                "version": "1.0",
                "created_date": "2026-03-02",
                "description": "Data unit, jabatan, kantor, dan keterangan dari personel POLRES SAMOSIR",
                "source_file": "personel.json",
                "extraction_date": "2026-03-02T10:27:00+07:00",
                "total_personel": len(personel_data)
            },
            "units": {
                "total_count": len(units),
                "data": sorted([{
                    "nama_unit": unit,
                    "jumlah_personel": len(unit_details[unit]),
                    "personel_list": unit_details[unit][:5]  # Max 5 personel per unit
                } for unit in units], key=lambda x: x['nama_unit'])
            },
            "jabatans": {
                "total_count": len(jabatans),
                "data": sorted([{
                    "nama_jabatan": jabatan,
                    "jumlah_personel": len(jabatan_details[jabatan]),
                    "personel_list": jabatan_details[jabatan][:5]  # Max 5 personel per jabatan
                } for jabatan in jabatans], key=lambda x: x['nama_jabatan'])
            },
            "kantors": {
                "total_count": len(kantors),
                "data": sorted([{
                    "nama_kantor": kantor,
                    "jumlah_personel": len(kantor_details[kantor]),
                    "personel_list": kantor_details[kantor][:5]  # Max 5 personel per kantor
                } for kantor in kantors], key=lambda x: x['nama_kantor'])
            },
            "keterangans": {
                "total_count": len(keterangans),
                "data": sorted([{
                    "keterangan": keterangan
                } for keterangan in keterangans], key=lambda x: x['keterangan'])
            }
        }
        
        return output_data
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return None

def save_structure_data(structure_data, output_file):
    """
    Simpan data struktur ke file JSON
    
    Args:
        structure_data (dict): Data struktur
        output_file (str): Path output file
    """
    
    try:
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(structure_data, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Data struktur disimpan ke: {output_file}")
        return True
        
    except Exception as e:
        print(f"❌ Error menyimpan file: {str(e)}")
        return False

def print_summary(structure_data):
    """
    Cetak summary data struktur
    
    Args:
        structure_data (dict): Data struktur
    """
    
    print("\n📊 SUMMARY DATA STRUKTUR:")
    print("=" * 50)
    print(f"🏢 Total Units: {structure_data['units']['total_count']}")
    print(f"💼 Total Jabatans: {structure_data['jabatans']['total_count']}")
    print(f"🏛️ Total Kantors: {structure_data['kantors']['total_count']}")
    print(f"📝 Total Keterangans: {structure_data['keterangans']['total_count']}")
    
    print("\n🏢 DAFTAR UNIT:")
    for i, unit in enumerate(structure_data['units']['data'][:10], 1):
        print(f"  {i}. {unit['nama_unit']} ({unit['jumlah_personel']} personel)")
    
    print("\n💼 DAFTAR JABATAN (Top 10):")
    for i, jabatan in enumerate(structure_data['jabatans']['data'][:10], 1):
        print(f"  {i}. {jabatan['nama_jabatan']} ({jabatan['jumlah_personel']} personel)")
    
    print("\n🏛️ DAFTAR KANTOR:")
    for i, kantor in enumerate(structure_data['kantors']['data'], 1):
        print(f"  {i}. {kantor['nama_kantor']} ({kantor['jumlah_personel']} personel)")

def main():
    """
    Main function
    """
    
    # File paths
    personel_file = "/var/www/html/bagops/json/personel.json"
    output_file = "/var/www/html/bagops/json/personel_structure.json"
    
    print("🔄 Mengekstrak data struktur personel...")
    
    # Extract structure data
    structure_data = extract_personel_structure(personel_file)
    
    if structure_data:
        # Save to file
        if save_structure_data(structure_data, output_file):
            # Print summary
            print_summary(structure_data)
            
            # Show file size
            import os
            file_size = os.path.getsize(output_file) / 1024  # KB
            print(f"\n📏 Ukuran file: {file_size:.2f} KB")
            print(f"📁 Output: {output_file}")
        else:
            print("❌ Gagal menyimpan data struktur")
    else:
        print("❌ Gagal mengekstrak data struktur")

if __name__ == "__main__":
    main()
