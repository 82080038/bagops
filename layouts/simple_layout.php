<?php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/**
 * Simplified Main Layout Template for Root Page System
 */

// Get global variables
$pageTitle = $GLOBALS['page_title'] ?? 'BAGOPS POLRES SAMOSIR';
$pageDescription = $GLOBALS['page_description'] ?? 'Sistem BAGOPS POLRES SAMOSIR';
$pageKeywords = $GLOBALS['page_keywords'] ?? 'bagops, polres, samosir';
$pageData = $GLOBALS['page_data'] ?? [];
$currentPage = $GLOBALS['current_page'] ?? 'dashboard';
$userRole = $GLOBALS['user_role'] ?? 'user';
$customCss = $GLOBALS['custom_css'] ?? '';
$customJs = $GLOBALS['custom_js'] ?? '';

// Pass database connection to templates
$db = $GLOBALS['db'] ?? null;
$auth = $GLOBALS['auth'] ?? null;

// Get current user info
$currentUser = $auth ? $auth->getCurrentUser() : ['username' => 'Guest', 'role' => 'guest'];
if (!$currentUser) {
    $currentUser = ['username' => 'Guest', 'role' => 'guest'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
    
    <!-- jQuery (moved to head for early loading) -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables Plugin -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <link href="assets/css/jquery.dataTables.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .card.border-left-primary { border-left: 4px solid #007bff !important; }
        .card.border-left-success { border-left: 4px solid #28a745 !important; }
        .card.border-left-info { border-left: 4px solid #17a2b8 !important; }
        .card.border-left-warning { border-left: 4px solid #ffc107 !important; }
        .badge-primary { background-color: #007bff !important; }
        .badge-success { background-color: #28a745 !important; }
        .badge-info { background-color: #17a2b8 !important; }
        .badge-warning { background-color: #ffc107 !important; color: #212529 !important; }
        .badge-secondary { background-color: #6c757d !important; }
        .badge-danger { background-color: #dc3545 !important; }
        
        /* Header and Content Spacing */
        .main-content {
            margin-top: 70px !important;
            padding-top: 5px !important;
        }
        
        .breadcrumb-container {
            padding: 8px 0 !important;
            margin-bottom: 5px !important;
            border-bottom: 1px solid #e9ecef;
        }
        
        .page-header {
            padding: 10px 0 !important;
            margin-bottom: 15px !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid #e9ecef;
        }
        
        .page-content {
            padding: 5px 0 !important;
        }
        
        .page-title {
            margin-bottom: 8px !important;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .page-description {
            margin-bottom: 0 !important;
            font-size: 0.95rem;
        }
        
        /* Card spacing improvements */
        .card {
            margin-bottom: 15px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card-header {
            padding: 12px 20px !important;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-body {
            padding: 20px !important;
        }
        
        /* Table spacing */
        .table {
            margin-bottom: 0 !important;
        }
        
        .table-responsive {
            margin-bottom: 15px !important;
            width: 100% !important;
        }
        
        /* Make DataTables use full width */
        #personelTable_wrapper .dataTables_wrapper {
            width: 100% !important;
        }
        
        #personelTable {
            width: 100% !important;
        }
        
        #personelTable_wrapper .dataTables_scroll {
            width: 100% !important;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            width: 100% !important;
        }
        
        /* Fix dropdown z-index and positioning */
        .navbar-nav .dropdown-menu {
            z-index: 1050 !important;
            position: absolute !important;
        }
        
        .navbar-nav .dropdown.show .dropdown-menu {
            display: block !important;
        }
        
        /* Ensure dropdown is clickable */
        .dropdown-toggle {
            cursor: pointer !important;
        }
        
        /* Fix dropdown positioning */
        .dropdown-menu.dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }
        
        /* Form spacing */
        .form-group {
            margin-bottom: 15px !important;
        }
        
        .form-control, .form-select {
            margin-bottom: 0 !important;
        }
        
        /* Button spacing */
        .btn {
            margin-right: 5px !important;
            margin-bottom: 5px !important;
        }
        
        .btn:last-child {
            margin-right: 0 !important;
        }
        
        /* Alert spacing */
        .alert {
            margin-bottom: 15px !important;
            padding: 12px 20px !important;
        }
        
        /* Modal spacing */
        .modal-header {
            padding: 15px 20px !important;
            border-bottom: 1px solid #e9ecef;
        }
        
        .modal-body {
            padding: 20px !important;
        }
        
        .modal-footer {
            padding: 15px 20px !important;
            border-top: 1px solid #e9ecef;
        }
        
        /* Loading overlay improvements */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-header {
                padding: 8px 0 !important;
            }
            
            .page-title {
                font-size: 1.25rem !important;
            }
            
            .page-description {
                font-size: 0.875rem !important;
            }
            
            .card-body {
                padding: 15px !important;
            }
        }
    </style>
    
    <!-- Page-specific CSS -->
    <?php if (!empty($customCss)): ?>
    <style><?php echo $customCss; ?></style>
    <?php endif; ?>
</head>
<body class="bg-light">
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat halaman...</p>
    </div>

    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" data-bs-theme="dark">
        <div class="container-fluid px-4">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="simple_root_system.php?page=dashboard">
                <i class="fas fa-shield-alt me-2"></i>
                <span class="fw-bold">BAGOPS</span>
                <span class="d-none d-lg-inline ms-1 text-muted">POLRES SAMOSIR</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" aria-controls="navbarNav" 
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation -->
                <ul class="navbar-nav mx-auto">
                    <?php
                    // Simple navigation rendering
                    try {
                        require_once 'config/database.php';
                        $db = (new Database())->getConnection();
                        
                        $stmt = $db->prepare("
                            SELECT page_key, title, target_role 
                            FROM pages 
                            WHERE is_active = 1 
                            AND (target_role = :role OR target_role = 'all')
                            ORDER BY order_index
                        ");
                        $stmt->bindParam(':role', $userRole);
                        $stmt->execute();
                        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($pages as $page) {
                            $activeClass = ($page['page_key'] === $currentPage) ? 'active' : '';
                            echo '<li class="nav-item">';
                            echo '<a class="nav-link ' . $activeClass . '" href="simple_root_system.php?page=' . htmlspecialchars($page['page_key']) . '">';
                            echo '<span>' . htmlspecialchars($page['title']) . '</span>';
                            echo '</a>';
                            echo '</li>';
                        }
                    } catch (Exception $e) {
                        echo '<li class="nav-item"><a class="nav-link" href="simple_root_system.php?page=dashboard">Dashboard</a></li>';
                        echo '<li class="nav-item"><a class="nav-link" href="simple_root_system.php?page=personel">Personel</a></li>';
                    }
                    ?>
                </ul>

                <!-- Right Navigation -->
                <ul class="navbar-nav">
                    <!-- User Profile -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-3" href="#" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://picsum.photos/seed/<?php echo htmlspecialchars($currentUser['username']); ?>/35/35" 
                                 alt="User Avatar" class="rounded-circle border-2 border-white">
                            <div class="d-none d-lg-block text-start">
                                <div class="fw-semibold"><?php echo htmlspecialchars($currentUser['username']); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?></div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header"><?php echo htmlspecialchars($currentUser['username']); ?></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-3" href="simple_root_system.php?page=profile">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger d-flex align-items-center gap-3" href="login.php">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Keluar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-container" aria-label="breadcrumb">
            <div class="container-fluid px-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="simple_root_system.php?page=dashboard">
                            <i class="fas fa-home me-1"></i>
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(ucfirst($currentPage)); ?></li>
                </ol>
            </div>
        </nav>

        
        <!-- Page Content -->
        <div class="page-content">
            <div class="container-fluid px-4">
                <?php
                // Render page content
                try {
                    $templateFile = __DIR__ . "/../pages/{$currentPage}.php";
                    if (file_exists($templateFile)) {
                        include $templateFile;
                    } else {
                        echo '<div class="alert alert-info">';
                        echo '<h5><i class="fas fa-info-circle me-2"></i>Halaman ' . htmlspecialchars(ucfirst($currentPage)) . '</h5>';
                        echo '<p class="mb-0">Konten halaman ini sedang dalam pengembangan.</p>';
                        echo '<small>Template file: ' . $templateFile . ' tidak ditemukan</small>';
                        echo '</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">';
                    echo '<h5><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>';
                    echo '<p class="mb-0">' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer bg-light py-3 mt-4">
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> BAGOPS POLRES SAMOSIR. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Root Page System | Page: <?php echo htmlspecialchars($currentPage); ?> | 
                        Role: <?php echo htmlspecialchars($userRole); ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap 5.3 JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Bootstrap Components -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdowns
        var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        var dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
            return new bootstrap.Dropdown(dropdownTriggerEl);
        });
        
        console.log('Bootstrap dropdowns initialized:', dropdownList.length);
        
        // Manual dropdown toggle for debugging
        document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var dropdown = this.closest('.dropdown');
                var menu = dropdown.querySelector('.dropdown-menu');
                
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    // Close other dropdowns
                    document.querySelectorAll('.dropdown.show').forEach(function(openDropdown) {
                        openDropdown.classList.remove('show');
                        openDropdown.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
                    });
                    
                    // Open this dropdown
                    dropdown.classList.add('show');
                    this.setAttribute('aria-expanded', 'true');
                    
                    // Position menu
                    var rect = this.getBoundingClientRect();
                    menu.style.top = (rect.bottom + window.scrollY) + 'px';
                    menu.style.right = '0px';
                    menu.style.left = 'auto';
                }
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach(function(dropdown) {
                    dropdown.classList.remove('show');
                    dropdown.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
                });
            }
        });
    });
    </script>
    
    <!-- Custom JavaScript -->
    
    <!-- Page-specific JavaScript -->
    <?php if (!empty($customJs)): ?>
    <script><?php echo $customJs; ?></script>
    <?php endif; ?>

    <script>
        // Initialize page
        $(document).ready(function() {
            // Hide loading overlay
            $('#loadingOverlay').fadeOut();
            
            console.log('Root Page System loaded');
            console.log('Page: <?php echo $currentPage; ?>');
            console.log('Role: <?php echo $userRole; ?>');
        });
    </script>
</body>
</html>
