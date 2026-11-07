<?php
// Datos iniciales editables. `database/install.php` los copia a MySQL una vez;
// después, la fuente de verdad es la base de datos.
// `confirmed => 0` hace que el sitio muestre la marca "por confirmar" y no cree enlaces.

return [
    'settings' => [
        'location' => ['value' => 'Machala, El Oro, Ecuador', 'confirmed' => 0],
        // Vacíos: el enlace no aparece hasta que tengan un valor.
        'facebook' => ['value' => '', 'confirmed' => 1],
        'instagram' => ['value' => '', 'confirmed' => 1],
        'map_url' => ['value' => '', 'confirmed' => 1],
    ],
    'contacts' => [
        ['type' => 'phone', 'label' => 'Teléfono', 'value' => '+593 99 000 0000', 'confirmed' => 0],
        ['type' => 'email', 'label' => 'Correo', 'value' => 'contacto@redtech.com', 'confirmed' => 0],
    ],
    // Imágenes editables: `url` admite una dirección completa o una ruta local como assets/img/foto.webp.
    // Ver CREDITS.md. Las fotos actuales son ilustrativas: no muestran trabajos ni productos de RedTech.
    'images' => [
        'hero' => ['url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31', 'alt' => 'Rack de red con cables de colores conectados'],
        'cableado' => ['url' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8', 'alt' => 'Panel de conexiones con cables de red azules'],
        'camaras' => ['url' => 'https://images.unsplash.com/photo-1528312635006-8ea0bc49ec63', 'alt' => 'Cámara de seguridad tipo domo en un poste'],
        'servidores' => ['url' => 'https://images.unsplash.com/photo-1695668548342-c0c1ad479aee', 'alt' => 'Rack de servidores en una sala técnica'],
        'nosotros' => ['url' => 'https://images.unsplash.com/photo-1785682117394-4c8d27afc12a', 'alt' => 'Técnico revisando un rack con un equipo de medición'],
        'empresas' => ['url' => 'https://images.unsplash.com/photo-1584169417032-d34e8d805e8b', 'alt' => 'Pasillo de una sala técnica con racks de equipos'],
        'hogares' => ['url' => 'https://images.unsplash.com/photo-1549109926-58f039549485', 'alt' => 'Cámara de seguridad doméstica en una pared'],
        'producto_cables' => ['url' => 'https://images.unsplash.com/photo-1683322499436-f4383dd59f5a', 'alt' => 'Cables de red azules conectados a un equipo'],
        'producto_servidores' => ['url' => 'https://images.unsplash.com/photo-1698668975271-2ba9a323be6b', 'alt' => 'Equipos de red en rack con cables conectados'],
        'producto_routers' => ['url' => 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2', 'alt' => 'Router blanco con dos antenas'],
        'producto_camaras' => ['url' => 'https://images.unsplash.com/photo-1589935447067-5531094415d1', 'alt' => 'Dos cámaras de seguridad en un poste'],
    ],
    'stats' => [
        ['value' => '+150', 'label' => 'proyectos instalados', 'confirmed' => 0],
        ['value' => '24/7', 'label' => 'soporte técnico', 'confirmed' => 0],
        ['value' => '100%', 'label' => 'equipos certificados', 'confirmed' => 0],
    ],
];
