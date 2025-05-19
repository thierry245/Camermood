<?php
// logging_functions.php
require_once __DIR__.'/../config/db_config.php';

define('LOG_DIR', '/home/nkeussomyossah25/logs/');

function logToFile($filename, $message) {
    // Assurez-vous que le dossier de logs existe
    if (!file_exists(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }

    $logFile = LOG_DIR . $filename;
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
    $user = $_SESSION['user']['email'] ?? 'Anonyme';

    $logMessage = "[$timestamp] [IP: $ip] [User: $user] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}
?>