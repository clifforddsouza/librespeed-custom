<?php

// Type of db: "mssql", "mysql", "sqlite" or "postgresql"
$db_type = 'mysql';

// Password to login to stats.php. Change this!!!
$stats_password = 'librespeed';

// If set to true, test IDs will be obfuscated to prevent users from guessing URLs of other tests
$enable_id_obfuscation = false;

// If set to true, IP addresses will be redacted from IP and ISP info fields, as well as the log
$redact_ip_addresses = false;

// Sqlite3 settings
$Sqlite_db_file = '../../speedtest_telemetry.sql';

// MySQL Database Credentials
$MySql_username = 'librespeed';
$MySql_password = 'libresp33d';
$MySql_hostname = 'localhost';
$MySql_databasename = 'librespeed';
$MySql_port = 3306; // Ensure it's an integer

// Assign correct MySQL credentials to expected variables
$database_host = $MySql_hostname;
$database_user = $MySql_username;
$database_password = $MySql_password;
$database_name = $MySql_databasename;
$database_port = $MySql_port;

error_log("Step 2: Before database connection.");
//echo "Step 2: Before database connection.<br>";

// Connect to MySQL
$conn = new mysqli($database_host, $database_user, $database_password, $database_name, $database_port);

if ($conn->connect_error) {
   error_log("Database Connection Failed: " . $conn->connect_error);
   die("Database Connection Failed: " . $conn->connect_error);
}

error_log("Step 3: Database connected successfully.");
//echo "Step 3: Database connected successfully.<br>";

// Debugging Output
error_log("Checking MySQL variables:");
error_log("database_host: " . ($database_host ?? 'NOT SET'));
error_log("database_user: " . ($database_user ?? 'NOT SET'));
error_log("database_password: " . ($database_password ?? 'NOT SET'));
error_log("database_name: " . ($database_name ?? 'NOT SET'));

?>

