<?php
/**
 * Comprehensive Testing Framework
 * BAGOPS POLRES SAMOSIR - Unit and Integration Testing
 */

// Include required classes
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../services/PersonelService.php';
require_once '../services/OperationsService.php';
require_once '../services/ReportsService.php';
require_once '../services/AssignmentsService.php';
require_once '../services/SettingsService.php';

class TestFramework {
    private $testResults = [];
    private $totalTests = 0;
    private $passedTests = 0;
    private $failedTests = 0;
    
    public function __construct() {
        echo "=== BAGOPS COMPREHENSIVE TESTING FRAMEWORK ===\n";
        echo "Starting comprehensive testing...\n\n";
    }
    
    /**
     * Run a single test
     */
    public function test($testName, $testFunction) {
        $this->totalTests++;
        
        try {
            $startTime = microtime(true);
            $result = $testFunction();
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            if ($result) {
                $this->passedTests++;
                $status = "✅ PASS";
                echo "[$status] $testName ({$duration}ms)\n";
                $this->testResults[] = [
                    'name' => $testName,
                    'status' => 'PASS',
                    'duration' => $duration,
                    'message' => 'Test passed successfully'
                ];
            } else {
                $this->failedTests++;
                $status = "❌ FAIL";
                echo "[$status] $testName ({$duration}ms) - Test returned false\n";
                $this->testResults[] = [
                    'name' => $testName,
                    'status' => 'FAIL',
                    'duration' => $duration,
                    'message' => 'Test returned false'
                ];
            }
        } catch (Exception $e) {
            $this->failedTests++;
            $status = "❌ ERROR";
            echo "[$status] $testName - {$e->getMessage()}\n";
            $this->testResults[] = [
                'name' => $testName,
                'status' => 'ERROR',
                'duration' => 0,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Assert equals
     */
    public function assertEquals($expected, $actual, $message = '') {
        if ($expected !== $actual) {
            throw new Exception($message ?: "Expected " . var_export($expected, true) . " but got " . var_export($actual, true));
        }
        return true;
    }
    
    /**
     * Assert not empty
     */
    public function assertNotEmpty($value, $message = '') {
        if (empty($value)) {
            throw new Exception($message ?: "Value should not be empty");
        }
        return true;
    }
    
    /**
     * Assert true
     */
    public function assertTrue($value, $message = '') {
        if (!$value) {
            throw new Exception($message ?: "Value should be true");
        }
        return true;
    }
    
    /**
     * Assert false
     */
    public function assertFalse($value, $message = '') {
        if ($value) {
            throw new Exception($message ?: "Value should be false");
        }
        return true;
    }
    
    /**
     * Generate test report
     */
    public function generateReport() {
        $successRate = $this->totalTests > 0 ? round(($this->passedTests / $this->totalTests) * 100, 2) : 0;
        
        echo "\n=== TEST RESULTS ===\n";
        echo "Total Tests: {$this->totalTests}\n";
        echo "Passed: {$this->passedTests}\n";
        echo "Failed: {$this->failedTests}\n";
        echo "Success Rate: {$successRate}%\n";
        
        if ($this->failedTests > 0) {
            echo "\n=== FAILED TESTS ===\n";
            foreach ($this->testResults as $result) {
                if ($result['status'] !== 'PASS') {
                    echo "❌ {$result['name']}: {$result['message']}\n";
                }
            }
        }
        
        // Save detailed report
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'summary' => [
                'total' => $this->totalTests,
                'passed' => $this->passedTests,
                'failed' => $this->failedTests,
                'success_rate' => $successRate
            ],
            'results' => $this->testResults
        ];
        
        file_put_contents('test_report.json', json_encode($report, JSON_PRETTY_PRINT));
        echo "\nDetailed report saved to: test_report.json\n";
        
        return $report;
    }
}

// Unit Tests for Service Classes
class ServiceTests {
    private $framework;
    
    public function __construct($framework) {
        $this->framework = $framework;
    }
    
    /**
     * Test PersonelService
     */
    public function testPersonelService() {
        echo "\n--- PersonelService Tests ---\n";
        
        $this->framework->test("PersonelService - Constructor", function() {
            $service = new PersonelService();
            return $this->framework->assertNotEmpty($service, "PersonelService should be instantiated");
        });
        
        $this->framework->test("PersonelService - Get All Personel", function() {
            $service = new PersonelService();
            $result = $service->getAllPersonel();
            return $this->framework->assertTrue(is_array($result), "Should return array");
        });
        
        $this->framework->test("PersonelService - Get Personel Stats", function() {
            $service = new PersonelService();
            $stats = $service->getPersonelStats();
            return $this->framework->assertTrue(isset($stats['total']), "Should have total count");
        });
        
        $this->framework->test("PersonelService - Validate NRP Format", function() {
            $service = new PersonelService();
            try {
                $service->createPersonel(['nrp' => 'invalid', 'nama' => 'Test', 'pangkat' => 'Test']);
                return false; // Should throw exception
            } catch (Exception $e) {
                return $this->framework->assertTrue(strpos($e->getMessage(), 'Invalid NRP format') !== false, "Should validate NRP format");
            }
        });
    }
    
    /**
     * Test OperationsService
     */
    public function testOperationsService() {
        echo "\n--- OperationsService Tests ---\n";
        
        $this->framework->test("OperationsService - Constructor", function() {
            $service = new OperationsService();
            return $this->framework->assertNotEmpty($service, "OperationsService should be instantiated");
        });
        
        $this->framework->test("OperationsService - Get All Operations", function() {
            $service = new OperationsService();
            $result = $service->getAllOperations();
            return $this->framework->assertTrue(is_array($result), "Should return array");
        });
        
        $this->framework->test("OperationsService - Get Operation Stats", function() {
            $service = new OperationsService();
            $stats = $service->getOperationStats();
            return $this->framework->assertTrue(isset($stats['total']), "Should have total count");
        });
        
        $this->framework->test("OperationsService - Validate Operation Type", function() {
            $service = new OperationsService();
            try {
                $service->createOperation(['nama_operasi' => 'Test', 'jenis_operasi' => 'invalid', 'tanggal_mulai' => '2024-01-01', 'lokasi' => 'Test']);
                return false; // Should throw exception
            } catch (Exception $e) {
                return $this->framework->assertTrue(strpos($e->getMessage(), 'Invalid operation type') !== false, "Should validate operation type");
            }
        });
    }
    
    /**
     * Test ReportsService
     */
    public function testReportsService() {
        echo "\n--- ReportsService Tests ---\n";
        
        $this->framework->test("ReportsService - Constructor", function() {
            $service = new ReportsService();
            return $this->framework->assertNotEmpty($service, "ReportsService should be instantiated");
        });
        
        $this->framework->test("ReportsService - Get All Reports", function() {
            $service = new ReportsService();
            $result = $service->getAllReports();
            return $this->framework->assertTrue(is_array($result), "Should return array");
        });
        
        $this->framework->test("ReportsService - Get Report Stats", function() {
            $service = new ReportsService();
            $stats = $service->getReportStats();
            return $this->framework->assertTrue(isset($stats['total']), "Should have total count");
        });
        
        $this->framework->test("ReportsService - Validate Report Type", function() {
            $service = new ReportsService();
            try {
                $service->createReport(['jenis_laporan' => 'invalid', 'isi_laporan' => 'Test content with enough characters to pass validation']);
                return false; // Should throw exception
            } catch (Exception $e) {
                return $this->framework->assertTrue(strpos($e->getMessage(), 'Invalid report type') !== false, "Should validate report type");
            }
        });
    }
    
    /**
     * Test AssignmentsService
     */
    public function testAssignmentsService() {
        echo "\n--- AssignmentsService Tests ---\n";
        
        $this->framework->test("AssignmentsService - Constructor", function() {
            $service = new AssignmentsService();
            return $this->framework->assertNotEmpty($service, "AssignmentsService should be instantiated");
        });
        
        $this->framework->test("AssignmentsService - Get All Assignments", function() {
            $service = new AssignmentsService();
            $result = $service->getAllAssignments();
            return $this->framework->assertTrue(is_array($result), "Should return array");
        });
        
        $this->framework->test("AssignmentsService - Get Assignment Stats", function() {
            $service = new AssignmentsService();
            $stats = $service->getAssignmentStats();
            return $this->framework->assertTrue(isset($stats['total']), "Should have total count");
        });
        
        $this->framework->test("AssignmentsService - Validate Assignment Data", function() {
            $service = new AssignmentsService();
            try {
                $service->createAssignment(['personel_id' => '', 'operation_id' => '', 'role_assignment' => '']);
                return false; // Should throw exception
            } catch (Exception $e) {
                return $this->framework->assertTrue(strpos($e->getMessage(), 'required') !== false, "Should validate required fields");
            }
        });
    }
    
    /**
     * Test SettingsService
     */
    public function testSettingsService() {
        echo "\n--- SettingsService Tests ---\n";
        
        $this->framework->test("SettingsService - Constructor", function() {
            $service = new SettingsService();
            return $this->framework->assertNotEmpty($service, "SettingsService should be instantiated");
        });
        
        $this->framework->test("SettingsService - Get All Settings", function() {
            $service = new SettingsService();
            $settings = $service->getAllSettings();
            return $this->framework->assertTrue(is_array($settings), "Should return array");
        });
        
        $this->framework->test("SettingsService - Get Setting", function() {
            $service = new SettingsService();
            $app_name = $service->getSetting('app_name');
            return $this->framework->assertNotEmpty($app_name, "Should return app name");
        });
        
        $this->framework->test("SettingsService - Update Setting", function() {
            $service = new SettingsService();
            $result = $service->updateSetting('test_setting', 'test_value', 'string');
            return $this->framework->assertTrue($result, "Should update setting successfully");
        });
        
        $this->framework->test("SettingsService - Validate Session Timeout", function() {
            $service = new SettingsService();
            try {
                $service->updateSetting('session_timeout', 500, 'integer'); // Too high
                return false; // Should throw exception
            } catch (Exception $e) {
                return $this->framework->assertTrue(strpos($e->getMessage(), 'between 5 and 480') !== false, "Should validate session timeout range");
            }
        });
    }
}

// Integration Tests
class IntegrationTests {
    private $framework;
    
    public function __construct($framework) {
        $this->framework = $framework;
    }
    
    /**
     * Test Database Integration
     */
    public function testDatabaseIntegration() {
        echo "\n--- Database Integration Tests ---\n";
        
        $this->framework->test("Database - Connection", function() {
            $db = (new Database())->getConnection();
            return $this->framework->assertNotEmpty($db, "Database connection should be established");
        });
        
        $this->framework->test("Database - Query Execution", function() {
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
            $stmt->execute();
            $result = $stmt->fetch();
            return $this->framework->assertTrue(isset($result['count']), "Should execute query successfully");
        });
        
        $this->framework->test("Database - Transaction", function() {
            $db = (new Database())->getConnection();
            $db->beginTransaction();
            $db->rollBack();
            return $this->framework->assertTrue(true, "Transaction should work");
        });
    }
    
    /**
     * Test Authentication Integration
     */
    public function testAuthenticationIntegration() {
        echo "\n--- Authentication Integration Tests ---\n";
        
        $this->framework->test("Auth - Constructor", function() {
            $auth = new Auth();
            return $this->framework->assertNotEmpty($auth, "Auth should be instantiated");
        });
        
        $this->framework->test("Auth - Session Check", function() {
            $auth = new Auth();
            $isLoggedIn = $auth->isLoggedIn();
            return $this->framework->assertTrue(is_bool($isLoggedIn), "Should return boolean");
        });
    }
    
    /**
     * Test API Endpoints Integration
     */
    public function testAPIEndpointsIntegration() {
        echo "\n--- API Endpoints Integration Tests ---\n";
        
        $this->framework->test("API - Get Personnel Endpoint", function() {
            $url = 'http://localhost/bagops/ajax/get_personnel.php';
            $response = @file_get_contents($url);
            return $this->framework->assertTrue($response !== false, "Should get response from API");
        });
        
        $this->framework->test("API - Get Operations Endpoint", function() {
            $url = 'http://localhost/bagops/ajax/get_operation.php';
            $response = @file_get_contents($url);
            return $this->framework->assertTrue($response !== false, "Should get response from API");
        });
        
        $this->framework->test("API - Get Reports Endpoint", function() {
            $url = 'http://localhost/bagops/ajax/get_report.php';
            $response = @file_get_contents($url);
            return $this->framework->assertTrue($response !== false, "Should get response from API");
        });
    }
    
    /**
     * Test Frontend Integration
     */
    public function testFrontendIntegration() {
        echo "\n--- Frontend Integration Tests ---\n";
        
        $this->framework->test("Frontend - Dashboard Page", function() {
            $url = 'http://localhost/bagops/simple_root_system.php?page=dashboard';
            $response = @file_get_contents($url);
            return $this->framework->assertTrue($response !== false, "Should load dashboard page");
        });
        
        $this->framework->test("Frontend - Personel Page", function() {
            $url = 'http://localhost/bagops/simple_root_system.php?page=personel_ultra';
            $response = @file_get_contents($url);
            return $this->framework->assertTrue($response !== false, "Should load personel page");
        });
        
        $this->framework->test("Frontend - Operations Page", function() {
            $url = 'http://localhost/bagops/simple_root_system.php?page=operations';
            $response = @file_get_contents($url);
            return $this->framework->assertTrue($response !== false, "Should load operations page");
        });
    }
}

// Main Test Runner
function runComprehensiveTests() {
    $framework = new TestFramework();
    
    // Unit Tests
    $serviceTests = new ServiceTests($framework);
    $serviceTests->testPersonelService();
    $serviceTests->testOperationsService();
    $serviceTests->testReportsService();
    $serviceTests->testAssignmentsService();
    $serviceTests->testSettingsService();
    
    // Integration Tests
    $integrationTests = new IntegrationTests($framework);
    $integrationTests->testDatabaseIntegration();
    $integrationTests->testAuthenticationIntegration();
    $integrationTests->testAPIEndpointsIntegration();
    $integrationTests->testFrontendIntegration();
    
    // Generate Report
    return $framework->generateReport();
}

// Run tests if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runComprehensiveTests();
}
?>
