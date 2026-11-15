<?php
// Crea las tablas y carga los datos iniciales. Uso: php database/install.php
// Sin terminal (hosting gratuito), abre /admin/ y usa la clave de instalación.
// Se puede repetir: no sobrescribe datos que ya existan.

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/install.php';

$pdo = db();
if ($pdo === null) {
    fwrite(STDERR, "No se pudo conectar a MySQL. Revisa config/config.php.\n");
    exit(1);
}

foreach (run_install($pdo) as $line) {
    echo $line, "\n";
}
echo "Entra a /admin para crear tu usuario (usa admin_setup_key de config/config.php).\n";
