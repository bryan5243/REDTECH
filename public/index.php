<?php

declare(strict_types=1);

// En cPanel: el contenido de public/ va en public_html/, y config/, src/ y database/
// van junto a public_html/, fuera del alcance de la web. Ver README.md.
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/quote.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');

$content = require __DIR__ . '/../src/content.php';
$interests = [];
foreach ($content['services'] as $key => $service) {
    $interests[$key] = $service['name'];
}
$interests['tienda'] = 'Productos de la tienda';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = handle_quote($_POST, $interests);
    $para = ($_POST['para'] ?? '') === 'hogar' ? 'hogar' : 'empresa';
    $query = ['para' => $para];
    if ($errors === []) {
        $query['enviado'] = 1;
    } else {
        $_SESSION['quote_errors'] = $errors;
    }
    header('Location: ?' . http_build_query($query) . '#contacto', true, 303);
    exit;
}

$para = ($_GET['para'] ?? '') === 'hogar' ? 'hogar' : 'empresa';
$errors = $_SESSION['quote_errors'] ?? [];
$old = $_SESSION['quote_old'] ?? [];
unset($_SESSION['quote_errors'], $_SESSION['quote_old']);

$selected = $old['interest'] ?? (string) ($_GET['interes'] ?? '');
$sent = isset($_GET['enviado']);
$product = $content['products'][(string) ($_GET['producto'] ?? '')] ?? null;
$prefilledMessage = $product === null ? '' : 'Quiero consultar por: ' . $product . '.';
$site = site_data();
$location = $site['settings']['location'];
$csrf = csrf_token();

require __DIR__ . '/../src/home.php';
