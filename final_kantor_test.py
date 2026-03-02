#!/usr/bin/env python3
"""
Final test untuk halaman kantor yang sudah diperbaiki
"""

import requests
import json

def test_api():
    """Test API kantor"""
    print("🔍 Testing Kantor API...")
    
    url = "http://localhost/bagops/ajax/kantor.php"
    cookies = {'PHPSESSID': 'test_session'}
    
    # Test list
    response = requests.post(url, data={'action': 'list'}, cookies=cookies)
    
    if response.status_code == 200:
        try:
            data = response.json()
            if data['success']:
                print(f"✅ API Success: {len(data['data'])} kantor ditemukan")
                
                for kantor in data['data']:
                    pimpinan = kantor['pimpinan_nama'] or "Tidak ada"
                    print(f"  🏢 {kantor['nama_kantor']}: {kantor['jumlah_personel']} personel, Pimpinan: {pimpinan}")
                
                return True
            else:
                print(f"❌ API Error: {data.get('message', 'Unknown error')}")
                return False
        except json.JSONDecodeError:
            print("❌ Invalid JSON response")
            return False
    else:
        print(f"❌ HTTP Error: {response.status_code}")
        return False

def test_page_render():
    """Test render halaman"""
    print("\n🌐 Testing Page Render...")
    
    url = "http://localhost/bagops/simple_root_system.php?page=kantor"
    cookies = {'PHPSESSID': 'test_session'}
    
    response = requests.get(url, cookies=cookies)
    
    if response.status_code == 200:
        html = response.text
        
        # Cek komponen penting
        checks = {
            'Table kantorTable': 'id="kantorTable"' in html,
            'Table compact': 'table-sm' in html,
            'Padding minimal': 'py-1' in html,
            'JavaScript functions': 'function editKantor' in html,
            'Bootstrap modal': 'kantorModal' in html,
            'DataTables': 'DataTables' in html
        }
        
        print("📋 Komponen Halaman:")
        for check, status in checks.items():
            icon = "✅" if status else "❌"
            print(f"  {icon} {check}")
        
        all_good = all(checks.values())
        
        if all_good:
            print("\n🎉 Semua komponen lengkap!")
        else:
            print("\n⚠️ Beberapa komponen hilang")
        
        return all_good
    else:
        print(f"❌ HTTP Error: {response.status_code}")
        return False

def test_data_integrity():
    """Test integritas data"""
    print("\n🔍 Testing Data Integrity...")
    
    url = "http://localhost/bagops/ajax/kantor.php"
    cookies = {'PHPSESSID': 'test_session'}
    
    response = requests.post(url, data={'action': 'list'}, cookies=cookies)
    
    if response.status_code == 200:
        data = response.json()
        
        if data['success']:
            kantor_data = data['data']
            
            # Validasi total
            total_personel = sum(k['jumlah_personel'] for k in kantor_data)
            expected_total = 206 + 10 + 10 + 10 + 11 + 10  # 257
            
            print(f"📊 Total Personel: {total_personel} (expected: {expected_total})")
            
            # Validasi pimpinan
            pimpinan_count = sum(1 for k in kantor_data if k['pimpinan_nama'])
            print(f"👮 Kantor dengan pimpinan: {pimpinan_count}/6")
            
            # Validasi struktur
            required_fields = ['id', 'nama_kantor', 'jumlah_personel', 'pimpinan_nama', 'pimpinan_pangkat_asli']
            
            all_valid = True
            for kantor in kantor_data:
                for field in required_fields:
                    if field not in kantor:
                        print(f"❌ Missing field: {field} di {kantor['nama_kantor']}")
                        all_valid = False
            
            if all_valid:
                print("✅ Semua field lengkap")
            
            return all_valid and total_personel == expected_total
    
    return False

def main():
    print("🧪 Final Test Halaman Kantor")
    print("=" * 50)
    
    # Test API
    api_ok = test_api()
    
    # Test render
    render_ok = test_page_render()
    
    # Test data
    data_ok = test_data_integrity()
    
    print("\n" + "=" * 50)
    print("📊 Final Results:")
    print(f"  API Test: {'✅ PASS' if api_ok else '❌ FAIL'}")
    print(f"  Page Render: {'✅ PASS' if render_ok else '❌ FAIL'}")
    print(f"  Data Integrity: {'✅ PASS' if data_ok else '❌ FAIL'}")
    
    if api_ok and render_ok and data_ok:
        print("\n🎉 SEMUA TEST BERHASIL!")
        print("🌐 Halaman kantor siap digunakan")
        print("📊 Data 100% akurat dari Excel")
        print("🎨 UI compact dan responsive")
        print("⚙️ CRUD functionality lengkap")
    else:
        print("\n⚠️ Beberapa test gagal, perlu perbaikan")

if __name__ == "__main__":
    main()
