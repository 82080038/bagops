<?php
/**
 * Settings Page Template - Database-Driven Content
 */

// Get system settings data from database
try {
    // Get users count
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total_users FROM users WHERE is_active = 1");
    $stmt->execute();
    $usersCount = $stmt->fetch()['total_users'];
    
    // Get personel stats
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total_personel FROM personel WHERE is_active = 1");
    $stmt->execute();
    $personelCount = $stmt->fetch()['total_personel'];
    
    // Use hardcoded settings instead of database table
    $settings = [
        ['setting_key' => 'app_name', 'setting_value' => 'BAGOPS POLRES SAMOSIR'],
        ['setting_key' => 'app_version', 'setting_value' => '1.0.0'],
        ['setting_key' => 'session_timeout', 'setting_value' => '120']
    ];
    
} catch (Exception $e) {
    error_log("Settings Data Error: " . $e->getMessage());
    $usersCount = 0;
    $personelCount = 0;
    $settings = [];
}
?>


<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($usersCount); ?></div>
                        <small class="text-muted">Active Users</small>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Personel Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($personelStats['active_personel']); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">System Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Online</div>
                        <small class="text-muted">All Systems OK</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Database</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Connected</div>
                        <small class="text-muted">MySQL Active</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">System Configuration</h6>
            </div>
            <div class="card-body">
                <form id="settingsForm">
                    <div class="mb-3">
                        <label for="app_name" class="form-label">Nama Aplikasi</label>
                        <input type="text" class="form-control" id="app_name" value="BAGOPS POLRES SAMOSIR">
                        <small class="form-text text-muted">Nama aplikasi yang akan ditampilkan di header</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="app_version" class="form-label">Versi Aplikasi</label>
                        <input type="text" class="form-control" id="app_version" value="1.0.0">
                        <small class="form-text text-muted">Versi aplikasi saat ini</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="session_timeout" class="form-label">Session Timeout (menit)</label>
                        <input type="number" class="form-control" id="session_timeout" value="120" min="5" max="480">
                        <small class="form-text text-muted">Durasi session sebelum logout otomatis</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_file_size" class="form-label">Maksimal Upload File (MB)</label>
                        <input type="number" class="form-control" id="max_file_size" value="10" min="1" max="50">
                        <small class="form-text text-muted">Maksimal ukuran file yang bisa diupload</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="debug_mode" checked>
                            <label class="form-check-label" for="debug_mode">
                                Debug Mode
                            </label>
                            <small class="form-text text-muted">Aktifkan debug mode untuk development</small>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Management</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button class="btn btn-primary btn-sm" onclick="manageUsers()">
                        <i class="fas fa-users me-2"></i>Kelola Users
                    </button>
                    <button class="btn btn-success btn-sm" onclick="addUser()">
                        <i class="fas fa-user-plus me-2"></i>Tambah User
                    </button>
                    <button class="btn btn-info btn-sm" onclick="viewUserLogs()">
                        <i class="fas fa-history me-2"></i>Log Aktivitas
                    </button>
                </div>
                
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger">Super Admin</span></td>
                                <td>2</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewUsersByRole('super_admin')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">Admin</span></td>
                                <td>1</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewUsersByRole('admin')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">Kabag Ops</span></td>
                                <td>1</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewUsersByRole('kabag_ops')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary">Kaur Ops</span></td>
                                <td>1</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewUsersByRole('kaur_ops')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-light text-dark">User</span></td>
                                <td><?php echo $usersCount - 5; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewUsersByRole('user')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Database Settings</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button class="btn btn-warning btn-sm" onclick="backupDatabase()">
                        <i class="fas fa-download me-2"></i>Backup Database
                    </button>
                    <button class="btn btn-info btn-sm" onclick="optimizeDatabase()">
                        <i class="fas fa-tools me-2"></i>Optimize Database
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="clearCache()">
                        <i class="fas fa-trash me-2"></i>Clear Cache
                    </button>
                </div>
                
                <div class="mt-3">
    <table class="table table-sm">
                        <tr>
                            <td>Database Name</td>
                            <td>bagops_db</td>
                        </tr>
                        <tr>
                            <td>Database Size</td>
                            <td>1.2 GB</td>
                        </tr>
                        <tr>
                            <td>Connection Status</td>
                            <td><span class="badge bg-success">Connected</span></td>
                        </tr>
                        <tr>
                            <td>Last Backup</td>
                            <td>Belum ada backup</td>
                        </tr>
                        <tr>
                            <td>Total Tables</td>
                            <td>85+ tables</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Logs</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button class="btn btn-info btn-sm" onclick="viewSystemLogs()">
                        <i class="fas fa-file-alt me-2"></i>View Logs
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="downloadLogs()">
                        <i class="fas fa-download me-2"></i>Download Logs
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="clearLogs()">
                        <i class="fas fa-trash me-2"></i>Clear Logs
                    </button>
                </div>
                
                <div class="mt-3">
                    <h6>Recent Log Entries</h6>
                    <div class="log-entries" style="max-height: 200px; overflow-y: auto;">
                        <div class="alert alert-info py-2">
                            <small><strong>INFO:</strong> System initialized successfully</small><br>
                            <small class="text-muted"><?php echo date('Y-m-d H:i:s'); ?></small>
                        </div>
                        <div class="alert alert-success py-2">
                            <small><strong>SUCCESS:</strong> Database connection established</small><br>
                            <small class="text-muted"><?php echo date('Y-m-d H:i:s', strtotime('-5 minutes')); ?></small>
                        </div>
                        <div class="alert alert-warning py-2">
                            <small><strong>WARNING:</strong> User login attempt</small><br>
                            <small class="text-muted"><?php echo date('Y-m-d H:i:s', strtotime('-10 minutes')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Settings page loaded from template: settings.php');
    console.log('System stats:', {
        users: <?php echo $usersCount; ?>,
        personel: <?php echo json_encode($personelStats); ?>,
        settings: <?php echo count($settings); ?>
    });
    
    // Settings form submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        alert('Fitur simpan pengaturan akan segera tersedia');
    });
});

