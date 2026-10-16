<?php
// Common init: start session, buka koneksi PDO
session_start();
$pdo = new PDO('sqlite:' . __DIR__ . '/../data.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON;');

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function getJsonInput() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function requireAuth() {
    if (!isset($_SESSION['user'])) {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
}
?>
