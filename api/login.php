<?php
require __DIR__ . '/_init.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}
$input = getJsonInput();
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
if (!$username || !$password) jsonResponse(['error' => 'username & password required'], 400);

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) jsonResponse(['error' => 'User tidak ditemukan'], 401);
if (!password_verify($password, $user['password_hash'])) jsonResponse(['error' => 'Password salah'], 401);

$_SESSION['user'] = ['id'=>$user['id'], 'username'=>$user['username'], 'name'=>$user['name'], 'role'=>$user['role']];
jsonResponse(['ok'=>true, 'user'=>$_SESSION['user']]);
?>