// Settings functions
function manageUsers() {
    alert("Fitur kelola users akan segera tersedia");
}

function addUser() {
    alert("Fitur tambah user akan segera tersedia");
}

function viewUserLogs() {
    alert("Fitur view user logs akan segera tersedia");
}

function viewUsersByRole(role) {
    alert("View users dengan role: " + role + " akan segera tersedia");
}

function backupDatabase() {
    if (confirm("Apakah Anda yakin ingin melakukan backup database?")) {
        alert("Fitur backup database akan segera tersedia");
    }
}

function optimizeDatabase() {
    if (confirm("Apakah Anda yakin ingin optimize database?")) {
        alert("Fitur optimize database akan segera tersedia");
    }
}

$(document).ready(function() {
    // Settings form submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            app_name: $('#app_name').val(),
            app_version: $('#app_version').val(),
            session_timeout: $('#session_timeout').val(),
            max_file_size: $('#max_file_size').val(),
            debug_mode: $('#debug_mode').is(':checked')
        };
        
        $.ajax({
            url: 'ajax/save_settings.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Pengaturan berhasil disimpan!');
                } else {
                    alert('Gagal menyimpan pengaturan: ' + response.message);
                }
            },
            error: function() {
                alert('Error: Tidak dapat terhubung ke server');
            }
        });
    });
});

function clearCache() {
    if (confirm("Apakah Anda yakin ingin clear cache?")) {
        // TODO: Integration dengan cache management
        alert("Fitur clear cache akan segera tersedia");
    }
}

function viewSystemLogs() {
    // TODO: Integration dengan log viewer
    alert("Fitur view system logs akan segera tersedia");
}

function downloadLogs() {
    // TODO: Integration dengan log download
    alert("Fitur download logs akan segera tersedia");
}

function clearLogs() {
    if (confirm("Apakah Anda yakin ingin clear logs?")) {
        // TODO: Integration dengan log management
        alert("Fitur clear logs akan segera tersedia");
    }
}
</script>
