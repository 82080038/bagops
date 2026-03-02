<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>PHP Error Reporting Test</h1>";
echo "<h2>Current Error Reporting Settings:</h2>";

echo "<table border='1'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>error_reporting()</td><td>" . error_reporting() . "</td></tr>";
echo "<tr><td>display_errors</td><td>" . ini_get('display_errors') . "</td></tr>";
echo "<tr><td>display_startup_errors</td><td>" . ini_get('display_startup_errors') . "</td></tr>";
echo "<tr><td>log_errors</td><td>" . ini_get('log_errors') . "</td></tr>";
echo "</table>";

echo "<h2>Testing Error Types:</h2>";

// This should trigger a Notice
echo "<h3>Notice Test:</h3>";
$undefined_var = $undefined_var;

// This should trigger a Warning
echo "<h3>Warning Test:</h3>";
$file_handle = fopen('nonexistent_file.txt', 'r');

// This should trigger a Deprecated warning (if PHP version supports it)
echo "<h3>Deprecated Test:</h3>";
$deprecated_function = split(':', 'test:string');

echo "<h2>Complete!</h2>";
echo "<p>If you see error messages above, error reporting is working.</p>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>