<?php

error_log("==== TELEMETRY.PHP EXECUTED ====");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/apache2/php_errors.log');


require 'telemetry_settings.php';
require_once 'telemetry_db.php';
require_once '../backend/getIP_util.php';

$ip = getClientIp();
$ispinfo = $_POST['ispinfo'] ?? 'Microscan Internet Limiter';
$extra = $_POST['extra'] ?? 'No extra info';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown User Agent';
$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown Language';

error_log("Received POST data: " . print_r($_POST, true));
error_log("==== START RECEIVED POST DATA ====");
error_log(print_r($_POST, true));
error_log("==== END RECEIVED POST DATA ====");

$dl = isset($_POST['dl']) ? floatval($_POST['dl']) : 0;
error_log("Raw POST data: " . print_r($_POST, true));
$ul = isset($_POST['ul']) ? floatval($_POST['ul']) : 0;
$ping = isset($_POST['ping']) ? floatval($_POST['ping']) : 0;
$jitter = isset($_POST['jitter']) ? floatval($_POST['jitter']) : 0;
$log = $_POST['log'] ?? 'No log data';
$packet_loss = isset($_POST['packet_loss']) ? $_POST['packet_loss'] : 0;


// Redact IP addresses if enabled
if (isset($redact_ip_addresses) && $redact_ip_addresses === true) {
    $ip = '0.0.0.0';
    $ipv4_regex = '/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/';
    $ipv6_regex = '/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/';
    $hostname_regex = '/"hostname":"([^\\\\"]|\\\\")*"/';
    
    $ispinfo = preg_replace($ipv4_regex, '0.0.0.0', $ispinfo);
    $ispinfo = preg_replace($ipv6_regex, '0.0.0.0', $ispinfo);
    $ispinfo = preg_replace($hostname_regex, '"hostname":"REDACTED"', $ispinfo);
    
    $log = preg_replace($ipv4_regex, '0.0.0.0', $log);
    $log = preg_replace($ipv6_regex, '0.0.0.0', $log);
    $log = preg_replace($hostname_regex, '"hostname":"REDACTED"', $log);
}

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Insert into the database
//$id = insertSpeedtestUser($ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $log);
error_log("📥 Parsed Packet Loss (final value to DB): $packet_loss");
$id = insertSpeedtestUser($ip, $ispinfo, $extra, $ua, $lang, $dl, $ul, $ping, $jitter, $log, $packet_loss);

if ($id === false) {
    exit(1);
}

echo 'id '.$id;

