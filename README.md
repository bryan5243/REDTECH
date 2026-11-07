# RedTech

Sitio web de RedTech en PHP puro y MySQL. Por ahora incluye la página de inicio con su formulario de cotización.

## Ver el sitio en local

```bash
php -S localhost:8000 -t public
```

Abre http://localhost:8000. Sin MySQL, el sitio muestra los datos de `config/defaults.php`, pero el formulario no puede guardar solicitudes.

## Conectar MySQL

1. Crea la base de datos y su usuario.
2. Copia `config/config.example.php` como `config/config.php` y completa los datos.
3. Ejecuta el instalador:

```bash
php database/install.php
```

## Editar teléfonos, correos y cifras

Hasta que exista el panel de administración, estos datos se editan en MySQL (por ejemplo, con phpMyAdmin):

| Dato | Tabla |
|---|---|
| Teléfonos y correos | `contacts` |
| Cifras de la empresa | `stats` |
| Ubicación | `settings` |

Cada fila tiene una columna `confirmed`. Con `0`, el sitio muestra la marca "por confirmar" y no crea enlaces de llamada ni de correo; con `1`, publica el dato normalmente. Usa `active = 0` para ocultar una fila de `contacts` o `stats`.

Las solicitudes de cotización se guardan en la tabla `quote_requests`.

## Publicar en cPanel

1. Copia el contenido de `public/` en `public_html/`.
2. Copia `config/`, `src/` y `database/` junto a `public_html/`, fuera de esa carpeta.
3. Crea `config/config.php` en el servidor y ejecuta el instalador por SSH.
