<?php
// Catálogo inicial de EJEMPLO. `database/install.php` lo copia a MySQL una vez;
// después se administra desde /admin. Precios, nombres y fotos son ilustrativos:
// reemplázalos por tus productos reales antes de publicar.

// Ilustraciones locales (reemplázalas por fotos de tus trabajos).
$ad = static fn (string $name): string => 'assets/img/publicidad/' . $name . '.svg';

return [
    'categories' => [
        ['slug' => 'impresion', 'name' => 'Impresión', 'description' => 'Gigantografías, lonas, adhesivos, tarjetas y volantes.', 'image' => $ad('gigantografia')],
        ['slug' => 'rotulacion', 'name' => 'Rotulación y letreros', 'description' => 'Letreros para locales, vitrinas y vehículos.', 'image' => $ad('rotulo')],
        ['slug' => 'diseno', 'name' => 'Diseño gráfico', 'description' => 'Logos, identidad de marca y artes para redes.', 'image' => $ad('diseno-logo')],
    ],
    'products' => [
        [
            'category' => 'impresion', 'slug' => 'tarjetas-de-presentacion', 'name' => 'Tarjetas de presentación', 'brand' => 'RedTech Publicidad',
            'summary' => 'Tarjetas impresas a full color con tu diseño o con el que te hacemos.',
            'description' => "Tarjetas de presentación para tu negocio.\nSi no tienes diseño, te lo preparamos y te enviamos una prueba antes de imprimir.",
            'specs' => "Impresión: full color\nLados: uno o dos\nCantidad: según pedido\nDiseño: opcional",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('tarjetas'), 'gallery' => [], 'featured' => 1,
        ],
        [
            'category' => 'impresion', 'slug' => 'gigantografia-lona', 'name' => 'Gigantografía en lona', 'brand' => 'RedTech Publicidad',
            'summary' => 'Lonas publicitarias para fachadas, eventos y promociones.',
            'description' => 'Impresión en gran formato en lona, lista para colgar o montar en estructura. Tú eliges la medida.',
            'specs' => "Material: lona\nMedida: a tu gusto\nAcabado: con ojales o para estructura\nUso: interior y exterior",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('gigantografia'), 'gallery' => [], 'featured' => 1,
        ],
        [
            'category' => 'rotulacion', 'slug' => 'rotulo-letrero', 'name' => 'Rótulo o letrero para tu local', 'brand' => 'RedTech Publicidad',
            'summary' => 'Letreros para fachadas y locales que hacen visible tu negocio.',
            'description' => 'Diseñamos, fabricamos e instalamos el letrero de tu negocio. Coordinamos contigo la medida y el material.',
            'specs' => "Tipo: según tu proyecto\nInstalación: incluida a coordinar\nDiseño: incluido",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('rotulo'), 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'impresion', 'slug' => 'adhesivos-vinilos', 'name' => 'Adhesivos y vinilos', 'brand' => 'RedTech Publicidad',
            'summary' => 'Stickers, etiquetas y vinilos para productos, vitrinas y vehículos.',
            'description' => 'Adhesivos impresos y cortados a la forma que necesites, para empaques, vitrinas o vehículos.',
            'specs' => "Material: vinilo adhesivo\nCorte: a la forma\nUso: interior y exterior",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('vinilos'), 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'diseno', 'slug' => 'diseno-de-logo', 'name' => 'Diseño de logo e identidad', 'brand' => 'RedTech Publicidad',
            'summary' => 'Creamos o renovamos el logo de tu marca, con sus colores y aplicaciones.',
            'description' => "Diseño de logotipo con propuestas y ajustes.\nTe entregamos los archivos listos para imprimir y para redes sociales.",
            'specs' => "Entrega: archivos para impresión y digitales\nIncluye: paleta de colores",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('diseno-logo'), 'gallery' => [], 'featured' => 0,
        ],
        [
            'category' => 'impresion', 'slug' => 'volantes', 'name' => 'Volantes publicitarios', 'brand' => 'RedTech Publicidad',
            'summary' => 'Volantes a full color para tus promociones y eventos.',
            'description' => 'Volantes impresos para repartir en tu local, en eventos o puerta a puerta. Te ayudamos con el diseño.',
            'specs' => "Impresión: full color\nTamaño: según pedido\nCantidad: según pedido",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('portada'), 'gallery' => [], 'featured' => 1,
        ],
        [
            'category' => 'diseno', 'slug' => 'artes-redes-sociales', 'name' => 'Artes para redes sociales', 'brand' => 'RedTech Publicidad',
            'summary' => 'Publicaciones y portadas con la imagen de tu marca.',
            'description' => 'Diseñamos publicaciones, historias y portadas para Facebook, Instagram y WhatsApp con tu logo y tus colores.',
            'specs' => "Formatos: publicación, historia y portada\nEntrega: archivos listos para publicar",
            'price' => null, 'stock' => 'bajo_pedido', 'image' => $ad('vinilos'), 'gallery' => [], 'featured' => 0,
        ],
    ],
];
