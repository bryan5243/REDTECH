<?php

declare(strict_types=1);

/** Valida y guarda una solicitud. Devuelve los errores por campo; vacío si se guardó. */
function quote_interests(): array
{
    return [
        'cableado' => 'Cableado estructurado',
        'camaras' => 'Cámaras de seguridad',
        'servidores' => 'Servidores',
        'redes' => 'Redes y Wi-Fi',
        'soporte' => 'Soporte y mantenimiento',
        'tienda' => 'Compra de equipos',
        'publicidad' => 'Publicidad: diseño, impresión o rotulación',
    ];
}

function handle_quote(array $input, array $allowedInterests): array
{
    $old = [
        'name' => trim((string) ($input['name'] ?? '')),
        'phone' => trim((string) ($input['phone'] ?? '')),
        'email' => trim((string) ($input['email'] ?? '')),
        'company' => trim((string) ($input['company'] ?? '')),
        'interest' => (string) ($input['interest'] ?? ''),
        'message' => trim((string) ($input['message'] ?? '')),
    ];
    $_SESSION['quote_old'] = $old;
    $errors = [];

    if (!csrf_valid($input['csrf'] ?? null)) {
        return ['form' => 'Tu sesión caducó. Revisa tus datos y vuelve a enviar.'];
    }
    // Campo trampa: los visitantes reales no lo ven. A los bots se les responde como si nada.
    if (($input['website'] ?? '') !== '') {
        unset($_SESSION['quote_old']);
        return [];
    }

    if ($old['name'] === '' || mb_strlen($old['name']) > 120) {
        $errors['name'] = 'Escribe tu nombre para saber a quién llamar.';
    }
    if ($old['phone'] === '' && $old['email'] === '') {
        $errors['phone'] = 'Déjanos un teléfono o un correo para responderte.';
    }
    if ($old['phone'] !== '' && !preg_match('/^[0-9+() .-]{7,40}$/', $old['phone'])) {
        $errors['phone'] = 'Revisa el teléfono: usa solo números, espacios y +.';
    }
    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Revisa el correo: debe tener la forma nombre@dominio.com.';
    }
    if (!array_key_exists($old['interest'], $allowedInterests)) {
        $errors['interest'] = 'Elige qué necesitas.';
    }
    if (mb_strlen($old['company']) > 120 || mb_strlen($old['message']) > 2000) {
        $errors['message'] = 'El texto es demasiado largo; resúmelo un poco.';
    }
    if ($errors !== []) {
        return $errors;
    }

    $pdo = db();
    if ($pdo === null) {
        return ['form' => 'No pudimos guardar tu solicitud. Inténtalo de nuevo en unos minutos.'];
    }

    try {
        $pdo->prepare(
            'INSERT INTO quote_requests (name, phone, email, company, interest, message)
             VALUES (?, ?, ?, ?, ?, ?)'
        )->execute([
            $old['name'],
            $old['phone'] ?: null,
            $old['email'] ?: null,
            $old['company'] ?: null,
            $old['interest'],
            $old['message'] ?: null,
        ]);
    } catch (PDOException $exception) {
        error_log('RedTech DB: ' . $exception->getMessage());
        return ['form' => 'No pudimos guardar tu solicitud. Inténtalo de nuevo en unos minutos.'];
    }

    unset($_SESSION['quote_old']);
    return [];
}
