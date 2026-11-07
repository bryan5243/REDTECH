# RedTech

Sitio web y tienda de RedTech en PHP puro y MySQL:

- **Inicio** (`public/index.php`): servicios, equipos destacados, proceso, sectores, preguntas frecuentes y formulario de cotización.
- **Tienda** (`public/tienda.php`, `public/producto.php`): catálogo con categorías, búsqueda, orden y ficha de producto.
- **Carrito por WhatsApp**: el cliente arma su pedido y lo envía por WhatsApp con un mensaje ya escrito. El pedido también queda guardado en el panel.
- **Panel de administración** (`public/admin/`): productos, categorías, pedidos, cotizaciones, teléfonos, correos, cifras, fotos y número de WhatsApp.

## Ver el sitio en tu computadora

```bash
php -S localhost:8000 -t public
```

Abre http://localhost:8000. Sin MySQL, el sitio muestra el catálogo de ejemplo de `config/catalog.php` y los datos de `config/defaults.php`, pero no guarda cotizaciones ni pedidos y el panel no funciona.

## Conectar MySQL

1. Crea la base de datos y su usuario (en cPanel: **Bases de datos MySQL**).
2. Copia `config/config.example.php` como `config/config.php` y completa los datos de MySQL.
3. En el mismo archivo, escribe una clave larga en `admin_setup_key`.
4. Ejecuta el instalador (en cPanel puedes usar **Terminal**):

```bash
php database/install.php
```

El instalador crea las tablas y carga el catálogo de ejemplo. Se puede repetir sin borrar tus datos.

**Sin terminal** (por ejemplo, en un hosting gratuito): abre `/admin/`. Si la conexión funciona pero faltan las tablas, el panel te pide la clave `admin_setup_key` y las crea.

## Panel de administración

1. Entra a `/admin/`.
2. La primera vez te pedirá la clave `admin_setup_key`, un usuario y una contraseña de al menos 10 caracteres.
3. Ya con tu usuario, borra `admin_setup_key` de `config/config.php`.

Desde el panel puedes:

| Sección | Qué haces |
|---|---|
| Productos | Crear, editar, ocultar, destacar y eliminar productos; subir fotos y galería; precio (vacío = «precio a consultar») y disponibilidad. |
| Categorías | Crear y ordenar categorías de la tienda. |
| Pedidos | Ver los pedidos enviados por WhatsApp y marcarlos como atendidos. |
| Cotizaciones | Ver las solicitudes del formulario y responder por WhatsApp. |
| Datos del sitio | Logo (normal y para fondo oscuro), colores de la marca, número de WhatsApp, teléfonos, correos, ubicación, horario, redes sociales, cifras, fotos del sitio y tu contraseña. |

Lo que no marques como **confirmado** aparece con la etiqueta «por confirmar» y sin enlaces. El botón flotante y el carrito de WhatsApp funcionan cuando hay un número de WhatsApp o un teléfono confirmado.

**Antes de publicar:** carga tus productos reales, revisa los textos de `src/content.php` y desmarca el aviso «Catálogo de demostración» en Datos del sitio.

## Publicar en cPanel

1. Copia el contenido de `public/` en `public_html/` (incluida la carpeta `uploads/` con su `.htaccess`).
2. Copia `config/`, `src/` y `database/` junto a `public_html/`, fuera de esa carpeta.
3. Crea `config/config.php` en el servidor y ejecuta el instalador.
4. Comprueba que `public_html/uploads/` tenga permisos de escritura para subir fotos.

## Publicar en un hosting gratuito (InfinityFree u otro con PHP y MySQL)

1. Crea la cuenta y el sitio. Te dan un subdominio gratis, por ejemplo `redtech.infinityfreeapp.com`.
2. En el panel, abre **MySQL Databases** y crea una base. Anota el host (por ejemplo `sql123.infinityfree.com`), el nombre, el usuario y la contraseña.
3. Sube **todo el proyecto** dentro de `htdocs/` con el administrador de archivos o por FTP (FileZilla). El `.htaccess` de la raíz sirve `public/` como página principal y bloquea `config/`, `src/` y `database/`.
4. En `htdocs/config/`, copia `config.example.php` como `config.php` y escribe los datos de MySQL y una `admin_setup_key`.
5. Abre `https://tu-subdominio/admin/`, escribe la clave para instalar la base de datos y luego crea tu usuario.

## Seguridad

- Contraseñas con `password_hash`, sesión que caduca a las 2 horas y bloqueo de 15 minutos tras 5 intentos fallidos.
- Todos los formularios llevan token CSRF y las consultas usan sentencias preparadas.
- Las fotos subidas se validan como imagen real (JPG, PNG, WEBP o GIF, máximo 4 MB), se guardan con nombre aleatorio y `uploads/.htaccess` impide ejecutar código ahí.
- Los totales de los pedidos se calculan en el servidor con los precios del catálogo.

## Fuentes e imágenes

Las fuentes Sora e Inter están en `public/assets/fonts/` (licencia SIL Open Font License). Las fotos de ejemplo vienen de Unsplash: ver `CREDITS.md`.
