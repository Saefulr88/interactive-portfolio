<?php
require __DIR__ . '/_init.php';
requireAuth();
$date = $_GET['date'] ?? date('Y-m-d');
$from = $date . ' 00:00:00';
$to = $date . ' 23:59:59';

// summary
$stmt = $pdo->prepare('SELECT COUNT(*) as tx_count, COALESCE(SUM(total),0) as total_sales FROM transactions WHERE date BETWEEN ? AND ?');
$stmt->execute([$from,$to]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);

// top products
$stmt = $pdo->prepare("\n  SELECT ti.product_id, ti.name, SUM(ti.quantity) as qty, SUM(ti.subtotal) as subtotal\n  FROM transaction_items ti\n  JOIN transactions t ON ti.transaction_id = t.id\n  WHERE t.date BETWEEN ? AND ?\n  GROUP BY ti.product_id, ti.name\n  ORDER BY qty DESC\n  LIMIT 10\n");
$stmt->execute([$from,$to]);
$top = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse(['date'=>$date, 'summary'=>$summary, 'top_products'=>$top]);
?>
