<?php

declare(strict_types=1);

/**
 * Respaldos desde el panel: la base de datos como archivo .sql (se importa con
 * phpMyAdmin) y un ZIP con el código, las fotos subidas y las facturas.
 * Se generan al momento en una carpeta temporal y se borran tras la descarga.
 */

/** Escribe un volcado SQL completo de la base en $path. */
function backup_database(PDO $pdo, string $path): void
{
    $out = fopen($path, 'wb');
    if ($out === false) {
        throw new RuntimeException('No se pudo crear el archivo del respaldo.');
    }
    $name = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
    fwrite($out, "-- Respaldo de la base de datos de RedTech\n-- Base: $name\n-- Fecha: " . date('Y-m-d H:i:s') . "\n-- Para restaurar: phpMyAdmin → Importar este archivo.\n\n");
    fwrite($out, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n\n");

    foreach ($pdo->query('SHOW FULL TABLES WHERE Table_type = \'BASE TABLE\'')->fetchAll(PDO::FETCH_NUM) as [$table]) {
        $quoted = '`' . str_replace('`', '``', $table) . '`';
        $create = $pdo->query("SHOW CREATE TABLE $quoted")->fetch(PDO::FETCH_NUM)[1];
        fwrite($out, "DROP TABLE IF EXISTS $quoted;\n$create;\n\n");

        $rows = $pdo->query("SELECT * FROM $quoted", PDO::FETCH_ASSOC);
        $batch = [];
        $columns = null;
        foreach ($rows as $row) {
            $columns ??= '(' . implode(', ', array_map(static fn ($c) => '`' . str_replace('`', '``', $c) . '`', array_keys($row))) . ')';
            $batch[] = '(' . implode(', ', array_map(static fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), $row)) . ')';
            if (count($batch) === 100) {
                fwrite($out, "INSERT INTO $quoted $columns VALUES\n" . implode(",\n", $batch) . ";\n");
                $batch = [];
            }
        }
        if ($batch) {
            fwrite($out, "INSERT INTO $quoted $columns VALUES\n" . implode(",\n", $batch) . ";\n");
        }
        fwrite($out, "\n");
    }
    fwrite($out, "SET FOREIGN_KEY_CHECKS = 1;\n");
    fclose($out);
}

/** Agrega una carpeta al ZIP (recursivo), saltando lo que indique $skip. */
function backup_add_dir(ZipArchive $zip, string $dir, string $prefix, callable $skip): void
{
    if (!is_dir($dir)) {
        return;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $relative = $prefix . '/' . str_replace('\\', '/', substr($file->getPathname(), strlen($dir) + 1));
        if (!$skip($relative)) {
            $zip->addFile($file->getPathname(), $relative);
        }
    }
}

/**
 * Crea el ZIP completo en $path: database.sql + el proyecto con la misma estructura
 * del repositorio (public/, src/, config/, database/, storage/).
 * config/config.php (contraseña de MySQL) queda fuera salvo que $withConfig sea true.
 */
function backup_full(PDO $pdo, string $path, bool $withConfig): void
{
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('El hosting no tiene la extensión ZIP de PHP. Descarga solo la base de datos.');
    }
    $sql = $path . '.sql';
    backup_database($pdo, $sql);

    $zip = new ZipArchive();
    if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('No se pudo crear el ZIP del respaldo.');
    }
    $zip->addFile($sql, 'database.sql');
    $zip->addFromString('LEEME.txt', implode("\n", [
        'Respaldo de RedTech — ' . date('Y-m-d H:i'),
        '',
        'database.sql   Base de datos completa. Restaurar: phpMyAdmin → Importar.',
        'public/        Lo que va en public_html (incluye uploads/ con las fotos).',
        'src/, config/, database/, storage/   Van junto a public_html (storage/ guarda las facturas).',
        $withConfig ? 'config/config.php incluido: contiene la contraseña de MySQL, guárdalo en un lugar seguro.' : 'config/config.php NO incluido (tiene la contraseña de MySQL): créalo de nuevo desde config.example.php.',
        '',
    ]));

    $skip = static fn (string $p): bool => (bool) preg_match('#(^|/)\.git/|(^|/)node_modules/|\.log$#', $p)
        || ($p === 'config/config.php' && !$withConfig);
    backup_add_dir($zip, PUBLIC_DIR, 'public', $skip);
    foreach (['src', 'config', 'database', 'storage'] as $folder) {
        backup_add_dir($zip, ROOT . '/' . $folder, $folder, $skip);
    }
    foreach (['.htaccess', 'README.md'] as $file) {
        if (is_file(ROOT . '/' . $file)) {
            $zip->addFile(ROOT . '/' . $file, $file);
        }
    }
    $zip->close();
    @unlink($sql);
}

/** Envía un archivo al navegador y lo borra. */
function backup_send(string $path, string $name, string $type): void
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: ' . $type);
    header('Content-Length: ' . filesize($path));
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Cache-Control: private, no-store');
    header('X-Content-Type-Options: nosniff');
    readfile($path);
    @unlink($path);
    exit;
}
