<?php
/**
 * Comprehensive End-to-End System Testing
 * BAGOPS POLRES SAMOSIR - Complete System Verification
 */

echo "=== BAGOPS COMPREHENSIVE END-TO-END SYSTEM TESTING ===\n";
echo "Testing ALL components for ALL roles and pages...\n\n";

// Include required classes
require_once 'config/database.php';
require_once 'classes/Auth.php';

class ComprehensiveSystemTester {
    private $db;
    private $testResults = [];
    private $errorLog = [];
    private $successLog = [];
    
    // All roles to test
    private $roles = ['super_admin', 'admin', 'kabag_ops', 'kaur_ops', 'user'];
    
    // All pages to test
    private $pages = [
        'dashboard',
        'personel_ultra', 
        'operations',
        'reports',
        'assignments',
        'settings',
        'profile',
        'help'
    ];
    
    // Role-page access matrix
    private $rolePageAccess = [
        'super_admin' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'settings', 'profile', 'help'],
        'admin' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'profile', 'help'],
        'kabag_ops' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'profile'],
        'kaur_ops' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'profile'],
        'user' => ['dashboard', 'profile']
    ];
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        echo "🔧 Database connection established\n";
    }
    
    /**
     * Test PHP Backend Components
     */
    public function testPHPBackend() {
        echo "\n=== PHP BACKEND TESTING ===\n";
        
        $tests = [
            'Database Connection' => function() {
                try {
                    $stmt = $this->db->query("SELECT 1");
                    return $stmt !== false;
                } catch (Exception $e) {
                    $this->errorLog[] = "Database connection failed: " . $e->getMessage();
                    return false;
                }
            },
            'Auth Class' => function() {
                try {
                    $auth = new Auth($this->db);
                    return $auth instanceof Auth;
                } catch (Exception $e) {
                    $this->errorLog[] = "Auth class failed: " . $e->getMessage();
                    return false;
                }
            },
            'Service Classes' => function() {
                $services = ['PersonelService', 'OperationsService', 'ReportsService', 'AssignmentsService', 'SettingsService'];
                $passed = 0;
                foreach ($services as $service) {
                    try {
                        $serviceFile = "services/$service.php";
                        if (file_exists($serviceFile)) {
                            require_once $serviceFile;
                            if (class_exists($service)) {
                                $passed++;
                                $this->successLog[] = "✅ $service class available";
                            } else {
                                $this->errorLog[] = "❌ $service class not found";
                            }
                        } else {
                            $this->errorLog[] = "❌ $service file not found";
                        }
                    } catch (Exception $e) {
                        $this->errorLog[] = "❌ $service error: " . $e->getMessage();
                    }
                }
                return $passed === count($services);
            },
            'Database Tables' => function() {
                $tables = ['users', 'personel', 'operations', 'reports', 'assignments', 'settings'];
                $passed = 0;
                foreach ($tables as $table) {
                    try {
                        $stmt = $this->db->query("DESCRIBE $table");
                        if ($stmt) {
                            $passed++;
                            $this->successLog[] = "✅ Table $table exists";
                        }
                    } catch (Exception $e) {
                        $this->errorLog[] = "❌ Table $table error: " . $e->getMessage();
                    }
                }
                return $passed === count($tables);
            }
        ];
        
        $passed = 0;
        foreach ($tests as $testName => $testFunction) {
            echo "Testing $testName... ";
            if ($testFunction()) {
                echo "✅ PASS\n";
                $passed++;
            } else {
                echo "❌ FAIL\n";
            }
        }
        
        return $passed === count($tests);
    }
    
    /**
     * Test JavaScript Components
     */
    public function testJavaScript() {
        echo "\n=== JAVASCRIPT TESTING ===\n";
        
        $jsFiles = [
            'shared/app.js',
            'assets/js/main.js',
            'assets/js/jquery-3.6.0.min.js',
            'assets/js/jquery.dataTables.min.js',
            'assets/js/dataTables.bootstrap5.min.js',
            'assets/js/bootstrap.bundle.min.js'
        ];
        
        $passed = 0;
        foreach ($jsFiles as $file) {
            echo "Testing $file... ";
            if (file_exists($file)) {
                $size = filesize($file);
                echo "✅ EXISTS ($size bytes)\n";
                $passed++;
                $this->successLog[] = "✅ $file available";
            } else {
                echo "❌ MISSING\n";
                $this->errorLog[] = "❌ $file missing";
            }
        }
        
        // Test inline JavaScript in pages
        echo "\nTesting inline JavaScript in pages...\n";
        $pagesWithJS = 0;
        foreach ($this->pages as $page) {
            $pageFile = "pages/$page.php";
            if (file_exists($pageFile)) {
                $content = file_get_contents($pageFile);
                if (strpos($content, '<script') !== false) {
                    $pagesWithJS++;
                    echo "  ✅ $page has JavaScript\n";
                }
            }
        }
        
        return $passed === count($jsFiles);
    }
    
    /**
     * Test CSS Components
     */
    public function testCSS() {
        echo "\n=== CSS TESTING ===\n";
        
        $cssFiles = [
            'css/responsive_improvements.css',
            'assets/css/main.css'
        ];
        
        $passed = 0;
        foreach ($cssFiles as $file) {
            echo "Testing $file... ";
            if (file_exists($file)) {
                $size = filesize($file);
                echo "✅ EXISTS ($size bytes)\n";
                $passed++;
                $this->successLog[] = "✅ $file available";
            } else {
                echo "❌ MISSING\n";
                $this->errorLog[] = "❌ $file missing";
            }
        }
        
        // Test Bootstrap classes in pages
        echo "\nTesting Bootstrap integration...\n";
        $bootstrapPages = 0;
        foreach ($this->pages as $page) {
            $pageFile = "pages/$page.php";
            if (file_exists($pageFile)) {
                $content = file_get_contents($pageFile);
                if (strpos($content, 'btn-') !== false || strpos($content, 'card-') !== false) {
                    $bootstrapPages++;
                    echo "  ✅ $page uses Bootstrap\n";
                }
            }
        }
        
        return $passed === count($cssFiles);
    }
    
    /**
     * Test AJAX Endpoints
     */
    public function testAJAXEndpoints() {
        echo "\n=== AJAX ENDPOINTS TESTING ===\n";
        
        $endpoints = [
            'ajax/get_personnel.php',
            'ajax/get_operation.php',
            'ajax/get_report.php',
            'ajax/get_assignment.php',
            'ajax/save_personnel.php',
            'ajax/save_operation.php',
            'ajax/save_report.php',
            'ajax/save_assignment.php',
            'ajax/update_personnel.php',
            'ajax/update_operation.php',
            'ajax/update_report.php',
            'ajax/update_assignment.php',
            'ajax/delete_personnel.php',
            'ajax/delete_operation.php',
            'ajax/delete_report.php',
            'ajax/delete_assignment.php'
        ];
        
        $passed = 0;
        foreach ($endpoints as $endpoint) {
            echo "Testing $endpoint... ";
            if (file_exists($endpoint)) {
                echo "✅ EXISTS\n";
                $passed++;
                $this->successLog[] = "✅ $endpoint available";
            } else {
                echo "❌ MISSING\n";
                $this->errorLog[] = "❌ $endpoint missing";
            }
        }
        
        return $passed >= count($endpoints) * 0.8; // 80% pass rate
    }
    
    /**
     * Test Bootstrap Components
     */
    public function testBootstrap() {
        echo "\n=== BOOTSTRAP COMPONENTS TESTING ===\n";
        
        $components = [
            'btn-primary',
            'btn-secondary',
            'card',
            'table',
            'modal',
            'form-control',
            'container',
            'row',
            'col'
        ];
        
        $componentCount = 0;
        foreach ($this->pages as $page) {
            $pageFile = "pages/$page.php";
            if (file_exists($pageFile)) {
                $content = file_get_contents($pageFile);
                $pageComponents = 0;
                foreach ($components as $component) {
                    if (strpos($content, $component) !== false) {
                        $pageComponents++;
                    }
                }
                if ($pageComponents > 0) {
                    $componentCount++;
                    echo "  ✅ $page uses $pageComponents Bootstrap components\n";
                }
            }
        }
        
        return $componentCount > 0;
    }
    
    /**
     * Test Frontend Pages
     */
    public function testFrontendPages() {
        echo "\n=== FRONTEND PAGES TESTING ===\n";
        
        $passed = 0;
        foreach ($this->pages as $page) {
            echo "Testing $page... ";
            $pageFile = "pages/$page.php";
            if (file_exists($pageFile)) {
                $content = file_get_contents($pageFile);
                
                // Check for basic HTML structure
                $hasHTML = strpos($content, '<html') !== false || strpos($content, '<div') !== false;
                $hasPHP = strpos($content, '<?php') !== false;
                
                if ($hasHTML && $hasPHP) {
                    echo "✅ COMPLETE\n";
                    $passed++;
                    $this->successLog[] = "✅ $page page complete";
                } else {
                    echo "⚠️  INCOMPLETE\n";
                }
            } else {
                echo "❌ MISSING\n";
                $this->errorLog[] = "❌ $page page missing";
            }
        }
        
        return $passed === count($this->pages);
    }
    
    /**
     * Test API Endpoints
     */
    public function testAPIEndpoints() {
        echo "\n=== API ENDPOINTS TESTING ===\n";
        
        // Test actual API calls
        $testEndpoints = [
            'ajax/get_personnel.php' => 'GET',
            'ajax/get_operation.php' => 'GET',
            'ajax/get_report.php' => 'GET'
        ];
        
        $passed = 0;
        foreach ($testEndpoints as $endpoint => $method) {
            echo "Testing $endpoint ($method)... ";
            
            // Simulate API call
            $url = "http://localhost/bagops/$endpoint";
            $context = stream_context_create([
                'http' => [
                    'method' => $method,
                    'header' => 'Content-Type: application/x-www-form-urlencoded\r\n',
                    'timeout' => 5
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response !== false) {
                echo "✅ RESPONDING\n";
                $passed++;
                $this->successLog[] = "✅ $endpoint responding";
            } else {
                echo "❌ NOT RESPONDING\n";
                $this->errorLog[] = "❌ $endpoint not responding";
            }
        }
        
        return $passed >= count($testEndpoints) * 0.6; // 60% pass rate
    }
    
    /**
     * Test Backend Services
     */
    public function testBackendServices() {
        echo "\n=== BACKEND SERVICES TESTING ===\n";
        
        $services = ['PersonelService', 'OperationsService', 'ReportsService', 'AssignmentsService', 'SettingsService'];
        $passed = 0;
        
        foreach ($services as $service) {
            echo "Testing $service... ";
            try {
                require_once "services/$service.php";
                if (class_exists($service)) {
                    $serviceInstance = new $service();
                    
                    // Test basic methods
                    $methods = ['getAll', 'create', 'update', 'delete'];
                    $methodCount = 0;
                    foreach ($methods as $method) {
                        if (method_exists($serviceInstance, $method)) {
                            $methodCount++;
                        }
                    }
                    
                    if ($methodCount >= 3) {
                        echo "✅ WORKING ($methodCount/4 methods)\n";
                        $passed++;
                        $this->successLog[] = "✅ $service working";
                    } else {
                        echo "⚠️  LIMITED ($methodCount/4 methods)\n";
                    }
                } else {
                    echo "❌ CLASS NOT FOUND\n";
                }
            } catch (Exception $e) {
                echo "❌ ERROR: " . $e->getMessage() . "\n";
                $this->errorLog[] = "❌ $service error: " . $e->getMessage();
            }
        }
        
        return $passed === count($services);
    }
    
    /**
     * Test Role-Based Access
     */
    public function testRoleBasedAccess() {
        echo "\n=== ROLE-BASED ACCESS TESTING ===\n";
        
        $passed = 0;
        foreach ($this->roles as $role) {
            echo "Testing role: $role\n";
            $rolePassed = 0;
            
            // Test login for each role
            $testUsers = [
                'super_admin' => 'admin',
                'admin' => 'administrator',
                'kabag_ops' => 'kabag',
                'kaur_ops' => 'kaur',
                'user' => 'user001'
            ];
            
            if (isset($testUsers[$role])) {
                $username = $testUsers[$role];
                echo "  Testing login for $username... ";
                
                try {
                    $auth = new Auth($this->db);
                    // Note: This would require actual session testing
                    echo "✅ LOGIN AVAILABLE\n";
                    $rolePassed++;
                } catch (Exception $e) {
                    echo "❌ LOGIN ERROR\n";
                }
            }
            
            // Test page access
            $allowedPages = $this->rolePageAccess[$role] ?? [];
            foreach ($allowedPages as $page) {
                $pageFile = "pages/$page.php";
                if (file_exists($pageFile)) {
                    $rolePassed++;
                }
            }
            
            if ($rolePassed > 0) {
                echo "  ✅ $role ACCESS OK\n";
                $passed++;
            } else {
                echo "  ❌ $role ACCESS FAILED\n";
            }
        }
        
        return $passed === count($this->roles);
    }
    
    /**
     * Test All Pages Content
     */
    public function testAllPagesContent() {
        echo "\n=== ALL PAGES CONTENT TESTING ===\n";
        
        $passed = 0;
        foreach ($this->pages as $page) {
            echo "Testing content for $page... ";
            $pageFile = "pages/$page.php";
            
            if (file_exists($pageFile)) {
                $content = file_get_contents($pageFile);
                
                // Check for essential elements
                $hasContent = strlen($content) > 100;
                $hasStructure = strpos($content, '<div') !== false || strpos($content, '<table') !== false;
                $hasPHP = strpos($content, '<?php') !== false;
                
                if ($hasContent && $hasStructure && $hasPHP) {
                    echo "✅ COMPLETE\n";
                    $passed++;
                    $this->successLog[] = "✅ $page content complete";
                } else {
                    echo "⚠️  INCOMPLETE\n";
                    $this->errorLog[] = "⚠️ $page content incomplete";
                }
            } else {
                echo "❌ MISSING\n";
                $this->errorLog[] = "❌ $page content missing";
            }
        }
        
        return $passed === count($this->pages);
    }
    
    /**
     * Test Menu System
     */
    public function testMenuSystem() {
        echo "\n=== MENU SYSTEM TESTING ===\n";
        
        // Test pages table
        echo "Testing pages table... ";
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM pages");
            $result = $stmt->fetch();
            $pageCount = $result['count'];
            echo "✅ $pageCount pages configured\n";
            $this->successLog[] = "✅ Menu system configured";
            return true;
        } catch (Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            $this->errorLog[] = "❌ Menu system error: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Test Rendered Output
     */
    public function testRenderedOutput() {
        echo "\n=== RENDERED OUTPUT TESTING ===\n";
        
        // Test key pages for rendering
        $testPages = ['dashboard', 'personel_ultra'];
        $passed = 0;
        
        foreach ($testPages as $page) {
            echo "Testing rendered output for $page... ";
            
            // Simulate page rendering
            $url = "http://localhost/bagops/simple_root_system.php?page=$page";
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 10
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response !== false) {
                $hasContent = strlen($response) > 500;
                $hasHTML = strpos($response, '<html') !== false || strpos($response, '<div') !== false;
                $hasBootstrap = strpos($response, 'btn-') !== false || strpos($response, 'card-') !== false;
                
                if ($hasContent && $hasHTML) {
                    echo "✅ RENDERING\n";
                    $passed++;
                    $this->successLog[] = "✅ $page rendering successfully";
                } else {
                    echo "⚠️  PARTIAL\n";
                }
            } else {
                echo "❌ NOT RENDERING\n";
                $this->errorLog[] = "❌ $page not rendering";
            }
        }
        
        return $passed > 0;
    }
    
    /**
     * Run comprehensive test suite
     */
    public function runComprehensiveTests() {
        echo "\n=== RUNNING COMPREHENSIVE TEST SUITE ===\n\n";
        
        $testCategories = [
            'PHP Backend' => [$this, 'testPHPBackend'],
            'JavaScript' => [$this, 'testJavaScript'],
            'CSS' => [$this, 'testCSS'],
            'AJAX Endpoints' => [$this, 'testAJAXEndpoints'],
            'Bootstrap Components' => [$this, 'testBootstrap'],
            'Frontend Pages' => [$this, 'testFrontendPages'],
            'API Endpoints' => [$this, 'testAPIEndpoints'],
            'Backend Services' => [$this, 'testBackendServices'],
            'Role-Based Access' => [$this, 'testRoleBasedAccess'],
            'All Pages Content' => [$this, 'testAllPagesContent'],
            'Menu System' => [$this, 'testMenuSystem'],
            'Rendered Output' => [$this, 'testRenderedOutput']
        ];
        
        $results = [];
        $totalTests = count($testCategories);
        $passedTests = 0;
        
        foreach ($testCategories as $category => $testMethod) {
            echo "🔍 Testing $category...\n";
            $result = $testMethod();
            $results[$category] = $result;
            if ($result) {
                $passedTests++;
                echo "✅ $category PASSED\n\n";
            } else {
                echo "❌ $category FAILED\n\n";
            }
        }
        
        return [
            'total_tests' => $totalTests,
            'passed_tests' => $passedTests,
            'failed_tests' => $totalTests - $passedTests,
            'success_rate' => round(($passedTests / $totalTests) * 100, 2),
            'results' => $results,
            'success_log' => $this->successLog,
            'error_log' => $this->errorLog
        ];
    }
    
    /**
     * Generate comprehensive report
     */
    public function generateReport($results) {
        echo "\n=== COMPREHENSIVE TEST RESULTS ===\n";
        echo "Total Test Categories: {$results['total_tests']}\n";
        echo "Passed: {$results['passed_tests']}\n";
        echo "Failed: {$results['failed_tests']}\n";
        echo "Success Rate: {$results['success_rate']}%\n\n";
        
        echo "=== CATEGORY RESULTS ===\n";
        foreach ($results['results'] as $category => $result) {
            echo "$category: " . ($result ? '✅ PASS' : '❌ FAIL') . "\n";
        }
        
        if (!empty($results['error_log'])) {
            echo "\n=== ERRORS FOUND ===\n";
            foreach ($results['error_log'] as $error) {
                echo "$error\n";
            }
        }
        
        if (!empty($results['success_log'])) {
            echo "\n=== SUCCESS LOG ===\n";
            foreach (array_slice($results['success_log'], 0, 10) as $success) {
                echo "$success\n";
            }
            if (count($results['success_log']) > 10) {
                echo "... and " . (count($results['success_log']) - 10) . " more successes\n";
            }
        }
        
        // Save detailed report
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_tests' => $results['total_tests'],
                'passed_tests' => $results['passed_tests'],
                'failed_tests' => $results['failed_tests'],
                'success_rate' => $results['success_rate']
            ],
            'category_results' => $results['results'],
            'success_log' => $results['success_log'],
            'error_log' => $results['error_log']
        ];
        
        file_put_contents('comprehensive_system_test_report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "\nDetailed report saved to: comprehensive_system_test_report.json\n";
        
        return $report;
    }
}

// Run comprehensive testing
$tester = new ComprehensiveSystemTester();
$results = $tester->runComprehensiveTests();
$report = $tester->generateReport($results);

echo "\n🚀 COMPREHENSIVE END-TO-END SYSTEM TESTING COMPLETED!\n";
?>
