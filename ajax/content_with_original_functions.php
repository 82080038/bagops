<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

try {
    $auth = new Auth((new Database())->getConnection());
    $user = $auth->getCurrentUser();
    $userRole = $user['role'] ?? 'user';
    $userId = $user['id'] ?? 0;
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit();
    }
    
    $page = $_POST['page'] ?? 'dashboard';
    $success = false;
    $message = '';
    $content = '';
    
    // Permission checking function
    function canAccessModule($userRole, $module) {
        $permissions = [
            'super_admin' => ['all'],
            'admin' => ['dashboard', 'kantor', 'users', 'personel', 'operations', 'renops', 'posko', 'reports', 'settings', 'pengaturan', 'documents', 'calendar', 'analytics', 'mobile', 'struktur_organisasi', 'verifikasi_struktur', 'jabatan', 'master', 'struktur', 'daily_report', 'dashboard_main', 'operations_data', 'personel_data', 'personel_import', 'monthly_report', 'pangkat', 'assignments', 'profile'],
            'kabag_ops' => ['dashboard', 'kantor', 'renops', 'sprin', 'posko', 'reports', 'personel', 'analytics', 'struktur_organisasi', 'verifikasi_struktur', 'jabatan', 'master', 'struktur', 'daily_report', 'operations_data', 'personel_data', 'monthly_report', 'pangkat', 'assignments', 'profile'],
            'kaur_ops' => ['dashboard', 'kantor', 'renops', 'sprin', 'reports', 'personel', 'struktur_organisasi', 'verifikasi_struktur', 'jabatan', 'daily_report', 'operations_data', 'personel_data', 'monthly_report', 'pangkat', 'assignments', 'profile'],
            'user' => ['dashboard', 'profile', 'assignments', 'reports', 'daily_report', 'monthly_report']
        ];
        
        return in_array($module, $permissions[$userRole] ?? []) || in_array('all', $permissions[$userRole] ?? []);
    }
    
    // Original Dashboard Content Function
    function getDashboardContent() {
        ob_start();
        ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                <p class="text-muted">Ringkasan aktivitas dan statistik sistem BAGOPS</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Personel</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">1,234</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Operasi Aktif</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cogs fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Laporan Hari Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tugas Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">5</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tasks fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Aktivitas</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Login Super Admin</td>
                                        <td>2 menit yang lalu</td>
                                    </tr>
                                    <tr>
                                        <td>Tambah Personel Baru</td>
                                        <td>1 jam yang lalu</td>
                                    </tr>
                                    <tr>
                                        <td>Buat Operasi Baru</td>
                                        <td>2 jam yang lalu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <button class="btn btn-primary btn-block">
                                    <i class="fas fa-user-plus"></i> Tambah Personel
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Buat Operasi
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button class="btn btn-info btn-block">
                                    <i class="fas fa-file-alt"></i> Laporan Harian
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button class="btn btn-warning btn-block">
                                    <i class="fas fa-chart-bar"></i> Analytics
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Original Personel Content Function
    function getPersonelContent() {
        ob_start();
        ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-users me-2"></i>Data Personel</h2>
                <p class="text-muted">Manajemen data personel kepolisian</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Personel</h6>
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Personel
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="ajaxPersonelTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>NRP</th>
                                        <th>Nama</th>
                                        <th>Pangkat</th>
                                        <th>Jabatan</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>80123456</td>
                                        <td>Ahmad Wijaya</td>
                                        <td>Kompol</td>
                                        <td>Kapolsek</td>
                                        <td>Polsek Samosir</td>
                                        <td><span class="badge badge-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>80123457</td>
                                        <td>Budi Santoso</td>
                                        <td>Akp</td>
                                        <td>Kanit Reskrim</td>
                                        <td>Polsek Samosir</td>
                                        <td><span class="badge badge-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>80123458</td>
                                        <td>Chandra Dewi</td>
                                        <td>Iptu</td>
                                        <td>Kanit Intel</td>
                                        <td>Polsek Samosir</td>
                                        <td><span class="badge badge-warning">Cuti</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Original Operations Content Function
    function getOperationsContent() {
        ob_start();
        ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-cogs me-2"></i>Data Operasi</h2>
                <p class="text-muted">Manajemen operasi kepolisian</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Operasi</h6>
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Buat Operasi
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="ajaxOperationsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nama Operasi</th>
                                        <th>Jenis</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Operasi PPKM Darurat</td>
                                        <td>Penegakan Hukum</td>
                                        <td>2024-01-15</td>
                                        <td>2024-01-20</td>
                                        <td><span class="badge badge-success">Selesai</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Operasi Lilin Samosir</td>
                                        <td>Pengamanan</td>
                                        <td>2024-02-01</td>
                                        <td>2024-02-15</td>
                                        <td><span class="badge badge-primary">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Operasi Yustisi</td>
                                        <td>Penegakan Disiplin</td>
                                        <td>2024-02-10</td>
                                        <td>2024-02-12</td>
                                        <td><span class="badge badge-warning">Planning</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    switch ($page) {
        case 'dashboard':
            if (canAccessModule($userRole, 'dashboard')) {
                $content = getDashboardContent();
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul dashboard';
            }
            break;
            
        case 'personel':
            if (canAccessModule($userRole, 'personel')) {
                $content = getPersonelContent();
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul personel';
            }
            break;
            
        case 'operations':
            if (canAccessModule($userRole, 'operations')) {
                $content = getOperationsContent();
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul operations';
            }
            break;
            
        case 'reports':
            if (canAccessModule($userRole, 'reports')) {
                $content = '<div class="container-fluid"><h2><i class="fas fa-file-alt me-2"></i>Laporan</h2><p class="text-muted">Manajemen laporan operasional</p><div class="alert alert-info">Fitur laporan akan segera tersedia</div></div>';
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul reports';
            }
            break;
            
        case 'settings':
            if ($userRole === 'super_admin' || $userRole === 'admin') {
                $content = '<div class="container-fluid"><h2><i class="fas fa-cog me-2"></i>Pengaturan</h2><p class="text-muted">Pengaturan sistem</p><div class="alert alert-info">Fitur pengaturan akan segera tersedia</div></div>';
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul settings';
            }
            break;
            
        case 'master':
            if (canAccessModule($userRole, 'master')) {
                $content = '<div class="container-fluid"><h2><i class="fas fa-database me-2"></i>Data Master</h2><p class="text-muted">Manajemen data master sistem</p><div class="alert alert-info">Fitur data master akan segera tersedia</div></div>';
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul master';
            }
            break;
            
        default:
            $content = '<div class="container-fluid"><h2>' . htmlspecialchars($page) . '</h2><p class="text-muted">Halaman dalam pengembangan</p></div>';
            $success = true;
            break;
    }
    
    echo json_encode(['success' => $success, 'content' => $content, 'message' => $message]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
