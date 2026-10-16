<?php
// Halaman POS (minimal). Perhatikan: app.js melakukan redirect ke login jika belum login.
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Aplikasi Kasir</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
    <h1>Aplikasi Kasir</h1>
    <div class="layout">
      <div class="left">
        <h3>Daftar Produk</h3>
        <div id="products"></div>
      </div>
      <div class="right">
        <h3>Keranjang</h3>
        <table id="cartTable">
          <thead><tr><th>Nama</th><th>Harga</th><th>Qty</th><th>Sub</th><th></th></tr></thead>
          <tbody></tbody>
        </table>
        <div class="summary">
          <div>Total: <span id="total">0</span></div>
          <div>
            Bayar: <input id="payment" type="number" value="0">
          </div>
          <button id="checkout">Checkout & Cetak</button>
          <button id="logout">Logout</button>
        </div>
      </div>
    </div>
  </div>
  <script src="app.js"></script>
</body>
</html>
