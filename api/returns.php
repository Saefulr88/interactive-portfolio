<?php
require __DIR__ . '/_init.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    requireAuth();
    $input = getJsonInput();
    $transaction_id = $input['transaction_id'] ?? null;
    $items = $input['items'] ?? [];
    if (!is_array($items) || count($items) === 0) jsonResponse(['error'=>'Items kosong'], 400);
    $total = 0;
    foreach ($items as $it) $total += intval($it['price']) * intval($it['quantity']);
    $returnNo = 'RET-' . time();

    try {
        $pdo->beginTransaction();
        $ins = $pdo->prepare('INSERT INTO returns (return_no, transaction_id, total, created_by) VALUES (?, ?, ?, ?)');
        $ins->execute([$returnNo, $transaction_id, $total, $_SESSION['user']['id']]);
        $retId = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO return_items (return_id, transaction_item_id, product_id, name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $up = $pdo->prepare('UPDATE products SET stock = stock + ? WHERE id = ?');
        foreach ($items as $it) {
            $tid = $it['transaction_item_id'] ?? null;
            $pid = $it['product_id'] ?? null;
            $name = $it['name'] ?? '';
            $price = intval($it['price']);
            $qty = intval($it['quantity']);
            $subtotal = $price * $qty;
            $stmt->execute([$retId, $tid, $pid, $name, $price, $qty, $subtotal]);
            if ($pid) $up->execute([$qty, $pid]);
        }
        $pdo->commit();
        jsonResponse(['ok'=>true, 'returnId'=>$retId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['error'=>$e->getMessage()], 500);
    }
}

jsonResponse(['error'=>'Method not allowed'], 405);
?>
