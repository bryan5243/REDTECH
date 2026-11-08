<?php
// Restaurante ficticio para las visitas de venta. La instalación lo copia a la base de datos
// una sola vez; después todo se edita desde el panel (/admin).
// Nombres, precios, teléfonos y la cuenta bancaria son de EJEMPLO.

return [
    'settings' => [
        // Identidad
        'name' => 'Marisquería La Gaviota',
        'tagline' => 'Mariscos frescos de Puerto Jelí',
        'hero_title' => 'Del manglar a tu mesa',
        'hero_text' => 'Ceviches, encocados y arroces marineros con la pesca del día. Pide desde tu mesa con el código QR o llévalo a casa.',
        'hero_image' => '',
        'logo' => '',
        'logo_dark' => '',
        'color_primary' => '',
        'color_accent' => '',
        'color_dark' => '',
        // Contacto y ubicación
        'address' => 'Malecón de Puerto Jelí, Santa Rosa, El Oro',
        'map_embed' => 'Puerto Jelí, Santa Rosa, El Oro, Ecuador',
        'phone' => '07 000 0000',
        'instagram' => '',
        'facebook' => '',
        'tiktok' => '',
        // Horario: días 1 (lunes) a 7 (domingo)
        'schedule_text' => 'Martes a domingo, 10:00 a 18:00',
        'open_days' => '1,2,3,4,5,6,7',
        'open_from' => '00:00',
        'open_to' => '23:59',
        // Pedidos
        'orders_enabled' => '1',
        'orders_follow_hours' => '0',
        'mode_mesa' => '1',
        'mode_llevar' => '1',
        'mode_domicilio' => '1',
        'tables' => '12',
        'delivery_fee' => '1.50',
        'delivery_note' => 'Entregas en Santa Rosa y Puerto Jelí. Tiempo aproximado: 40 minutos.',
        'default_eta' => '20',
        // Pagos (para llevar y a domicilio)
        'pay_cash' => '1',
        'pay_transfer' => '1',
        'bank_name' => 'Banco de ejemplo',
        'bank_type' => 'Cuenta de ahorros',
        'bank_number' => '1234567890',
        'bank_holder' => 'Marisquería La Gaviota (demo)',
        'bank_id' => '0700000000001',
        'pay_other' => '',
        // WhatsApp: opcional. Los pedidos llegan a la pantalla de cocina, no al WhatsApp.
        'whatsapp' => '',
        'whatsapp_orders' => '0',
        'whatsapp_reservations' => '0',
        // Reservas
        'reservations_enabled' => '1',
        'reservation_note' => 'Te confirmamos la reserva por llamada o mensaje.',
        'max_party' => '20',
        // Dirección pública para los QR (ej. https://lagaviota.com). Vacío: se calcula sola.
        'site_url' => '',
        'demo_notice' => '1',
    ],
    // kind: dibujo de respaldo cuando un plato no tiene foto (entradas, ceviches, platos, bebidas, postres)
    'categories' => [
        ['slug' => 'entradas', 'name' => 'Entradas', 'kind' => 'entradas', 'description' => 'Para abrir el apetito mientras llega lo bueno.'],
        ['slug' => 'ceviches', 'name' => 'Ceviches', 'kind' => 'ceviches', 'description' => 'Con limón, cebolla, tomate y culantro, como se hacen en la costa.'],
        ['slug' => 'platos-fuertes', 'name' => 'Platos fuertes', 'kind' => 'platos', 'description' => 'Servidos con arroz, patacones o maduro, según el plato.'],
        ['slug' => 'bebidas', 'name' => 'Bebidas', 'kind' => 'bebidas', 'description' => 'Jugos naturales del día, bien fríos.'],
    ],
    'dishes' => [
        ['category' => 'entradas', 'name' => 'Patacones con queso', 'price' => 3.50, 'description' => 'Seis patacones crocantes con queso criollo y salsa de ajo.', 'badge' => ''],
        ['category' => 'entradas', 'name' => 'Bolón mixto', 'price' => 3.00, 'description' => 'Verde majado con queso y chicharrón, con huevo frito.', 'badge' => ''],
        ['category' => 'entradas', 'name' => 'Empanadas de camarón', 'price' => 4.50, 'description' => 'Tres empanadas de verde rellenas de camarón al ajillo.', 'badge' => 'Nuevo'],
        ['category' => 'entradas', 'name' => 'Calamar apanado', 'price' => 7.50, 'description' => 'Anillos de calamar dorados con salsa tártara y limón.', 'badge' => '', 'available' => 0],
        ['category' => 'ceviches', 'name' => 'Ceviche de camarón', 'price' => 8.00, 'description' => 'Camarón de la zona en jugo de limón y tomate, con chifles y canguil.', 'badge' => 'Más pedido', 'featured' => 1],
        ['category' => 'ceviches', 'name' => 'Ceviche de concha negra', 'price' => 10.00, 'description' => 'La especialidad de Jelí: concha prieta del manglar, cebolla morada y limón.', 'badge' => 'De la casa', 'featured' => 1],
        ['category' => 'ceviches', 'name' => 'Ceviche mixto', 'price' => 10.50, 'description' => 'Camarón, concha, pescado y calamar en un solo plato.', 'badge' => ''],
        ['category' => 'ceviches', 'name' => 'Ceviche de pescado', 'price' => 7.50, 'description' => 'Pescado blanco del día marinado al estilo peruano-orense.', 'badge' => ''],
        ['category' => 'platos-fuertes', 'name' => 'Arroz marinero', 'price' => 12.00, 'description' => 'Arroz amarillo con camarón, concha, calamar y pescado, con patacones.', 'badge' => 'Recomendado', 'featured' => 1],
        ['category' => 'platos-fuertes', 'name' => 'Encocado de camarón', 'price' => 11.00, 'description' => 'Camarones en salsa de coco y refrito, con arroz y maduro frito.', 'badge' => '', 'featured' => 1],
        ['category' => 'platos-fuertes', 'name' => 'Pescado frito entero', 'price' => 12.50, 'description' => 'Pargo o corvina del día, con arroz, ensalada y patacones.', 'badge' => ''],
        ['category' => 'platos-fuertes', 'name' => 'Cazuela de mariscos', 'price' => 11.50, 'description' => 'Guiso espeso de verde y maní con mariscos, al horno de barro.', 'badge' => 'Picante opcional'],
        ['category' => 'platos-fuertes', 'name' => 'Encebollado', 'price' => 5.00, 'description' => 'Caldo de albacora con yuca y cebolla curtida, con chifles.', 'badge' => ''],
        ['category' => 'platos-fuertes', 'name' => 'Parrillada de mariscos para 2', 'price' => 28.00, 'description' => 'Langostinos, pulpo, calamar, pescado y conchas asadas, con dos acompañantes.', 'badge' => 'Para compartir', 'featured' => 1],
        ['category' => 'bebidas', 'name' => 'Jugo de maracuyá', 'price' => 2.00, 'description' => 'Natural, con o sin azúcar.', 'badge' => ''],
        ['category' => 'bebidas', 'name' => 'Limonada imperial', 'price' => 2.50, 'description' => 'Limón, hierbabuena y hielo frappé.', 'badge' => ''],
        ['category' => 'bebidas', 'name' => 'Coco helado', 'price' => 2.50, 'description' => 'Agua de coco servida en el mismo coco.', 'badge' => ''],
        ['category' => 'bebidas', 'name' => 'Cerveza nacional 600 ml', 'price' => 3.00, 'description' => 'Bien fría. Solo para mayores de 18 años.', 'badge' => ''],
        ['category' => 'bebidas', 'name' => 'Gaseosa personal', 'price' => 1.25, 'description' => 'Cola, naranja o limón.', 'badge' => ''],
    ],
];
