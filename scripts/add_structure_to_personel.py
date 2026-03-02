#!/usr/bin/env python3
"""
Script untuk menambahkan data struktur ke bagian bawah personel.json
Author: BAGOPS System
Date: 2026-03-02
"""

import json

def add_structure_to_personel():
    """
    Tambahkan data struktur ke bagian bawah personel.json
    """
    
    personel_file = "/var/www/html/bagops/json/personel.json"
    structure_file = "/var/www/html/bagops/json/personel_structure.json"
    
    try:
        # Baca file personel.json
        with open(personel_file, 'r', encoding='utf-8') as f:
            personel_data = json.load(f)
        
        # Baca file struktur
        with open(structure_file, 'r', encoding='utf-8') as f:
            structure_data = json.load(f)
        
        # Tambahkan data struktur ke personel.json
        personel_data["personel_structure"] = {
            "units": structure_data["units"],
            "jabatans": structure_data["jabatans"], 
            "kantors": structure_data["kantors"],
            "keterangans": structure_data["keterangans"]
        }
        
        # Update metadata
        personel_data["metadata"]["includes_structure"] = True
        personel_data["metadata"]["structure_added_date"] = "2026-03-02T10:27:00+07:00"
        
        # Simpan kembali file personel.json
        with open(personel_file, 'w', encoding='utf-8') as f:
            json.dump(personel_data, f, indent=2, ensure_ascii=False)
        
        print("✅ Data struktur berhasil ditambahkan ke personel.json")
        print(f"📊 Total Units: {len(structure_data['units']['data'])}")
        print(f"💼 Total Jabatans: {len(structure_data['jabatans']['data'])}")
        print(f"🏛️ Total Kantors: {len(structure_data['kantors']['data'])}")
        print(f"📝 Total Keterangans: {len(structure_data['keterangans']['data'])}")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return False

if __name__ == "__main__":
    add_structure_to_personel()
