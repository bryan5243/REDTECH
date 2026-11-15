<?php
// Copia este archivo como config/config.php y completa los datos de MySQL.
// config/config.php no se sube al repositorio.

return [
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'redtech',
        'user' => 'redtech_user',
        'pass' => '',
    ],
    // Clave para crear el primer administrador en /admin. Usa una frase larga y
    // bórrala de aquí cuando ya tengas tu usuario.
    'admin_setup_key' => '',
];
