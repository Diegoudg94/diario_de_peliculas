# CineDiario

CineDiario es un diario personal de películas hecho con Laravel y SQLite. Permite registrar películas vistas, calificarlas de 1 a 10 y conservar una reseña. Incluye búsqueda, filtro por género, ordenamiento y un dashboard con estadísticas.

## Objetivo académico

Proyecto final de la Licenciatura en Diseño de Sistemas Web para **Conceptualización de Servicios en Nube**. Demuestra una aplicación web con persistencia en la nube y enlaces a otros servicios, sin depender de APIs privadas. Es una demostración pública con datos de prueba; no incluye cuentas ni debe usarse para información confidencial.

## Tecnologías

PHP 8.3 o superior, Laravel 13, Blade, Eloquent ORM, SQLite, Bootstrap 5 y Bootstrap Icons por CDN, CSS propio y JavaScript nativo. Railway usa Railpack, Caddy y FrankenPHP. No hay proceso de compilación con npm.

## Estructura principal

```text
app/Http/Controllers/PeliculaController.php  CRUD, filtros y estadísticas
app/Models/Pelicula.php                Modelo Eloquent
database/migrations/                  Esquema SQLite
database/seeders/                     Seis ejemplos idempotentes
resources/views/layouts/             Navegación y footer
resources/views/peliculas/            Dashboard, detalle y formularios
public/css/app.css                    Estilos responsivos
public/js/app.js                      Confirmación de eliminación
public/images/poster-placeholder.svg  Portada local predeterminada
routes/web.php                        Rutas resource y /up (Laravel)
Caddyfile, start-container.sh         Servidor de producción
railpack.json, railway.json           Configuración de despliegue
tests/Feature/PeliculaCrudTest.php    Pruebas con SQLite en memoria
```

## Ejecutar localmente en macOS

1. Instala PHP 8.3+ con `pdo_sqlite` y Composer. Comprueba con `php -v`, `php -m | grep pdo_sqlite` y `composer --version`.
2. En la raíz de este proyecto ejecuta:

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan serve
```

Abre <http://127.0.0.1:8000>. El archivo `.env.example` ya selecciona SQLite (`DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`), sesiones y caché en archivos. `php artisan db:seed` es **manual**; ejecutarlo de nuevo no duplica los seis ejemplos. No se ejecuta al iniciar el servidor ni en cada despliegue.

Para ejecutar pruebas sin tocar la base local: `vendor/bin/phpunit`. `phpunit.xml` establece `DB_DATABASE=:memory:` y `RefreshDatabase` crea el esquema dentro del proceso de pruebas. También se puede comprobar la ruta de salud con `curl -i http://127.0.0.1:8000/up`.

## Publicar el código en GitHub

Crea un repositorio vacío en GitHub y sustituye `TU_USUARIO` por tu usuario:

```sh
git init
git add .
git commit -m "Implementar CineDiario"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/cinediario.git
git push -u origin main
```

`.gitignore` excluye `.env`, SQLite local, `vendor/`, logs y configuraciones cacheadas. Revisa `git status --short` antes de subir. `composer.lock` sí debe versionarse para instalar las mismas versiones.

## Desplegar en Railway

1. En Railway crea un proyecto **Deploy from GitHub repo** y selecciona el repositorio. El builder debe ser **Railpack**. `railway.json` configura `/start-container.sh` y el health check `/up`; `railpack.json` conserva el proveedor PHP y el comando de inicio. El script crea los directorios necesarios. No configures un comando de pre-deploy para las migraciones: el volumen se monta al arrancar el contenedor.
2. Adjunta un **Volume** al mismo servicio con **Mount Path `/data`**. Railway proporciona automáticamente `RAILWAY_VOLUME_NAME` y `RAILWAY_VOLUME_MOUNT_PATH`. Los cambios pendientes de Railway, incluidos volumen y variables, deben aplicarse mediante **Deploy**.
3. Añade estas variables al servicio (sin comillas salvo que el panel las requiera):

