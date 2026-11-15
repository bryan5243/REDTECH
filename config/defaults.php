<?php
// Datos iniciales editables. `database/install.php` los copia a MySQL una vez;
// después, la fuente de verdad es la base de datos.
// `confirmed => 0` hace que el sitio muestre la marca "por confirmar" y no cree enlaces.

return [
    'settings' => [
        'location' => ['value' => 'Machala, El Oro, Ecuador', 'confirmed' => 0],
        // Vacíos: el enlace no aparece hasta que tengan un valor.
        'facebook' => ['value' => 'https://www.facebook.com/RedTechPublicidad/', 'confirmed' => 1],
        'instagram' => ['value' => '', 'confirmed' => 1],
        'map_url' => ['value' => '', 'confirmed' => 1],
        'tiktok' => ['value' => '', 'confirmed' => 1],
        // Mapa de Contacto: una dirección (ej. "Av. 25 de Junio y Guayas, Machala") o el enlace
        // "Insertar un mapa" de Google Maps (https://www.google.com/maps/embed?...). Vacío: usa la ubicación.
        'map_embed' => ['value' => '', 'confirmed' => 1],
        // Número de WhatsApp para pedidos, solo dígitos con código de país (ej. 593991234567).
        // Vacío: el carrito avisa que falta configurarlo y el botón flotante no aparece.
        'whatsapp' => ['value' => '593968447384', 'confirmed' => 1],
        'schedule' => ['value' => 'Lunes a viernes, 08:00 a 18:00', 'confirmed' => 0],
        // 1 muestra en la tienda el aviso de que el catálogo es de ejemplo.
        'catalog_sample' => ['value' => '1', 'confirmed' => 1],
        // Logo e identidad. Vacíos: logo de texto "RedTech" y colores originales.
        'logo' => ['value' => '', 'confirmed' => 1],
        'logo_dark' => ['value' => '', 'confirmed' => 1],
        'color_primary' => ['value' => '', 'confirmed' => 1],
        'color_accent' => ['value' => '', 'confirmed' => 1],
        'color_dark' => ['value' => '', 'confirmed' => 1],
    ],
    'contacts' => [
        ['type' => 'phone', 'label' => 'WhatsApp', 'value' => '+593 96 844 7384', 'confirmed' => 1],
        ['type' => 'email', 'label' => 'Correo', 'value' => 'redtech24@hotmail.com', 'confirmed' => 1],
    ],
    // Imágenes editables: `url` admite una dirección completa o una ruta local como assets/img/foto.webp.
    // Ver CREDITS.md. Las fotos actuales son ilustrativas: no muestran trabajos ni productos de RedTech.
    // Imágenes editables: `url` admite una dirección completa o una ruta local como assets/img/foto.webp.
    // Las actuales son ilustraciones de ejemplo: reemplázalas por fotos de tus trabajos desde el panel.
    'images' => [
        'portada' => ['url' => 'assets/img/marca/fondo.svg', 'alt' => 'Fondo con formas de color de la marca'],
        'portada_tarjeta' => ['url' => 'assets/img/marca/portada.svg', 'alt' => 'Letrero, volante, tarjeta y adhesivos de una marca'],
        'diseno' => ['url' => 'assets/img/marca/diseno-logo.svg', 'alt' => 'Logo en diseño en la pantalla'],
        'impresion' => ['url' => 'assets/img/marca/gigantografia.svg', 'alt' => 'Gigantografía impresa en lona'],
        'rotulacion' => ['url' => 'assets/img/marca/rotulo.svg', 'alt' => 'Letrero rotulado en la fachada de un local'],
        'equipo' => ['url' => 'assets/img/marca/vinilos.svg', 'alt' => 'Adhesivos y vinilos impresos'],
        'sector_comercios' => ['url' => 'assets/img/marca/rotulo.svg', 'alt' => 'Fachada de comercio con letrero'],
        'sector_eventos' => ['url' => 'assets/img/marca/gigantografia.svg', 'alt' => 'Lona para inauguración'],
        'sector_emprendedores' => ['url' => 'assets/img/marca/tarjetas.svg', 'alt' => 'Tarjetas de presentación'],
        'sector_empresas' => ['url' => 'assets/img/marca/diseno-logo.svg', 'alt' => 'Diseño de identidad de marca'],
    ],
    'stats' => [
        ['value' => '2 mil', 'label' => 'seguidores en Facebook', 'confirmed' => 1],
        ['value' => '100%', 'label' => 'nos recomiendan en Facebook', 'confirmed' => 1],
        ['value' => '+9', 'label' => 'años de trabajo', 'confirmed' => 0],
    ],
];
