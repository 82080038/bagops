#!/usr/bin/env python3
"""
Script untuk memperbaiki halaman kantor berdasarkan analisis
"""

import os
import re

def fix_kantor_page():
    """Perbaiki halaman kantor"""
    
    kantor_file = '/home/petrick/Dokumen/code/bagops/pages/kantor.php'
    
    if not os.path.exists(kantor_file):
        print(f"❌ File {kantor_file} tidak ditemukan!")
        return False
    
    # Baca file
    with open(kantor_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    print("🔧 Memperbaiki halaman kantor...")
    
    # 1. Pastikan table dengan id='kantorTable' ada
    if 'id="kantorTable"' not in content:
        print("❌ Table kantorTable tidak ditemukan!")
        return False
    
    # 2. Tambahkan class table-sm jika belum ada
    if 'table-sm' not in content:
        content = re.sub(
            r'<table class="table table-bordered"',
            '<table class="table table-bordered table-sm"',
            content
        )
        print("✅ Added table-sm class")
    
    # 3. Tambahkan py-1 pada td jika belum ada
    if 'class="py-1"' not in content:
        # Tambahkan py-1 pada td di tbody
        content = re.sub(
            r'<td>',
            '<td class="py-1">',
            content
        )
        print("✅ Added py-1 class to td")
    
    # 4. Tambahkan align-middle pada tr jika belum ada
    if 'align-middle' not in content:
        content = re.sub(
            r'<tr>',
            '<tr class="align-middle">',
            content
        )
        print("✅ Added align-middle class to tr")
    
    # 5. Pastikan JavaScript functions ada
    required_functions = [
        'function editKantor',
        'function deleteKantor',
        'function viewKantor',
        'function showCreateKantorModal',
        'function saveKantor',
        'function toggleStatus',
        'function filterKantor',
        'function updateKantorTable'
    ]
    
    missing_functions = []
    for func in required_functions:
        if func not in content:
            missing_functions.append(func)
    
    if missing_functions:
        print(f"⚠️ Missing functions: {missing_functions}")
        
        # Tambahkan JavaScript functions yang hilang
        js_code = """
<script>
// CRUD Functions for Kantor Management
function editKantor(id) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var kantor = response.data;
                $('#kantorModalTitle').text('Edit Kantor');
                $('#kantorId').val(kantor.id);
                $('#nama_kantor').val(kantor.nama_kantor);
                $('#tipe_kantor_polisi').val(kantor.tipe_kantor_polisi);
                $('#klasifikasi').val(kantor.klasifikasi);
                $('#level_kompleksitas').val(kantor.level_kompleksitas);
                $('#pimpinan_default_pangkat').val(kantor.pimpinan_default_pangkat);
                $('#alamat').val(kantor.alamat);
                $('#telepon').val(kantor.telepon);
                $('#email').val(kantor.email);
                $('#jam_operasional').val(kantor.jam_operasional);
                $('#latitude').val(kantor.latitude);
                $('#longitude').val(kantor.longitude);
                $('#status').val(kantor.status);
                
                var modal = new bootstrap.Modal(document.getElementById('kantorModal'));
                modal.show();
            } else {
                alert('Gagal memuat data kantor: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function deleteKantor(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kantor ini?')) {
        $.ajax({
            url: 'ajax/kantor.php',
            method: 'POST',
            data: {action: 'delete', id: id},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Gagal menghapus kantor: ' + response.message);
                }
            },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function viewKantor(id) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var kantor = response.data;
                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Umum</h6>
                            <table class="table table-sm">
                                <tr><td>Nama Kantor</td><td><strong>${kantor.nama_kantor}</strong></td></tr>
                                <tr><td>Tipe</td><td>${kantor.tipe_kantor_polisi || '-'}</td></tr>
                                <tr><td>Klasifikasi</td><td>${kantor.klasifikasi || '-'}</td></tr>
                                <tr><td>Level Kompleksitas</td><td>${kantor.level_kompleksitas || '-'}</td></tr>
                                <tr><td>Pimpinan Aktual</td><td>${kantor.pimpinan_nama ? kantor.pimpinan_pangkat_asli + ' - ' + kantor.pimpinan_nama : 'Tidak ada'}</td></tr>
                                <tr><td>Status</td><td><span class="badge ${kantor.status === 'aktif' ? 'bg-success' : 'bg-danger'}">${kantor.status}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Kontak & Lokasi</h6>
                            <table class="table table-sm">
                                <tr><td>Alamat</td><td>${kantor.alamat || '-'}</td></tr>
                                <tr><td>Telepon</td><td>${kantor.telepon || '-'}</td></tr>
                                <tr><td>Email</td><td>${kantor.email || '-'}</td></tr>
                                <tr><td>Jam Operasional</td><td>${kantor.jam_operasional || '-'}</td></tr>
                                <tr><td>Latitude</td><td>${kantor.latitude || '-'}</td></tr>
                                <tr><td>Longitude</td><td>${kantor.longitude || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Personel</h6>
                            <p><strong>Total Personel:</strong> ${kantor.jumlah_personel || 0} personel</p>
                        </div>
                    </div>
                `;
                
                $('#detailKantorContent').html(content);
                var modal = new bootstrap.Modal(document.getElementById('detailKantorModal'));
                modal.show();
            } else {
                alert('Gagal memuat detail kantor: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function showCreateKantorModal() {
    $('#kantorModalTitle').text('Tambah Kantor Baru');
    $('#kantorForm')[0].reset();
    $('#kantorId').val('');
    $('#status').val('aktif');
    var modal = new bootstrap.Modal(document.getElementById('kantorModal'));
    modal.show();
}

function saveKantor() {
    var form = $('#kantorForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var formData = new FormData(form);
    formData.append('action', $('#kantorId').val() ? 'update' : 'create');
    
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('kantorModal')).hide();
                location.reload();
            } else {
                alert('Gagal menyimpan kantor: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function toggleStatus(id) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'toggle_status', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert('Gagal update status: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function filterKantor() {
    var filters = {
        tipe_kantor_polisi: $('#filterTipe').val(),
        klasifikasi: $('#filterKlasifikasi').val(),
        level_kompleksitas: $('#filterKompleksitas').val(),
        status: $('#filterStatus').val(),
        search: $('#searchKantor').val()
    };
    
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'list', ...filters},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateKantorTable(response.data);
            }
        },
        error: function() {
            alert('Error: Tidak dapat filter data');
        }
    });
}

function updateKantorTable(data) {
    var tbody = $('#kantorTable tbody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">Tidak ada data kantor</td></tr>');
        return;
    }
    
    data.forEach(function(kantor) {
        var row = `
            <tr class="align-middle">
                <td class="py-1">${kantor.id}</td>
                <td class="py-1">
                    <div>
                        <strong>${kantor.nama_kantor}</strong>
                        ${kantor.email ? '<br><small class="text-muted">' + kantor.email + '</small>' : ''}
                    </div>
                </td>
                <td class="py-1">
                    ${kantor.pimpinan_nama ? 
                        `<div><strong>${kantor.pimpinan_pangkat_asli}</strong><br><small class="text-muted">${kantor.pimpinan_nama}</small></div>` : 
                        `<span class="badge bg-danger">0</span>`
                    }
                </td>
                <td class="py-1"><span class="badge bg-primary">${kantor.jumlah_personel || 0}</span></td>
                <td class="py-1">
                    <span class="badge ${kantor.status === 'aktif' ? 'bg-success' : 'bg-danger'}">
                        ${kantor.status || '-'}
                    </span>
                </td>
                <td class="py-1">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewKantor(${kantor.id})"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" onclick="editKantor(${kantor.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn ${kantor.status === 'aktif' ? 'btn-secondary' : 'btn-success'}" 
                                onclick="toggleStatus(${kantor.id})" 
                                title="${kantor.status === 'aktif' ? 'Non-aktifkan' : 'Aktifkan'}">
                            <i class="fas fa-${kantor.status === 'aktif' ? 'pause' : 'play'}"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteKantor(${kantor.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function refreshKantor() {
    location.reload();
}

function exportKantor(format) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'export', format: format},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.open('ajax/kantor.php?action=download_export&filename=' + response.filename, '_blank');
            } else {
                alert('Gagal export: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat export data');
        }
    });
}
</script>
"""
        
        # Tambahkan sebelum closing body tag
        if '</body>' in content:
            content = content.replace('</body>', js_code + '\n</body>')
            print("✅ Added missing JavaScript functions")
        else:
            print("⚠️ Could not find </body> tag")
    
    # 6. Pastikan DataTables initialization ada
    if '$(document).ready(function()' not in content:
        dt_init = """
<script>
$(document).ready(function() {
    console.log('Kantor page loaded with enhanced management functionality');
    
    // Initialize DataTable
    $('#kantorTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: 'Export PDF',
                className: 'btn btn-warning btn-sm'
            }
        ]
    });
    
    // Filter handlers
    $('#filterTipe, #filterKlasifikasi, #filterKompleksitas, #filterStatus').on('change', function() {
        filterKantor();
    });
    
    // Search on enter
    $('#searchKantor').on('keypress', function(e) {
        if (e.which === 13) {
            filterKantor();
        }
    });
});
</script>
"""
        
        if '</body>' in content:
            content = content.replace('</body>', dt_init + '\n</body>')
            print("✅ Added DataTables initialization")
    
    # Simpan file
    with open(kantor_file, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ File {kantor_file} berhasil diperbaiki!")
    return True

if __name__ == "__main__":
    if fix_kantor_page():
        print("\n🎉 Perbaikan halaman kantor selesai!")
        print("🔄 Restart aplikasi untuk melihat perubahan")
    else:
        print("\n❌ Gagal memperbaiki halaman kantor")
