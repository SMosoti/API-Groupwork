<?php
// db_connect.php
$DB_HOST = '127.0.0.1';
$DB_PORT = '3307';              
$DB_NAME = 'api_groupwork';
$DB_USER = 'root';
$DB_PASS = '';                  

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

try {
    $conn = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>
