<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');
error_reporting(E_ALL);

date_default_timezone_set("Asia/Kolkata"); // ✅ Set timezone to IST
error_log("=== STARTING telemetry_db.php ===");

echo "Step 1: Script started.<br>";
error_log("Step 1: Script started.");
require_once("/var/www/html/librespeed/results/telemetry_settings.php");
error_log("Loaded telemetry_settings.php");
echo "Step 1: Loaded telemetry_settings.php<br>";

require_once 'idObfuscation.php';

define('TELEMETRY_SETTINGS_FILE', 'telemetry_settings.php');

function getPdo($returnErrorMessage = false)
{
    if (!file_exists(TELEMETRY_SETTINGS_FILE) || !is_readable(TELEMETRY_SETTINGS_FILE)) {
        if ($returnErrorMessage) {
            return 'missing TELEMETRY_SETTINGS_FILE';
        }
        return false;
    }

    require TELEMETRY_SETTINGS_FILE;

    if (!isset($db_type)) {
        if ($returnErrorMessage) {
            return "db_type not set in '" . TELEMETRY_SETTINGS_FILE . "'";
        }
        return false;
    }

    $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    try {
        if ('mysql' === $db_type) {
            if (!isset($MySql_hostname, $MySql_port, $MySql_databasename, $MySql_username, $MySql_password)) {
                if ($returnErrorMessage) {
                    return "Required mysql database settings missing in '" . TELEMETRY_SETTINGS_FILE . "'";
                }
                return false;
            }

            $dsn = 'mysql:host=' . $MySql_hostname . ';port=' . $MySql_port . ';dbname=' . $MySql_databasename;
            return new PDO($dsn, $MySql_username, $MySql_password, $pdoOptions);
        }
    } catch (Exception $e) {
        if ($returnErrorMessage) {
            return $e->getMessage();
        }
        return false;
    }

    if ($returnErrorMessage) {
        return "db_type '" . $db_type . "' not supported";
    }
    return false;
}

function isObfuscationEnabled()
{
    require TELEMETRY_SETTINGS_FILE;
    return isset($enable_id_obfuscation) && true === $enable_id_obfuscation;
}

/**
 * Insert speed test results into the database, now including packet loss
 */
function insertSpeedtestUser($ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $log, $packet_loss, $returnExceptionOnError = false)
{
    $pdo = getPdo();
    if (!($pdo instanceof PDO)) {
        if ($returnExceptionOnError) {
            return new Exception("Failed to get database connection object");
        }
        return false;
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO speedtest_users
            (ip, ispinfo, extra, ua, lang, dl, ul, ping, jitter, packet_loss, log)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $packet_loss, $log
        ]);
        $id = $pdo->lastInsertId();
    } catch (Exception $e) {
        if ($returnExceptionOnError) {
            return $e;
        }
        return false;
    }

    if (isObfuscationEnabled()) {
        return obfuscateId($id);
    }

    return $id;
}

function getSpeedtestUserById($id, $returnExceptionOnError = false)
{
    $pdo = getPdo();
    if (!($pdo instanceof PDO)) {
        if ($returnExceptionOnError) {
            return new Exception("Failed to get database connection object");
        }
        return false;
    }

    if (isObfuscationEnabled()) {
        $id = deobfuscateId($id);
    }

    try {
        $stmt = $pdo->prepare(
    //        'SELECT id, timestamp, ip, ispinfo, ua, lang, dl, ul, ping, jitter, packet_loss, log, extra
      //      FROM speedtest_users
        //    WHERE id = :id'
	
	
	'SELECT id, 
        CONVERT_TZ(timestamp, "+00:00", "+05:30") AS timestamp, 
        ip, ispinfo, ua, lang, dl, ul, ping, jitter, packet_loss, log, extra
    FROM speedtest_users
    WHERE id = :id'
	
	
	
	
	);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        if ($returnExceptionOnError) {
            return $e;
        }
        return false;
    }

    if (!is_array($row)) {
        return null;
    }

    $row['id_formatted'] = $row['id'];
    if (isObfuscationEnabled()) {
        $row['id_formatted'] = obfuscateId($row['id']) . ' (deobfuscated: ' . $row['id'] . ')';
    }

    return $row;
}

function getLatestSpeedtestUsers()
{
    $pdo = getPdo();
    if (!($pdo instanceof PDO)) {
        return false;
    }

    require TELEMETRY_SETTINGS_FILE;

    try {
        $sql = 'SELECT ';
        if ('mssql' === $db_type) {
            $sql .= ' TOP(100) ';
        }

        $sql .= ' id, timestamp, ip, ispinfo, ua, lang, dl, ul, ping, jitter, packet_loss, log, extra
            FROM speedtest_users
            ORDER BY timestamp DESC ';

        if ('mssql' !== $db_type) {
            $sql .= ' LIMIT 100 ';
        }

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $i => $row) {
            $rows[$i]['id_formatted'] = $row['id'];
            if (isObfuscationEnabled()) {
                $rows[$i]['id_formatted'] = obfuscateId($row['id']) . ' (deobfuscated: ' . $row['id'] . ')';
            }
        }
    } catch (Exception $e) {
        return false;
    }

    return $rows;
}

?>

