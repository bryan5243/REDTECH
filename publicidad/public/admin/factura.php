<?php

declare(strict_types=1);

// Descarga la factura de un pedido. Solo para administradores: los archivos
// están fuera de la carpeta pública.
define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

$pdo = require_admin();
$statement = $pdo->prepare('SELECT id, invoice FROM orders WHERE id = ?');
$statement->execute([(int) ($_GET['id'] ?? 0)]);
$order = $statement->fetch();
$path = $order ? (string) $order['invoice'] : '';
$file = ROOT . '/' . $path;

if ($path === '' || !preg_match('#^storage/facturas/\d{6}/[a-f0-9]{16}\.(pdf|jpg|png|webp)$#', $path) || !is_file($file)) {
    http_response_code(404);
    exit('Factura no encontrada.');
}

$ext = pathinfo($file, PATHINFO_EXTENSION);
$types = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
header('Content-Type: ' . $types[$ext]);
header('Content-Length: ' . filesize($file));
header('Content-Disposition: inline; filename="factura-pedido-' . (int) $order['id'] . '.' . $ext . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($file);
