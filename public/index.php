<?php

declare(strict_types=1);

// En cPanel: el contenido de public/ va en public_html/, y config/, src/ y database/
// van junto a public_html/, fuera del alcance de la web. Ver README.md.
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/catalog.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/quote.php';

start_session();

$content = require __DIR__ . '/../src/content.php';
$interests = quote_interests();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = handle_quote($_POST, $interests);
    if ($errors === []) {
        header('Location: ./?enviado=1#contacto', true, 303);
    } else {
        $_SESSION['quote_errors'] = $errors;
        header('Location: ./#contacto', true, 303);
    }
    exit;
}

$errors = $_SESSION['quote_errors'] ?? [];
$old = $_SESSION['quote_old'] ?? [];
unset($_SESSION['quote_errors'], $_SESSION['quote_old']);

$selected = $old['interest'] ?? (string) ($_GET['interes'] ?? '');
$sent = isset($_GET['enviado']);
$site = site_data();
$featured = products(['featured' => true, 'limit' => 8]);
$categories = categories();

require __DIR__ . '/../src/home.php';
