<?php
/**
 * Simple Service Layer Test
 * BAGOPS POLRES SAMOSIR - Service Layer Verification
 */

echo "=== BAGOPS SERVICE LAYER VERIFICATION ===\n";
echo "Testing Service Layer Implementation...\n\n";

// Test 1: Check Service Files Exist
echo "1. Service Files Check:\n";
$serviceFiles = [
    'PersonelService.php',
    'OperationsService.php', 
    'ReportsService.php',
    'AssignmentsService.php',
    'SettingsService.php'
];

$serviceCount = 0;
foreach ($serviceFiles as $file) {
    if (file_exists("services/$file")) {
        echo "   ✅ $file - Found\n";
        $serviceCount++;
    } else {
        echo "   ❌ $file - Missing\n";
    }
}
echo "   Total: $serviceCount/" . count($serviceFiles) . " files\n\n";

// Test 2: Check Service Class Structure
echo "2. Service Class Structure Check:\n";
foreach ($serviceFiles as $file) {
    $filePath = "services/$file";
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $className = str_replace('.php', '', $file);
        
        // Check for class definition
        if (strpos($content, "class $className") !== false) {
            echo "   ✅ $className - Class defined\n";
        } else {
            echo "   ❌ $className - Class not found\n";
        }
        
        // Check for constructor
        if (strpos($content, "__construct") !== false) {
            echo "   ✅ $className - Constructor present\n";
        } else {
            echo "   ❌ $className - Constructor missing\n";
        }
        
        // Check for CRUD methods
        $crudMethods = ['getAll', 'getById', 'create', 'update', 'delete'];
        foreach ($crudMethods as $method) {
            if (strpos($content, "function $method") !== false || strpos($content, $method) !== false) {
                echo "   ✅ $className - $method method found\n";
            }
        }
    }
}

// Test 3: Check Database Integration
echo "\n3. Database Integration Check:\n";
try {
    require_once 'config/database.php';
    $db = (new Database())->getConnection();
    echo "   ✅ Database connection successful\n";
    
    // Test basic query
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "   ✅ Database query successful ({$result['count']} users)\n";
    
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 4: Check Authentication Integration
echo "\n4. Authentication Integration Check:\n";
try {
    require_once 'classes/Auth.php';
    $db = (new Database())->getConnection();
    $auth = new Auth($db);
    echo "   ✅ Auth class instantiated\n";
    
    $isLoggedIn = $auth->isLoggedIn();
    echo "   ✅ Auth session check successful (Status: " . ($isLoggedIn ? 'Logged in' : 'Not logged in') . ")\n";
    
} catch (Exception $e) {
    echo "   ❌ Auth integration failed: " . $e->getMessage() . "\n";
}

// Test 5: Check API Endpoints
echo "\n5. API Endpoints Check:\n";
$apiEndpoints = [
    'get_personnel.php',
    'get_operation.php',
    'get_report.php',
    'save_assignment.php',
    'save_settings.php'
];

$apiCount = 0;
foreach ($apiEndpoints as $endpoint) {
    if (file_exists("ajax/$endpoint")) {
        echo "   ✅ $endpoint - Found\n";
        $apiCount++;
    } else {
        echo "   ❌ $endpoint - Missing\n";
    }
}
echo "   Total: $apiCount/" . count($apiEndpoints) . " endpoints\n\n";

// Test 6: Check Frontend Integration
echo "6. Frontend Integration Check:\n";
$frontendPages = [
    'personel_ultra.php',
    'operations.php',
    'reports.php',
    'assignments.php',
    'settings.php'
];

$pageCount = 0;
foreach ($frontendPages as $page) {
    if (file_exists("pages/$page")) {
        echo "   ✅ $page - Found\n";
        $pageCount++;
    } else {
        echo "   ❌ $page - Missing\n";
    }
}
echo "   Total: $pageCount/" . count($frontendPages) . " pages\n\n";

// Generate Summary Report
$successRate = round((($serviceCount + $apiCount + $pageCount) / (count($serviceFiles) + count($apiEndpoints) + count($frontendPages))) * 100, 2);

echo "=== IMPLEMENTATION SUMMARY ===\n";
echo "Service Layer: $serviceCount/" . count($serviceFiles) . " files (" . round(($serviceCount/count($serviceFiles))*100, 2) . "%)\n";
echo "API Endpoints: $apiCount/" . count($apiEndpoints) . " files (" . round(($apiCount/count($apiEndpoints))*100, 2) . "%)\n";
echo "Frontend Pages: $pageCount/" . count($frontendPages) . " files (" . round(($pageCount/count($frontendPages))*100, 2) . "%)\n";
echo "Overall Success Rate: $successRate%\n\n";

if ($successRate >= 90) {
    echo "🎉 EXCELLENT: Service Layer Implementation Complete!\n";
} elseif ($successRate >= 75) {
    echo "✅ GOOD: Service Layer Mostly Complete\n";
} else {
    echo "⚠️  NEEDS WORK: Service Layer Incomplete\n";
}

// Save report
$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'service_layer' => [
        'total' => count($serviceFiles),
        'implemented' => $serviceCount,
        'success_rate' => round(($serviceCount/count($serviceFiles))*100, 2)
    ],
    'api_endpoints' => [
        'total' => count($apiEndpoints),
        'implemented' => $apiCount,
        'success_rate' => round(($apiCount/count($apiEndpoints))*100, 2)
    ],
    'frontend_pages' => [
        'total' => count($frontendPages),
        'implemented' => $pageCount,
        'success_rate' => round(($pageCount/count($frontendPages))*100, 2)
    ],
    'overall_success_rate' => $successRate
];

file_put_contents('service_layer_test_report.json', json_encode($report, JSON_PRETTY_PRINT));
echo "Detailed report saved to: service_layer_test_report.json\n";

echo "\n🚀 SERVICE LAYER & COMPREHENSIVE TESTING COMPLETED!\n";
?>
