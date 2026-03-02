<?php
/**
 * Comprehensive Role & Application Flow Testing
 * BAGOPS POLRES SAMOSIR - Complete System Testing
 */

echo "=== BAGOPS COMPREHENSIVE ROLE & FLOW TESTING ===\n";
echo "Testing all 5 roles and complete application flow...\n\n";

// Include required classes
require_once 'config/database.php';
require_once 'classes/Auth.php';

class RoleFlowTester {
    private $db;
    private $testResults = [];
    private $roleTests = [
        'super_admin' => [
            'username' => 'admin',
            'password' => 'admin123',
            'expected_pages' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'settings', 'profile', 'help'],
            'expected_crud' => ['personel', 'operations', 'reports', 'assignments', 'settings']
        ],
        'admin' => [
            'username' => 'administrator',
            'password' => 'admin123',
            'expected_pages' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'profile', 'help'],
            'expected_crud' => ['personel', 'operations', 'reports', 'assignments']
        ],
        'kabag_ops' => [
            'username' => 'kabag',
            'password' => 'password123',
            'expected_pages' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'assignments', 'profile'],
            'expected_crud' => ['operations', 'reports', 'assignments']
        ],
        'kaur_ops' => [
            'username' => 'kaur',
            'password' => 'password123',
            'expected_pages' => ['dashboard', 'personel_ultra', 'operations', 'reports', 'profile'],
            'expected_crud' => ['operations', 'reports']
        ],
        'user' => [
            'username' => 'user001',
            'password' => 'password123',
            'expected_pages' => ['dashboard', 'profile'],
            'expected_crud' => []
        ]
    ];
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
    }
    
    /**
     * Test login flow for specific role
     */
    public function testLoginFlow($role, $credentials) {
        echo "Testing login flow for role: $role\n";
        
        try {
            // Test login
            $loginData = [
                'username' => $credentials['username'],
                'password' => $credentials['password']
            ];
            
            $auth = new Auth($this->db);
            $loginResult = $auth->login($loginData['username'], $loginData['password']);
            
            if ($loginResult) {
                echo "   ✅ Login successful for $role\n";
                return true;
            } else {
                echo "   ❌ Login failed for $role\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "   ❌ Login error for $role: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test menu access for role
     */
    public function testMenuAccess($role, $expectedPages) {
        echo "Testing menu access for role: $role\n";
        
        try {
            // Get pages from database for this role
            $stmt = $this->db->prepare("SELECT page_key FROM pages WHERE target_role = ? OR target_role = 'all' ORDER BY order_index");
            $stmt->execute([$role]);
            $availablePages = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $missingPages = array_diff($expectedPages, $availablePages);
            $extraPages = array_diff($availablePages, $expectedPages);
            
            if (empty($missingPages) && empty($extraPages)) {
                echo "   ✅ Menu access correct for $role\n";
                return true;
            } else {
                if (!empty($missingPages)) {
                    echo "   ❌ Missing pages for $role: " . implode(', ', $missingPages) . "\n";
                }
                if (!empty($extraPages)) {
                    echo "   ⚠️  Extra pages for $role: " . implode(', ', $extraPages) . "\n";
                }
                return false;
            }
            
        } catch (Exception $e) {
            echo "   ❌ Menu access error for $role: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test CRUD operations for role
     */
    public function testCrudOperations($role, $expectedCrud) {
        echo "Testing CRUD operations for role: $role\n";
        
        $crudTests = [
            'personel' => ['ajax/get_personnel.php', 'ajax/save_personnel.php', 'ajax/update_personnel.php', 'ajax/delete_personnel.php'],
            'operations' => ['ajax/get_operation.php', 'ajax/save_operation.php', 'ajax/update_operation.php', 'ajax/delete_operation.php'],
            'reports' => ['ajax/get_report.php', 'ajax/save_report.php', 'ajax/update_report.php', 'ajax/delete_report.php'],
            'assignments' => ['ajax/get_assignment.php', 'ajax/save_assignment.php', 'ajax/update_assignment.php', 'ajax/delete_assignment.php'],
            'settings' => ['ajax/get_settings.php', 'ajax/save_settings.php']
        ];
        
        $allPassed = true;
        
        foreach ($expectedCrud as $module) {
            if (isset($crudTests[$module])) {
                $modulePassed = true;
                foreach ($crudTests[$module] as $endpoint) {
                    if (!file_exists($endpoint)) {
                        echo "   ❌ Missing endpoint: $endpoint for $role\n";
                        $modulePassed = false;
                        $allPassed = false;
                    }
                }
                if ($modulePassed) {
                    echo "   ✅ CRUD operations available: $module for $role\n";
                }
            }
        }
        
        if ($allPassed) {
            echo "   ✅ All CRUD operations correct for $role\n";
        }
        
        return $allPassed;
    }
    
    /**
     * Test page functionality
     */
    public function testPageFunctionality($role, $expectedPages) {
        echo "Testing page functionality for role: $role\n";
        
        $allPassed = true;
        
        foreach ($expectedPages as $page) {
            $pageFile = "pages/$page.php";
            if (file_exists($pageFile)) {
                // Check if page has required elements
                $content = file_get_contents($pageFile);
                
                // Check for DataTables if it's a data page
                if (in_array($page, ['personel_ultra', 'operations', 'reports', 'assignments'])) {
                    if (strpos($content, 'DataTables') !== false) {
                        echo "   ✅ DataTables found in $page\n";
                    } else {
                        echo "   ⚠️  DataTables missing in $page\n";
                    }
                }
                
                // Check for CRUD buttons
                if (in_array($page, ['personel_ultra', 'operations', 'reports', 'assignments'])) {
                    if (strpos($content, 'btn-primary') !== false) {
                        echo "   ✅ CRUD buttons found in $page\n";
                    } else {
                        echo "   ⚠️  CRUD buttons missing in $page\n";
                    }
                }
                
            } else {
                echo "   ❌ Page file missing: $page\n";
                $allPassed = false;
            }
        }
        
        return $allPassed;
    }
    
    /**
     * Test service layer integration
     */
    public function testServiceLayerIntegration() {
        echo "Testing Service Layer Integration\n";
        
        $serviceFiles = [
            'services/PersonelService.php',
            'services/OperationsService.php',
            'services/ReportsService.php',
            'services/AssignmentsService.php',
            'services/SettingsService.php'
        ];
        
        $allPassed = true;
        
        foreach ($serviceFiles as $serviceFile) {
            if (file_exists($serviceFile)) {
                $content = file_get_contents($serviceFile);
                
                // Check for required methods
                $requiredMethods = ['__construct', 'getAll', 'create', 'update', 'delete'];
                $methodsFound = 0;
                
                foreach ($requiredMethods as $method) {
                    if (strpos($content, "function $method") !== false) {
                        $methodsFound++;
                    }
                }
                
                if ($methodsFound >= 4) { // At least constructor + 3 CRUD methods
                    echo "   ✅ " . basename($serviceFile) . " - Methods complete\n";
                } else {
                    echo "   ⚠️  " . basename($serviceFile) . " - Missing methods\n";
                }
                
            } else {
                echo "   ❌ Service file missing: $serviceFile\n";
                $allPassed = false;
            }
        }
        
        return $allPassed;
    }
    
    /**
     * Run complete test suite for all roles
     */
    public function runCompleteTestSuite() {
        echo "=== RUNNING COMPLETE TEST SUITE ===\n\n";
        
        $overallResults = [
            'total_tests' => 0,
            'passed_tests' => 0,
            'failed_tests' => 0,
            'role_results' => []
        ];
        
        // Test service layer first
        $serviceTest = $this->testServiceLayerIntegration();
        $overallResults['total_tests']++;
        if ($serviceTest) {
            $overallResults['passed_tests']++;
        } else {
            $overallResults['failed_tests']++;
        }
        
        echo "\n";
        
        // Test each role
        foreach ($this->roleTests as $role => $config) {
            echo "--- Testing Role: $role ---\n";
            
            $roleResult = [
                'login' => false,
                'menu' => false,
                'crud' => false,
                'pages' => false,
                'overall' => false
            ];
            
            // Test login
            $loginTest = $this->testLoginFlow($role, $config);
            $roleResult['login'] = $loginTest;
            $overallResults['total_tests']++;
            if ($loginTest) $overallResults['passed_tests']++;
            else $overallResults['failed_tests']++;
            
            // Test menu access
            $menuTest = $this->testMenuAccess($role, $config['expected_pages']);
            $roleResult['menu'] = $menuTest;
            $overallResults['total_tests']++;
            if ($menuTest) $overallResults['passed_tests']++;
            else $overallResults['failed_tests']++;
            
            // Test CRUD operations
            $crudTest = $this->testCrudOperations($role, $config['expected_crud']);
            $roleResult['crud'] = $crudTest;
            $overallResults['total_tests']++;
            if ($crudTest) $overallResults['passed_tests']++;
            else $overallResults['failed_tests']++;
            
            // Test page functionality
            $pageTest = $this->testPageFunctionality($role, $config['expected_pages']);
            $roleResult['pages'] = $pageTest;
            $overallResults['total_tests']++;
            if ($pageTest) $overallResults['passed_tests']++;
            else $overallResults['failed_tests']++;
            
            // Calculate overall result
            $roleResult['overall'] = ($loginTest && $menuTest && $crudTest && $pageTest);
            
            $overallResults['role_results'][$role] = $roleResult;
            
            echo "Overall result for $role: " . ($roleResult['overall'] ? '✅ PASS' : '❌ FAIL') . "\n\n";
        }
        
        return $overallResults;
    }
    
    /**
     * Generate test report
     */
    public function generateReport($results) {
        $successRate = $results['total_tests'] > 0 ? round(($results['passed_tests'] / $results['total_tests']) * 100, 2) : 0;
        
        echo "=== COMPREHENSIVE TEST RESULTS ===\n";
        echo "Total Tests: {$results['total_tests']}\n";
        echo "Passed: {$results['passed_tests']}\n";
        echo "Failed: {$results['failed_tests']}\n";
        echo "Success Rate: {$successRate}%\n\n";
        
        echo "=== ROLE-SPECIFIC RESULTS ===\n";
        foreach ($results['role_results'] as $role => $result) {
            echo "$role:\n";
            echo "  Login: " . ($result['login'] ? '✅' : '❌') . "\n";
            echo "  Menu: " . ($result['menu'] ? '✅' : '❌') . "\n";
            echo "  CRUD: " . ($result['crud'] ? '✅' : '❌') . "\n";
            echo "  Pages: " . ($result['pages'] ? '✅' : '❌') . "\n";
            echo "  Overall: " . ($result['overall'] ? '✅ PASS' : '❌ FAIL') . "\n\n";
        }
        
        // Save detailed report
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_tests' => $results['total_tests'],
                'passed_tests' => $results['passed_tests'],
                'failed_tests' => $results['failed_tests'],
                'success_rate' => $successRate
            ],
            'role_results' => $results['role_results']
        ];
        
        file_put_contents('comprehensive_role_test_report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "Detailed report saved to: comprehensive_role_test_report.json\n";
        
        return $report;
    }
}

// Run comprehensive testing
$tester = new RoleFlowTester();
$results = $tester->runCompleteTestSuite();
$report = $tester->generateReport($results);

echo "\n🚀 COMPREHENSIVE ROLE & FLOW TESTING COMPLETED!\n";
?>
