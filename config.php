<?php
// توکن ربات خود را اینجا قرار دهید
$botToken = "YOUR_BOT_TOKEN_HERE";
$apiUrl = "https://api.telegram.org/bot" . $botToken . "/";

// اطلاعات دیتابیس
$host = '127.0.0.1';
$db   = 'YOUR_DATABASE_NAME';
$user = 'YOUR_DATABASE_USER';
$pass = 'YOUR_DATABASE_PASSWORD';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (\PDOException $e) {
    error_log($e->getMessage());
    exit;
}
?>
