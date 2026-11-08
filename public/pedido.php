<?php

declare(strict_types=1);

// Registra en la base de datos el pedido que el cliente envía por WhatsApp,
// para verlo después en /admin. Los precios se toman del catálogo, no del navegador.
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/catalog.php';
require __DIR__ . '/../src/install.php';

start_session();
header('Content-Type: application/json; charset=utf-8');

$input = json_decode((string) file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_array($input) || !csrf_valid($input['csrf'] ?? null)) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

$name = trim(mb_substr((string) ($input['nombre'] ?? ''), 0, 120));
$city = trim(mb_substr((string) ($input['ciudad'] ?? ''), 0, 120));
$notes = trim(mb_substr((string) ($input['notas'] ?? ''), 0, 500));
$items = is_array($input['items'] ?? null) ? array_slice($input['items'], 0, 50) : [];
$pdo = db();

if ($name === '' || $items === [] || $pdo === null) {
    echo json_encode(['ok' => false]);
    exit;
}

$lines = [];
$total = 0.0;
$statement = $pdo->prepare('SELECT id, slug, name, price, image FROM products WHERE id = ? AND active = 1');
foreach ($items as $item) {
    $qty = max(1, min(999, (int) ($item['qty'] ?? 1)));
    $statement->execute([(int) ($item['id'] ?? 0)]);
    $product = $statement->fetch();
    if (!$product) {
        continue;
    }
    $price = $product['price'] === null ? null : (float) $product['price'];
    $total += ($price ?? 0) * $qty;
    $lines[] = ['id' => (int) $product['id'], 'slug' => $product['slug'], 'name' => $product['name'], 'image' => $product['image'], 'qty' => $qty, 'price' => $price];
}

if ($lines === []) {
    echo json_encode(['ok' => false]);
    exit;
}

try {
    ensure_order_code($pdo);
    // Código aleatorio: la página del pedido no se puede adivinar recorriendo números.
    $code = bin2hex(random_bytes(6));
    $pdo->prepare('INSERT INTO orders (code, customer_name, city, notes, items, total) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$code, $name, $city ?: null, $notes ?: null, json_encode($lines, JSON_UNESCAPED_UNICODE), $total]);
    echo json_encode(['ok' => true, 'code' => $code, 'number' => (int) $pdo->lastInsertId()]);
} catch (PDOException $exception) {
    error_log('RedTech DB: ' . $exception->getMessage());
    echo json_encode(['ok' => false]);
}
