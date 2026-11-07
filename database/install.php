<?php
// Crea las tablas y carga los datos iniciales. Uso: php database/install.php
// Se puede repetir: no sobrescribe datos que ya existan.

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../src/bootstrap.php';

$pdo = db();
if ($pdo === null) {
    fwrite(STDERR, "No se pudo conectar a MySQL. Revisa config/config.php.\n");
    exit(1);
}

foreach (array_filter(array_map('trim', explode(';', file_get_contents(__DIR__ . '/schema.sql')))) as $statement) {
    $pdo->exec($statement);
}

$defaults = require __DIR__ . '/../config/defaults.php';

$insertSetting = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value, confirmed) VALUES (?, ?, ?)');
foreach ($defaults['settings'] as $key => $setting) {
    $insertSetting->execute([$key, $setting['value'], $setting['confirmed']]);
}

$insertImage = $pdo->prepare('INSERT IGNORE INTO images (slot, url, alt) VALUES (?, ?, ?)');
foreach ($defaults['images'] as $slot => $image) {
    $insertImage->execute([$slot, $image['url'], $image['alt']]);
}

if ((int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn() === 0) {
    $insert = $pdo->prepare('INSERT INTO contacts (type, label, value, confirmed, sort_order) VALUES (?, ?, ?, ?, ?)');
    foreach ($defaults['contacts'] as $i => $contact) {
        $insert->execute([$contact['type'], $contact['label'], $contact['value'], $contact['confirmed'], $i]);
    }
}

if ((int) $pdo->query('SELECT COUNT(*) FROM stats')->fetchColumn() === 0) {
    $insert = $pdo->prepare('INSERT INTO stats (value, label, confirmed, sort_order) VALUES (?, ?, ?, ?)');
    foreach ($defaults['stats'] as $i => $stat) {
        $insert->execute([$stat['value'], $stat['label'], $stat['confirmed'], $i]);
    }
}

echo "Base de datos lista.\n";
