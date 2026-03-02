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
    
    // FIXED Permission Matrix - Kabag Ops should access operations
    function canAccessModule($userRole, $module) {
        $permissions = [
            'super_admin' => ['all'],
            'admin' => ['dashboard', 'kantor', 'users', 'personel', 'operations', 'renops', 'posko', 'reports', 'settings', 'pengaturan', 'documents', 'calendar', 'analytics', 'mobile', 'struktur_organisasi', 'verifikasi_struktur', 'jabatan', 'master', 'struktur', 'daily_report', 'dashboard_main', 'operations_data', 'personel_data', 'personel_import', 'monthly_report', 'pangkat', 'assignments', 'profile'],
            'kabag_ops' => ['dashboard', 'kantor', 'renops', 'sprin', 'posko', 'reports', 'personel', 'analytics', 'struktur_organisasi', 'verifikasi_struktur', 'jabatan', 'master', 'struktur', 'daily_report', 'operations_data', 'personel_data', 'monthly_report', 'pangkat', 'assignments', 'profile', 'operations'], // FIXED: Added 'operations'
            'kaur_ops' => ['dashboard', 'kantor', 'renops', 'sprin', 'reports', 'personel', 'struktur_organisasi', 'verifikasi_struktur', 'jabatan', 'daily_report', 'operations_data', 'personel_data', 'monthly_report', 'pangkat', 'assignments', 'profile'],
            'user' => ['dashboard', 'profile', 'assignments', 'reports', 'daily_report', 'monthly_report']
        ];
        
        return in_array($module, $permissions[$userRole] ?? []) || in_array('all', $permissions[$userRole] ?? []);
    }
    
    // Database connection for real data
    $db = (new Database())->getConnection();
    
    // DYNAMIC Dashboard Content with REAL Database Data
    function getDashboardContent($db) {
        ob_start();
        
        // Get REAL statistics from database
        $totalPersonel = 0;
        $activeOperations = 0;
        $todayReports = 0;
        $pendingTasks = 0;
        
        try {
            // Real personel count
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM personel WHERE is_active = 1");
            $stmt->execute();
            $totalPersonel = $stmt->fetch()['total'];
            
            // Real operations count
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM operations WHERE status = 'active'");
            $stmt->execute();
            $activeOperations = $stmt->fetch()['total'];
            
            // Today's reports (if table exists)
            try {
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM daily_reports WHERE DATE(created_at) = CURDATE()");
                $stmt->execute();
                $todayReports = $stmt->fetch()['total'];
            } catch (Exception $e) {
                $todayReports = 0;
            }
            
            // Pending tasks (if table exists)
            try {
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM assignments WHERE status = 'pending'");
                $stmt->execute();
                $pendingTasks = $stmt->fetch()['total'];
            } catch (Exception $e) {
                $pendingTasks = 0;
            }
            
        } catch (Exception $e) {
            // Fallback to zeros if database queries fail
            $totalPersonel = 0;
            $activeOperations = 0;
            $todayReports = 0;
            $pendingTasks = 0;
        }
        
        ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                <p class="text-muted">Ringkasan aktivitas dan statistik sistem BAGOPS (Real-time Database)</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Personel</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalPersonel); ?></div>
                                <small class="text-muted">Database Real-time</small>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($activeOperations); ?></div>
                                <small class="text-muted">Database Real-time</small>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($todayReports); ?></div>
                                <small class="text-muted">Database Real-time</small>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($pendingTasks); ?></div>
                                <small class="text-muted">Database Real-time</small>
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
                        <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru (Database)</h6>
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
                                    <?php
                                    try {
                                        $stmt = $db->prepare("SELECT action, created_at FROM audit_logs ORDER BY created_at DESC LIMIT 5");
                                        $stmt->execute();
                                        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (empty($activities)) {
                                            echo '<tr><td colspan="2" class="text-center text-muted">Belum ada aktivitas</td></tr>';
                                        } else {
                                            foreach ($activities as $activity) {
                                                $time = date('H:i', strtotime($activity['created_at']));
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($activity['action']) . '</td>';
                                                echo '<td>' . $time . '</td>';
                                                echo '</tr>';
                                            }
                                        }
                                    } catch (Exception $e) {
                                        echo '<tr><td colspan="2" class="text-center text-muted">Data tidak tersedia</td></tr>';
                                    }
                                    ?>
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
    
    // REAL Personel Content from Database
    function getPersonelContent($db) {
        ob_start();
        ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-users me-2"></i>Data Personel</h2>
                <p class="text-muted">Manajemen data personel kepolisian (Database Real-time)</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Personel (<?php 
                            try {
                                $stmt = $db->prepare("SELECT COUNT(*) as total FROM personel WHERE is_active = 1");
                                $stmt->execute();
                                echo $stmt->fetch()['total'] . ' records';
                            } catch (Exception $e) {
                                echo 'Data tidak tersedia';
                            }
                        ?>)</h6>
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
                                    <?php
                                    try {
                                        $stmt = $db->prepare("SELECT nrp, nama, pangkat, jabatan, unit, is_active FROM personel WHERE is_active = 1 ORDER BY nama LIMIT 10");
                                        $stmt->execute();
                                        $personelList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (empty($personelList)) {
                                            echo '<tr><td colspan="7" class="text-center text-muted">Belum ada data personel</td></tr>';
                                        } else {
                                            foreach ($personelList as $personel) {
                                                $statusBadge = $personel['is_active'] ? 'success' : 'secondary';
                                                $statusText = $personel['is_active'] ? 'Aktif' : 'Tidak Aktif';
                                                
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($personel['nrp'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($personel['nama'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($personel['pangkat'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($personel['jabatan'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($personel['unit'] ?? '-') . '</td>';
                                                echo '<td><span class="badge badge-' . $statusBadge . '">' . $statusText . '</span></td>';
                                                echo '<td>
                                                    <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                                    <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                                </td>';
                                                echo '</tr>';
                                            }
                                        }
                                    } catch (Exception $e) {
                                        echo '<tr><td colspan="7" class="text-center text-muted">Error loading data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                    }
                                    ?>
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
    
    // REAL Operations Content from Database
    function getOperationsContent($db) {
        ob_start();
        ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2><i class="fas fa-cogs me-2"></i>Data Operasi</h2>
                <p class="text-muted">Manajemen operasi kepolisian (Database Real-time)</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Operasi (<?php 
                            try {
                                $stmt = $db->prepare("SELECT COUNT(*) as total FROM operations");
                                $stmt->execute();
                                echo $stmt->fetch()['total'] . ' records';
                            } catch (Exception $e) {
                                echo 'Data tidak tersedia';
                            }
                        ?>)</h6>
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
                                    <?php
                                    try {
                                        $stmt = $db->prepare("SELECT nama_operasi, jenis_operasi, tanggal_mulai, tanggal_selesai, status FROM operations ORDER BY created_at DESC LIMIT 10");
                                        $stmt->execute();
                                        $operationsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (empty($operationsList)) {
                                            echo '<tr><td colspan="6" class="text-center text-muted">Belum ada data operasi</td></tr>';
                                        } else {
                                            foreach ($operationsList as $operation) {
                                                $statusBadge = 'secondary';
                                                switch ($operation['status']) {
                                                    case 'active': $statusBadge = 'primary'; break;
                                                    case 'completed': $statusBadge = 'success'; break;
                                                    case 'cancelled': $statusBadge = 'danger'; break;
                                                    case 'planning': $statusBadge = 'warning'; break;
                                                }
                                                
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($operation['nama_operasi'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($operation['jenis_operasi'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($operation['tanggal_mulai'] ?? '-') . '</td>';
                                                echo '<td>' . htmlspecialchars($operation['tanggal_selesai'] ?? '-') . '</td>';
                                                echo '<td><span class="badge badge-' . $statusBadge . '">' . htmlspecialchars($operation['status'] ?? '-') . '</span></td>';
                                                echo '<td>
                                                    <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                                    <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                                </td>';
                                                echo '</tr>';
                                            }
                                        }
                                    } catch (Exception $e) {
                                        echo '<tr><td colspan="6" class="text-center text-muted">Error loading data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                    }
                                    ?>
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
                $content = getDashboardContent($db);
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul dashboard';
            }
            break;
            
        case 'personel':
            if (canAccessModule($userRole, 'personel')) {
                $content = getPersonelContent($db);
                $success = true;
            } else {
                $message = 'Akses ditolak ke modul personel';
            }
            break;
            
        case 'operations':
            if (canAccessModule($userRole, 'operations')) {
                $content = getOperationsContent($db);
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
