<?php
/**
 * Dynamic Content Service for BAGOPS
 * Provides content loading with fallback strategies
 */

class DynamicContentService {
    private $auth;
    private $db;
    
    public function __construct($auth) {
        $this->auth = $auth;
        $this->db = $auth->db ?? null;
        // Also set global db for templates
        if ($this->db) {
            $GLOBALS['db'] = $this->db;
        }
    }
    
    public function getDynamicContent($page, $userRole = 'user') {
        try {
            // Strategy 1: Try to load from pages directory
            $content = $this->loadFromPageTemplate($page, $userRole);
            if ($content !== null) {
                return [
                    'success' => true,
                    'content' => $content,
                    'source' => 'page_template',
                    'page' => $page
                ];
            }
            
            // Strategy 2: Try database content
            $content = $this->loadFromDatabase($page, $userRole);
            if ($content !== null) {
                return [
                    'success' => true,
                    'content' => $content,
                    'source' => 'database',
                    'page' => $page
                ];
            }
            
            // Strategy 3: Fallback content
            $content = $this->getFallbackContent($page, $userRole);
            return [
                'success' => true,
                'content' => $content,
                'source' => 'fallback',
                'page' => $page
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'source' => 'error'
            ];
        }
    }
    
    private function loadFromPageTemplate($page, $userRole) {
        $templateFile = __DIR__ . '/../pages/' . $page . '.php';
        
        if (!file_exists($templateFile)) {
            return null;
        }
        
        // Check permissions
        if (!$this->hasPageAccess($page, $userRole)) {
            return $this->getAccessDeniedContent();
        }
        
        // Start output buffering
        ob_start();
        
        // Include template with global variables
        $GLOBALS['db'] = $this->db;
        $GLOBALS['auth'] = $this->auth;
        $GLOBALS['current_page'] = $page;
        $GLOBALS['user_role'] = $userRole;
        
        try {
            include $templateFile;
            $content = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        
        return $content;
    }
    
    private function loadFromDatabase($page, $userRole) {
        if (!$this->db) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, pd.content, pd.custom_css, pd.custom_js
                FROM pages p 
                LEFT JOIN page_details pd ON p.id = pd.page_id 
                WHERE p.page_key = ? AND p.is_active = 1
            ");
            $stmt->execute([$page]);
            $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pageData) {
                return null;
            }
            
            // Check role access
            if ($pageData['target_role'] !== 'all' && $pageData['target_role'] !== $userRole) {
                return $this->getAccessDeniedContent();
            }
            
            // Generate content from database
            $content = $this->generateContentFromPageData($pageData);
            
            return $content;
            
        } catch (Exception $e) {
            error_log("Database content loading error: " . $e->getMessage());
            return null;
        }
    }
    
    private function generateContentFromPageData($pageData) {
        $title = htmlspecialchars($pageData['title']);
        $description = htmlspecialchars($pageData['description'] ?? '');
        
        $content = '<div class="container-fluid">';
        $content .= '<div class="d-sm-flex align-items-center justify-content-between mb-4">';
        $content .= "<h1 class='h3 mb-0 text-gray-800'>{$title}</h1>";
        $content .= '</div>';
        
        // Add database content if available
        if (!empty($pageData['content'])) {
            $content .= '<div class="row">';
            $content .= '<div class="col-12">';
            $content .= $pageData['content']; // HTML content from database
            $content .= '</div>';
            $content .= '</div>';
        } else {
            // Default content based on page type
            $content .= $this->getDefaultContentByPageType($pageData['page_key'], $pageData['page_type']);
        }
        
        $content .= '</div>';
        
        return $content;
    }
    
    private function getDefaultContentByPageType($pageKey, $pageType) {
        switch ($pageType) {
            case 'dashboard':
                return $this->getDashboardContent($pageKey);
            case 'settings':
                return $this->getSettingsContent($pageKey);
            case 'report':
                return $this->getReportContent($pageKey);
            default:
                return $this->getStandardContent($pageKey);
        }
    }
    
    private function getDashboardContent($pageKey) {
        return '<div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Data</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    private function getSettingsContent($pageKey) {
        return '<div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pengaturan ' . htmlspecialchars($pageKey) . '</h6>
            </div>
            <div class="card-body">
                <p>Halaman pengaturan untuk ' . htmlspecialchars($pageKey) . '.</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Fitur pengaturan akan segera tersedia.
                </div>
            </div>
        </div>';
    }
    
    private function getReportContent($pageKey) {
        return '<div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Laporan ' . htmlspecialchars($pageKey) . '</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data laporan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>';
    }
    
    private function getStandardContent($pageKey) {
        return '<div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">' . htmlspecialchars(ucwords(str_replace('_', ' ', $pageKey))) . '</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Halaman ' . htmlspecialchars(ucwords(str_replace('_', ' ', $pageKey))) . ' sedang dalam pengembangan.
                </div>
            </div>
        </div>';
    }
    
    private function getFallbackContent($page, $userRole) {
        return '<div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">' . htmlspecialchars(ucwords(str_replace('_', ' ', $page))) . '</h1>
            </div>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Halaman <strong>' . htmlspecialchars($page) . '</strong> tidak ditemukan atau sedang dalam pengembangan.
                    </div>
                    <p class="mb-0">
                        <small class="text-muted">Role: ' . htmlspecialchars($userRole) . ' | Page: ' . htmlspecialchars($page) . '</small>
                    </p>
                </div>
            </div>
        </div>';
    }
    
    private function getAccessDeniedContent() {
        return '<div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-ban"></i> 
                        <strong>Akses Ditolak!</strong> Anda tidak memiliki izin untuk mengakses halaman ini.
                    </div>
                </div>
            </div>
        </div>';
    }
    
    private function hasPageAccess($page, $userRole) {
        if (!$this->db) {
            return true; // Allow access if no database
        }
        
        try {
            // Check page exists and is active
            $stmt = $this->db->prepare("
                SELECT id, target_role FROM pages 
                WHERE page_key = ? AND is_active = 1
            ");
            $stmt->execute([$page]);
            $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pageData) {
                return false;
            }
            
            // Check role access
            if ($pageData['target_role'] === 'all') {
                return true;
            }
            
            // Check specific permissions
            $stmt = $this->db->prepare("
                SELECT is_granted FROM page_permissions 
                WHERE page_id = ? AND role_name = ? AND permission_type = 'view'
            ");
            $stmt->execute([$pageData['id'], $userRole]);
            $permission = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $permission && $permission['is_granted'];
            
        } catch (Exception $e) {
            error_log("Permission check error: " . $e->getMessage());
            return true; // Allow access on error
        }
    }
}
?>
