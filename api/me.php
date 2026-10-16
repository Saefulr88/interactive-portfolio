<?php
require __DIR__ . '/_init.php';
jsonResponse(['user' => $_SESSION['user'] ?? null]);
?>
