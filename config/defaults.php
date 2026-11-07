<?php
// Datos iniciales editables. `database/install.php` los copia a MySQL una vez;
// después, la fuente de verdad es la base de datos.
// `confirmed => 0` hace que el sitio muestre la marca "por confirmar" y no cree enlaces.

return [
    'settings' => [
        'location' => ['value' => 'Machala, El Oro, Ecuador', 'confirmed' => 0],
    ],
    'contacts' => [
        ['type' => 'phone', 'label' => 'Teléfono', 'value' => '+593 99 000 0000', 'confirmed' => 0],
        ['type' => 'email', 'label' => 'Correo', 'value' => 'contacto@redtech.com', 'confirmed' => 0],
    ],
    'stats' => [
        ['value' => '+150', 'label' => 'proyectos instalados', 'confirmed' => 0],
        ['value' => '24/7', 'label' => 'soporte técnico', 'confirmed' => 0],
        ['value' => '100%', 'label' => 'equipos certificados', 'confirmed' => 0],
    ],
];
