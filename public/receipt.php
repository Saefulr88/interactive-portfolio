<?php
// contoh: /interactive-portfolio/public/receipt.php?id=1
require __DIR__ . '/../api/_init.php';
$id = $_GET['id'] ?? null;
if (!$id) { echo "<h3>id transaksi diperlukan</h3>"; exit; }
$stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ?');
stmt->execute([$id]);
$tx = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tx) { echo "<h3>Transaksi tidak ditemukan</h3>"; exit; }
$stmt = $pdo->prepare('SELECT * FROM transaction_items WHERE transaction_id = ?');
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

function fmt($n){ return number_format($n,0,',','.'); }
function esc($s){ return htmlspecialchars($s); }

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Kwitansi <?= esc($tx['invoice']) ?></title>
<style>
@media print { @page { margin:6mm; } }
body{ font-family:monospace; width:300px; padding:6px; }
h2,p{ margin:0; text-align:center; }
table{ width:100%; border-collapse:collapse; margin-top:8px; }
td{ vertical-align:top; padding:2px 0; }
.center{text-align:center;}
</style>
</head>
<body>
  <h2>Toko Contoh</h2>
  <p>Jl. Contoh No.1</p>
  <hr>
  <p>No: <?= esc($tx['invoice']) ?> &nbsp;&nbsp; <?= esc($tx['date']) ?></p>
  <table>
    <?php foreach($items as $it): ?>
      <tr>
        <td style="padding:4px 0"><?= esc($it['name']) ?></td>
        <td style="text-align:right;padding:4px 0"><?= fmt($it['price']) ?></td>
        <td style="text-align:center;padding:4px 0"><?= esc($it['quantity']) ?></td>
        <td style="text-align:right;padding:4px 0"><?= fmt($it['subtotal']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <hr>
  <table style="width:100%">
    <tr><td>Total</td><td style="text-align:right"><?= fmt($tx['total']) ?></td></tr>
    <tr><td>Dibayar</td><td style="text-align:right"><?= fmt($tx['payment']) ?></td></tr>
    <tr><td>Kembali</td><td style="text-align:right"><?= fmt($tx['change']) ?></td></tr>
  </table>
  <p class="center">--- Terima Kasih ---</p>
  <script>window.onload=function(){ setTimeout(()=>window.print(),300); };</script>
</body>
</html>
