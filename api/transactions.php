<?php
require __DIR__ . '/_init.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    requireAuth();
    $input = getJsonInput();
    $items = $input['items'] ?? [];
    $payment = intval($input['payment'] ?? 0);
    if (!is_array($items) || count($items) === 0) jsonResponse(['error'=>'Items tidak boleh kosong'], 400);
    $total = 0;
    foreach ($items as $it) $total += intval($it['price']) * intval($it['quantity']);
    $change = $payment - $total;
    if ($change < 0) jsonResponse(['error'=>'Uang bayar kurang'], 400);
    $invoice = 'INV-' . time();

    try {
        $pdo->beginTransaction();
        $ins = $pdo->prepare('INSERT INTO transactions (invoice, total, payment, change) VALUES (?, ?, ?, ?)');
        $ins->execute([$invoice, $total, $payment, $change]);
        $txId = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO transaction_items (transaction_id, product_id, name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)');
        $up = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
        foreach ($items as $it) {
            $pid = $it['product_id'] ?? null;
            $name = $it['name'] ?? '';
            $price = intval($it['price']);
            $qty = intval($it['quantity']);
            $subtotal = $price * $qty;
            $stmt->execute([$txId, $pid, $name, $price, $qty, $subtotal]);
            if ($pid) $up->execute([$qty, $pid]);
        }
        $pdo->commit();
        jsonResponse(['id'=>$txId, 'invoice'=>$invoice]);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['error'=>$e->getMessage()], 500);
    }
}

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    if (!$id) jsonResponse(['error'=>'id required'], 400);
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ?');
    $stmt->execute([$id]);
    $tx = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tx) jsonResponse(['error'=>'Transaksi tidak ditemukan'], 404);
    $stmt = $pdo->prepare('SELECT * FROM transaction_items WHERE transaction_id = ?');
    $stmt->execute([$id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse(['transaction'=>$tx, 'items'=>$items]);
}

jsonResponse(['error'=>'Method not allowed'], 405);
?>
