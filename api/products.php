<?php
require __DIR__ . '/_init.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse($rows);
}
if ($method === 'POST') {
    requireAuth();
    $input = getJsonInput();
    $sku = $input['sku'] ?? null;
    $name = $input['name'] ?? null;
    $price = intval($input['price'] ?? 0);
    $stock = intval($input['stock'] ?? 0);
    if (!$name) jsonResponse(['error'=>'name required'], 400);
    $ins = $pdo->prepare('INSERT INTO products (sku, name, price, stock) VALUES (?, ?, ?, ?)');
    $ins->execute([$sku, $name, $price, $stock]);
    jsonResponse(['id' => $pdo->lastInsertId()]);
}
jsonResponse(['error'=>'Method not allowed'], 405);
?>
