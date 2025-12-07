<?php
// db.php - central DB connection
$DB_HOST = '127.0.0.1';
$DB_NAME = 'banana_game';
$DB_USER = 'root';
$DB_PASS = ''; // change if needed

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>'DB connection failed: '.$e->getMessage()]);
    exit;
}
session_start();
