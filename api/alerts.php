<?php
require __DIR__ . '/_init.php';
requireAuth();
$threshold = intval($_GET['threshold'] ?? 5);
$stmt = $pdo->prepare('SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC');
$stmt->execute([$threshold]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
jsonResponse(['threshold'=>$threshold, 'items'=>$rows]);
?>