```dotenv
APP_NAME=CineDiario
APP_ENV=production
APP_DEBUG=false
APP_URL=URL_PUBLICA_DE_RAILWAY
APP_KEY=LLAVE_GENERADA_MANUALMENTE
LOG_CHANNEL=stderr
DB_CONNECTION=sqlite
DB_DATABASE=/data/database.sqlite
CACHE_STORE=file
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
PORT=8080
```

4. Genera **una sola vez** la llave en tu Mac con `php artisan key:generate --show`; copia el resultado completo (empieza por `base64:`) a `APP_KEY` en Railway. Conserva esa misma llave entre despliegues. No la incluyas en Git ni ejecutes `key:generate` en el contenedor.
5. En **Settings → Networking**, genera un dominio público. Copia la URL completa `https://...` a `APP_URL` y usa el puerto **8080** si Railway lo solicita. Aplica los cambios con **Deploy**.
6. Abre el dominio y `/up`. La primera ejecución crea `/data/database.sqlite` si no existe y ejecuta `php artisan migrate --force`; los siguientes arranques conservan los registros. Los datos de ejemplo no se cargan automáticamente en producción. Si deseas demostrarlos una sola vez, ejecuta `php artisan db:seed --force` manualmente en el entorno de producción.

El script detecta Railway, fuerza producción y SQLite, ignora `DB_URL`, comprueba `APP_KEY`, las variables oficiales del volumen, escritura en `/data` y `pdo_sqlite`; luego limpia la configuración cacheada y migra antes de iniciar FrankenPHP. El `Caddyfile` escucha `http://:{$PORT:8080}`, de modo que acepta el encabezado Host del dominio público. Railway termina HTTPS delante del contenedor.

## Solución de errores comunes

| Síntoma | Revisión |
| --- | --- |
| Intenta conectar a MySQL | Comprueba `DB_CONNECTION=sqlite`, elimina un `DB_URL` heredado y vuelve a desplegar. El script ignora `DB_URL` en Railway. |
| Falta tabla `cache` | Usa `CACHE_STORE=file` y `CACHE_DRIVER=file`; el script crea los directorios y ejecuta `config:clear`. |
| No existe `/data` o no permite escritura | Adjunta el volumen al **servicio**, con Mount Path `/data`; aplica **Deploy**. Comprueba permisos del usuario del contenedor; si hace falta, configura `RAILWAY_RUN_UID=0`. |
| HTTP 400 | Verifica que se esté usando el `Caddyfile` incluido, que el dominio público apunte al servicio y que `APP_URL` sea la URL pública. |
| HTTP 502 | Inspecciona los logs de arranque: falta de `APP_KEY`, volumen, `pdo_sqlite` o migración fallida. Verifica `PORT=8080` y `/up`. |
| Cambios perdidos | Comprueba que `DB_DATABASE=/data/database.sqlite` y que el volumen siga adjunto al mismo servicio. Nunca uses `migrate:fresh` en producción. |
| Página sin estilos | Bootstrap e Icons se cargan por CDN; el navegador necesita conexión a Internet. El CSS de CineDiario se sirve localmente. |

## Servicios en la nube

En el detalle, el título se codifica con `rawurlencode` para abrir búsquedas de **IMDb**, **YouTube** (tráiler oficial) y **JustWatch México**. Los enlaces usan `https`, abren en otra pestaña y tienen `rel="noopener noreferrer"`. El footer enlaza además a **GitHub** y **Railway**. No se usan APIs externas ni scraping; la disponibilidad real de una película depende de JustWatch.

## Seguridad y límites

Laravel valida los campos en el servidor, Eloquent parametriza las consultas, Blade escapa reseñas, los formularios usan CSRF y las portadas aceptan únicamente URL `http` o `https`. La eliminación requiere confirmación. No hay autenticación: cualquier visitante del enlace público puede crear, editar y borrar datos de prueba. SQLite con un volumen se plantea para una instancia sencilla de demostración, no para varios contenedores escribiendo simultáneamente.
