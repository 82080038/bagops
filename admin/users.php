<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Check authentication and super admin role
$auth = new Auth((new Database())->getConnection());
if (!$auth->isLoggedIn() || !$auth->isSuperAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get all users
function getAllUsers() {
    $pdo = (new Database())->getConnection();
    $stmt = $pdo->query("SELECT id, nrp, name, role, is_active, created_at, updated_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle_status') {
        $userId = $_POST['user_id'] ?? 0;
        $pdo = (new Database())->getConnection();
        
        // Toggle user status
        $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$userId]);
        
        header('Location: users.php?status=updated');
        exit();
    }
    
    if ($action === 'change_role') {
        $userId = $_POST['user_id'] ?? 0;
        $newRole = $_POST['new_role'] ?? 'user';
        $pdo = (new Database())->getConnection();
        
        // Update user role
        $stmt = $pdo->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newRole, $userId]);
        
        header('Location: users.php?status=updated');
        exit();
    }
    
    if ($action === 'delete_user') {
        $userId = $_POST['user_id'] ?? 0;
        $pdo = (new Database())->getConnection();
        
        // Don't allow deletion of super admin
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['role'] !== 'super_admin') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        }
        
        header('Location: users.php?status=deleted');
        exit();
    }
}

$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Super Admin</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-container {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .admin-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            color: #2c3e50;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 10px;
            display: block;
            transition: all 0.3s ease;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e1e8ed;
        }
        .admin-header h3 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .admin-header p {
            color: #7f8c8d;
            margin-bottom: 0;
        }
        .user-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .user-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
        }
        .user-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .role-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .role-super_admin {
            background: #dc3545;
            color: white;
        }
        .role-admin {
            background: #007bff;
            color: white;
        }
        .role-user {
            background: #6c757d;
            color: white;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            margin: 0 2px;
        }
        .search-box {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="admin-header">
                <i class="fas fa-user-shield fa-3x mb-3" style="color: #667eea;"></i>
                <h3>SUPER ADMIN</h3>
                <p><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users me-2"></i>User Management</a></li>
                <li><a href="settings.php"><i class="fas fa-cogs me-2"></i>System Settings</a></li>
                <li><a href="database.php"><i class="fas fa-database me-2"></i>Database</a></li>
                <li><a href="logs.php"><i class="fas fa-file-alt me-2"></i>System Logs</a></li>
                <li><a href="backup.php"><i class="fas fa-download me-2"></i>Backup & Restore</a></li>
                <li><a href="maintenance.php"><i class="fas fa-tools me-2"></i>Maintenance</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="admin-card">
                <h2><i class="fas fa-users me-2"></i>User Management</h2>
                <p class="text-muted">Manage system users and permissions</p>
                
                <?php if (isset($_GET['status'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        switch($_GET['status']) {
                            case 'updated': echo 'User updated successfully!'; break;
                            case 'deleted': echo 'User deleted successfully!'; break;
                            default: echo 'Operation completed successfully!';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Search and Filter -->
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by NRP or name...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="super_admin">Super Admin</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table user-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NRP</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['nrp']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td>
                                        <span class="role-badge role-<?= $user['role'] ?>">
                                            <?= strtoupper(str_replace('_', ' ', $user['role'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($user['updated_at'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if ($user['role'] !== 'super_admin'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-action" 
                                                        onclick="toggleStatus(<?= $user['id'] ?>)">
                                                    <i class="fas fa-toggle-<?= $user['is_active'] ? 'on' : 'off' ?>"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning btn-action" 
                                                        onclick="changeRole(<?= $user['id'] ?>, '<?= $user['role'] ?>')">
                                                    <i class="fas fa-user-tag"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-action" 
                                                        onclick="deleteUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">Protected</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4><?= count($users) ?></h4>
                            <p class="text-muted">Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4><?= count(array_filter($users, fn($u) => $u['is_active'])) ?></h4>
                            <p class="text-muted">Active Users</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4><?= count(array_filter($users, fn($u) => $u['role'] === 'user')) ?></h4>
                            <p class="text-muted">Regular Users</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4><?= count(array_filter($users, fn($u) => !empty($u['password']))) ?></h4>
                            <p class="text-muted">Registered</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden forms for actions -->
    <form id="toggleStatusForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="toggle_status">
        <input type="hidden" name="user_id" id="toggleUserId">
    </form>
    
    <form id="changeRoleForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="change_role">
        <input type="hidden" name="user_id" id="changeUserId">
        <input type="hidden" name="new_role" id="newRole">
    </form>
    
    <form id="deleteUserForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" name="user_id" id="deleteUserId">
    </form>
    
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script>
        function toggleStatus(userId) {
            if (confirm('Are you sure you want to toggle this user\'s status?')) {
                document.getElementById('toggleUserId').value = userId;
                document.getElementById('toggleStatusForm').submit();
            }
        }
        
        function changeRole(userId, currentRole) {
            const roles = ['user', 'admin', 'kabag_ops', 'kaur_ops'];
            const currentIndex = roles.indexOf(currentRole);
            const newRole = roles[(currentIndex + 1) % roles.length];
            
            if (confirm(`Change role from ${currentRole} to ${newRole}?`)) {
                document.getElementById('changeUserId').value = userId;
                document.getElementById('newRole').value = newRole;
                document.getElementById('changeRoleForm').submit();
            }
        }
        
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                document.getElementById('deleteUserId').value = userId;
                document.getElementById('deleteUserForm').submit();
            }
        }
        
        // Search functionality
        $(document).ready(function() {
            // Initialize DataTable
            $('#usersTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                columns: [
                    { data: 0, title: "ID" },
                    { data: 1, title: "NRP" },
                    { data: 2, title: "Name" },
                    { data: 3, title: "Role" },
                    { data: 4, title: "Status" },
                    { data: 5, title: "Registered" },
                    { data: 6, title: "Last Updated" },
                    { data: 7, title: "Actions", orderable: false }
                ],
                language: {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
            
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#usersTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
                });
            });
            
            $('#roleFilter').on('change', function() {
                const role = $(this).val();
                $('#usersTable tbody tr').filter(function() {
                    if (role === '') {
                        $(this).show();
                    } else {
                        $(this).toggle($(this).find('.role-badge').text().toLowerCase().includes(role.toLowerCase()));
                    }
                });
            });
            
            $('#statusFilter').on('change', function() {
                const status = $(this).val();
                $('#usersTable tbody tr').filter(function() {
                    if (status === '') {
                        $(this).show();
                    } else {
                        const isActive = $(this).find('.status-active').length > 0;
                        $(this).toggle((status === '1' && isActive) || (status === '0' && !isActive));
                    }
                });
            });
        });
    </script>
</body>
</html>
