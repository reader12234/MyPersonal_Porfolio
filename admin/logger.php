<?php
/**
 * Event Logging System
 * Logs all admin actions to a JSON-formatted log file
 */

function log_event($action, $details = []) {
    $logFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $user = isset($_SESSION['admin_full_name']) && !empty($_SESSION['admin_full_name']) 
        ? $_SESSION['admin_full_name'] 
        : (isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'guest');
    $origin = isset($_SESSION['admin_id']) ? 'admin_panel' : 'web';
    $payload = [
        'time' => $timestamp,
        'user' => $user,
        'origin' => $origin,
        'action' => $action,
        'details' => $details
    ];
    $line = json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL;

    // Explicit file handling using fopen/fwrite/fclose with lock
    $fh = @fopen($logFile, 'ab');
    if ($fh === false) { return; }
    if (@flock($fh, LOCK_EX)) {
        @fwrite($fh, $line);
        @flock($fh, LOCK_UN);
    } else {
        @fwrite($fh, $line);
    }
    @fclose($fh);
}
?>
