<?php
// Jalankan sekali: php init_db.php atau akses lewat browser
// Membuat data.db dari schema.sql dan menambahkan seed product + admin
$schemaFile = __DIR__ . '/db/schema.sql';
$dbFile = __DIR__ . '/data.db';

if (!file_exists($schemaFile)) {
    die("schema.sql tidak ditemukan di db/ \n");
}

$pdo = new PDO('sqlite:' . $dbFile);
$schema = file_get_contents($schemaFile);
$pdo->exec('PRAGMA foreign_keys = ON;');
$pdo->exec($schema);

// insert sample products jika belum ada
$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM products");
$cnt = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
if ($cnt == 0) {
    $insert = $pdo->prepare("INSERT INTO products (sku, name, price, stock) VALUES (?, ?, ?, ?)");
    $insert->execute(['P001','Roti Tawar',5000,50]);
    $insert->execute(['P002','Air Mineral 600ml',3000,120]);
    $insert->execute(['P003','Kopi Sachet',2000,200]);
    echo "Seed produk ditambahkan.\n";
} else {
    echo "Produk sudah ada, skip seed produk.\n";
}

// create admin user jika belum ada
$stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM users WHERE username = ?");
$stmt->execute(['admin']);
$cnt = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
if ($cnt == 0) {
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare("INSERT INTO users (username, password_hash, name, role) VALUES (?, ?, ?, ?)");
    $ins->execute(['admin', $hash, 'Admin', 'admin']);
    echo "Admin dibuat: username=admin password=admin123\n";
} else {
    echo "Admin sudah ada, skip.\n";
}

echo "Inisialisasi selesai. File DB: data.db\n";
?>
