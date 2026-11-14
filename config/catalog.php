<?php
// Catálogo inicial de EJEMPLO. `database/install.php` lo copia a MySQL una vez;
// después se administra desde /admin. Precios, nombres y fotos son ilustrativos:
// reemplázalos por tus productos reales antes de publicar.

$img = static fn (string $id): string => 'https://images.unsplash.com/' . $id;
$cables = $img('photo-1683322499436-f4383dd59f5a');
$patch = $img('photo-1544197150-b99a580bb7a8');
$rack = $img('photo-1558494949-ef010cbdcc31');
$servers = $img('photo-1695668548342-c0c1ad479aee');
$serverRack = $img('photo-1698668975271-2ba9a323be6b');
$room = $img('photo-1584169417032-d34e8d805e8b');
$router = $img('photo-1606904825846-647eb07f5be2');
$cameras = $img('photo-1589935447067-5531094415d1');
$dome = $img('photo-1528312635006-8ea0bc49ec63');
$homeCam = $img('photo-1549109926-58f039549485');
// Ilustraciones locales para la línea de publicidad (reemplázalas por fotos de tus trabajos).
$ad = static fn (string $name): string => 'assets/img/publicidad/' . $name . '.svg';

return [
    'categories' => [
        ['slug' => 'cables', 'name' => 'Cables y conectividad', 'description' => 'Cable UTP, fibra óptica, patch cords y accesorios de red.', 'image' => $cables],
        ['slug' => 'servidores', 'name' => 'Servidores y almacenamiento', 'description' => 'Servidores, NAS y racks para tu centro de datos.', 'image' => $servers],
        ['slug' => 'routers', 'name' => 'Routers y redes', 'description' => 'Routers, switches y puntos de acceso Wi-Fi.', 'image' => $router],
        ['slug' => 'camaras', 'name' => 'Cámaras y videovigilancia', 'description' => 'Cámaras IP, grabadores NVR y kits de seguridad.', 'image' => $cameras],
        ['slug' => 'publicidad', 'name' => 'Publicidad e impresión', 'description' => 'Diseño gráfico, impresión, gigantografías, adhesivos y rótulos para tu negocio.', 'image' => $ad('gigantografia')],
    ],
    'products' => [
        [
            'category' => 'cables', 'slug' => 'cable-utp-cat6-305m', 'name' => 'Cable UTP Cat6 bobina 305 m', 'brand' => 'RedTech Select',
            'summary' => 'Cable de cobre sólido para cableado estructurado en interiores.',
            'description' => "Bobina de cable UTP categoría 6 de 4 pares, ideal para redes de oficinas, bodegas y viviendas.\nSoporta Gigabit Ethernet y aplicaciones PoE.",
            'specs' => "Categoría: 6\nLongitud: 305 m\nConductor: cobre sólido 23 AWG\nUso: interior\nChaqueta: PVC",
            'price' => 145.00, 'stock' => 'disponible', 'image' => $cables, 'gallery' => [$patch, $rack], 'featured' => 1,
        ],
        [
            'category' => 'cables', 'slug' => 'patch-cord-cat6-1m', 'name' => 'Patch cord Cat6 1 m', 'brand' => 'RedTech Select',
            'summary' => 'Cable de conexión con conectores RJ45 moldeados.',
            'description' => 'Patch cord para conectar equipos al patch panel o al switch. Disponible en varios colores.',
            'specs' => "Categoría: 6\nLongitud: 1 m\nConectores: RJ45 moldeados\nColores: azul, gris, rojo",
            'price' => 3.50, 'stock' => 'disponible', 'image' => $patch, 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'cables', 'slug' => 'patch-panel-24-puertos', 'name' => 'Patch panel 24 puertos Cat6', 'brand' => 'RedTech Select',
            'summary' => 'Panel de parcheo para rack de 19", 1U.',
            'description' => 'Organiza y etiqueta las conexiones de tu red en el rack.',
            'specs' => "Puertos: 24 RJ45\nCategoría: 6\nTamaño: 1U, 19\"\nIncluye: organizador trasero",
            'price' => 48.00, 'stock' => 'bajo_pedido', 'image' => $rack, 'gallery' => [$patch], 'featured' => 0,
        ],
        [
            'category' => 'servidores', 'slug' => 'servidor-rack-1u', 'name' => 'Servidor rack 1U para pymes', 'brand' => 'RedTech Select',
            'summary' => 'Servidor compacto para aplicaciones, archivos y virtualización ligera.',
            'description' => "Servidor de montaje en rack pensado para pequeñas y medianas empresas.\nTe ayudamos a elegir la configuración y lo dejamos instalado.",
            'specs' => "Formato: rack 1U\nProcesador: según configuración\nMemoria: desde 16 GB\nAlmacenamiento: discos intercambiables en caliente\nFuente: redundante opcional",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $servers, 'gallery' => [$serverRack, $room], 'featured' => 1,
        ],
        [
            'category' => 'servidores', 'slug' => 'nas-4-bahias', 'name' => 'Almacenamiento NAS 4 bahías', 'brand' => 'RedTech Select',
            'summary' => 'Centraliza archivos y copias de seguridad de tu equipo.',
            'description' => 'Equipo NAS para compartir archivos en red y programar respaldos automáticos.',
            'specs' => "Bahías: 4\nRAID: 0, 1, 5, 10\nRed: 2 puertos Gigabit\nDiscos: no incluidos",
            'price' => 520.00, 'stock' => 'disponible', 'image' => $serverRack, 'gallery' => [$servers], 'featured' => 1,
        ],
        [
            'category' => 'servidores', 'slug' => 'rack-gabinete-42u', 'name' => 'Rack gabinete 42U', 'brand' => 'RedTech Select',
            'summary' => 'Gabinete cerrado con puerta de vidrio y ventilación.',
            'description' => 'Rack de piso para alojar servidores, switches y patch panels de forma ordenada y segura.',
            'specs' => "Altura: 42U\nAncho: 600 mm\nProfundidad: 1000 mm\nPuerta: vidrio templado con llave",
            'price' => 690.00, 'stock' => 'bajo_pedido', 'image' => $room, 'gallery' => [$rack], 'featured' => 0,
        ],
        [
            'category' => 'routers', 'slug' => 'router-wifi-6-doble-banda', 'name' => 'Router Wi-Fi 6 doble banda', 'brand' => 'RedTech Select',
            'summary' => 'Wi-Fi rápido y estable para hogar u oficina pequeña.',
            'description' => 'Router de doble banda con Wi-Fi 6 para más velocidad y más dispositivos conectados a la vez.',
            'specs' => "Estándar: Wi-Fi 6 (802.11ax)\nBandas: 2,4 GHz y 5 GHz\nPuertos: 4 LAN Gigabit, 1 WAN\nAntenas: 4 externas",
            'price' => 89.00, 'stock' => 'disponible', 'image' => $router, 'gallery' => [], 'featured' => 1,
        ],
        [
            'category' => 'routers', 'slug' => 'switch-24-puertos-poe', 'name' => 'Switch 24 puertos PoE+', 'brand' => 'RedTech Select',
            'summary' => 'Alimenta cámaras y puntos de acceso por el mismo cable de red.',
            'description' => 'Switch administrable con PoE+ para conectar y alimentar cámaras IP, teléfonos y antenas.',
            'specs' => "Puertos: 24 Gigabit PoE+ y 4 SFP\nPresupuesto PoE: 370 W\nGestión: web y VLAN\nMontaje: rack 1U",
            'price' => 410.00, 'stock' => 'disponible', 'image' => $rack, 'gallery' => [$patch], 'featured' => 1,
        ],
        [
            'category' => 'routers', 'slug' => 'access-point-wifi-6-techo', 'name' => 'Punto de acceso Wi-Fi 6 de techo', 'brand' => 'RedTech Select',
            'summary' => 'Cobertura Wi-Fi profesional para oficinas y locales.',
            'description' => 'Punto de acceso de montaje en techo, alimentado por PoE, con gestión centralizada.',
            'specs' => "Estándar: Wi-Fi 6\nAlimentación: PoE\nUsuarios simultáneos: hasta 200\nMontaje: techo o pared",
            'price' => 135.00, 'stock' => 'disponible', 'image' => $router, 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'camaras', 'slug' => 'camara-ip-domo-4mp', 'name' => 'Cámara IP domo 4 MP', 'brand' => 'RedTech Select',
            'summary' => 'Cámara de interior y exterior con visión nocturna.',
            'description' => 'Cámara tipo domo con resolución de 4 MP, visión nocturna infrarroja y alimentación PoE.',
            'specs' => "Resolución: 4 MP\nVisión nocturna: hasta 30 m\nProtección: IP67\nAlimentación: PoE / 12 V",
            'price' => 75.00, 'stock' => 'disponible', 'image' => $dome, 'gallery' => [$cameras], 'featured' => 1,
        ],
        [
            'category' => 'camaras', 'slug' => 'camara-ip-bullet-exterior', 'name' => 'Cámara IP bullet exterior', 'brand' => 'RedTech Select',
            'summary' => 'Ideal para perímetros, accesos y parqueaderos.',
            'description' => 'Cámara bullet resistente a la intemperie, con lente de largo alcance y detección de movimiento.',
            'specs' => "Resolución: 4 MP\nLente: 2,8 mm a 12 mm\nProtección: IP67\nAlertas: detección de movimiento",
            'price' => 92.00, 'stock' => 'disponible', 'image' => $cameras, 'gallery' => [$dome], 'featured' => 0,
        ],
        [
            'category' => 'camaras', 'slug' => 'kit-nvr-8-camaras', 'name' => 'Kit NVR 8 canales con 4 cámaras', 'brand' => 'RedTech Select',
            'summary' => 'Sistema completo de videovigilancia listo para instalar.',
            'description' => "Incluye grabador NVR de 8 canales, 4 cámaras IP y disco para grabación continua.\nVe tus cámaras desde el celular.",
            'specs' => "Canales: 8\nCámaras incluidas: 4 de 4 MP\nDisco: 2 TB\nAcceso remoto: app móvil",
            'price' => 480.00, 'stock' => 'disponible', 'image' => $homeCam, 'gallery' => [$cameras, $dome], 'featured' => 1,
        ],
        [
            'category' => 'publicidad', 'slug' => 'tarjetas-de-presentacion', 'name' => 'Tarjetas de presentación', 'brand' => 'RedTech Publicidad',
            'summary' => 'Tarjetas impresas a full color con tu diseño o con el que te hacemos.',
            'description' => "Tarjetas de presentación para tu negocio.\nSi no tienes diseño, te lo preparamos y te enviamos una prueba antes de imprimir.",
            'specs' => "Impresión: full color\nLados: uno o dos\nCantidad: según pedido\nDiseño: opcional",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('tarjetas'), 'gallery' => [], 'featured' => 1,
        ],
        [
            'category' => 'publicidad', 'slug' => 'gigantografia-lona', 'name' => 'Gigantografía en lona', 'brand' => 'RedTech Publicidad',
            'summary' => 'Lonas publicitarias para fachadas, eventos y promociones.',
            'description' => 'Impresión en gran formato en lona, lista para colgar o montar en estructura. Tú eliges la medida.',
            'specs' => "Material: lona\nMedida: a tu gusto\nAcabado: con ojales o para estructura\nUso: interior y exterior",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('gigantografia'), 'gallery' => [], 'featured' => 1,
        ],
        [
            'category' => 'publicidad', 'slug' => 'rotulo-letrero', 'name' => 'Rótulo o letrero para tu local', 'brand' => 'RedTech Publicidad',
            'summary' => 'Letreros para fachadas y locales que hacen visible tu negocio.',
            'description' => 'Diseñamos, fabricamos e instalamos el letrero de tu negocio. Coordinamos contigo la medida y el material.',
            'specs' => "Tipo: según tu proyecto\nInstalación: incluida a coordinar\nDiseño: incluido",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('rotulo'), 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'publicidad', 'slug' => 'adhesivos-vinilos', 'name' => 'Adhesivos y vinilos', 'brand' => 'RedTech Publicidad',
            'summary' => 'Stickers, etiquetas y vinilos para productos, vitrinas y vehículos.',
            'description' => 'Adhesivos impresos y cortados a la forma que necesites, para empaques, vitrinas o vehículos.',
            'specs' => "Material: vinilo adhesivo\nCorte: a la forma\nUso: interior y exterior",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('vinilos'), 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'publicidad', 'slug' => 'diseno-de-logo', 'name' => 'Diseño de logo e identidad', 'brand' => 'RedTech Publicidad',
            'summary' => 'Creamos o renovamos el logo de tu marca, con sus colores y aplicaciones.',
            'description' => "Diseño de logotipo con propuestas y ajustes.\nTe entregamos los archivos listos para imprimir y para redes sociales.",
            'specs' => "Entrega: archivos para impresión y digitales\nIncluye: paleta de colores",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('diseno-logo'), 'gallery' => [], 'featured' => 0,
        ],
    ],
];
