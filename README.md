# CineDiario

CineDiario es un diario de películas con registro, consulta, edición y eliminación de reseñas y calificaciones. El dashboard muestra estadísticas, búsqueda por título, filtro por género y ordenamiento. Es el proyecto final de **Conceptualización de Servicios en Nube** de la Licenciatura en Diseño de Sistemas Web.

## Tecnologías y servicios

- PHP 8.3+, Laravel 13, Blade y Eloquent ORM.
- SQLite para desarrollo local y pruebas automatizadas; PostgreSQL de Neon para producción.
- Bootstrap 5 y Bootstrap Icons por CDN, CSS propio y JavaScript nativo.
- GitHub para el código y Render (Docker, Apache) para la aplicación pública.
- Enlaces de búsqueda a IMDb, YouTube y JustWatch México, sin API ni credenciales externas.

## Estructura principal

```text
app/Http/Controllers/PeliculaController.php   CRUD, filtros y estadísticas
app/Models/Pelicula.php                     Modelo Eloquent
app/Providers/AppServiceProvider.php        Enlaces HTTPS en producción
config/database.php                         SQLite local y PostgreSQL de Neon
database/migrations/                       Tabla de películas
database/seeders/                          Seis ejemplos idempotentes
resources/views/                           Vistas Blade y footer de servicios
public/css/, public/js/, public/images/    Interfaz y portada predeterminada
public/.htaccess                           Reescritura de rutas con Apache
Dockerfile, docker/apache-vhost.conf        Imagen y sitio Apache
start-render.sh                            Arranque y migraciones en Render
tests/Feature/PeliculaCrudTest.php         Pruebas con SQLite en memoria
```

## Desarrollo local en macOS

Requiere PHP 8.3 o superior, Composer y `pdo_sqlite`. Comprueba `php -v`, `composer --version` y `php -m | grep pdo_sqlite`. Desde la raíz:

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan serve
```

Abre <http://127.0.0.1:8000>. `.env.example` usa SQLite; no hace falta Neon para el desarrollo. El seeder es manual e idempotente: una segunda ejecución no duplica las seis películas. No se ejecuta al reiniciar ni durante los despliegues.

Pruebas: `vendor/bin/phpunit`. `phpunit.xml` configura SQLite en memoria y `RefreshDatabase`, por lo que las pruebas no tocan `database/database.sqlite`. La ruta de salud de Laravel está en `/up`.

## Despliegue en Render con Neon

1. En Neon crea un proyecto PostgreSQL y copia su cadena de conexión desde el panel. Conserva la cadena fuera del repositorio. Debe usar `postgres://` o `postgresql://` y SSL (`sslmode=require` o una modalidad de verificación más fuerte). También puedes configurar `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` por separado en Render.
2. En Render crea un **Web Service** conectado al repositorio [Diegoudg94/diario_de_peliculas](https://github.com/Diegoudg94/diario_de_peliculas) y selecciona **Docker** como runtime. El Dockerfile sirve únicamente `public/` con Apache, escucha en `0.0.0.0:$PORT` y expone 10000 por defecto. No requiere disco persistente de Render: los registros viven en Neon.
3. Configura estas variables en **Environment** del servicio. Sustituye los valores de ejemplo únicamente en el panel de Render:

```dotenv
APP_NAME=CineDiario
APP_ENV=production
APP_DEBUG=false
APP_KEY=GENERAR_MANUALMENTE
APP_URL=URL_PUBLICA_DE_RENDER
LOG_CHANNEL=stderr
DB_CONNECTION=pgsql
DB_URL=CADENA_DE_CONEXION_DE_NEON
DB_SSLMODE=require
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
PORT=10000
```

   `APP_URL` debe ser la URL completa **HTTPS** del servicio, por ejemplo `https://nombre.onrender.com`. Si Render asigna el dominio después de crear el servicio, añade la URL y vuelve a desplegar. Para `APP_KEY`, ejecuta una sola vez `php artisan key:generate --show` en tu equipo y copia el valor `base64:...` al panel. **No regeneres esa llave durante los despliegues.** `DB_URL` se puede omitir si se usan todas las variables estándar `DB_HOST`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` (y opcionalmente `DB_PORT=5432`).
4. Configura **Health Check Path** como `/up`. Publica los cambios del repositorio cuando estés listo y despliega el servicio. Render gestiona HTTPS y el dominio; Apache no crea certificados dentro del contenedor.
5. En cada arranque, `start-render.sh` comprueba la configuración, fuerza producción y SSL, crea directorios con permisos, ejecuta `php artisan config:clear` y `php artisan migrate --force`, y arranca Apache en primer plano. Las migraciones no se ejecutan al construir la imagen. El script no usa `migrate:fresh`, no elimina registros y no ejecuta seeders.

Laravel lee `DB_URL` en la conexión `pgsql`; si no existe, usa las variables estándar. `DB_SSLMODE=require` es el valor predeterminado de PostgreSQL y el arranque rechaza una URL que solicite SSL desactivado. SQLite sigue siendo la conexión local predeterminada. La búsqueda usa `whereLike`, que conserva el comportamiento sin distinguir mayúsculas en SQLite y PostgreSQL.

## GitHub

El repositorio remoto ya existe. Para preparar los cambios sin publicarlos todavía:

```sh
git status --short
git add .
git commit -m "Preparar despliegue en Render con Neon"
```

Cuando se autorice la publicación, `git push origin main`. `.gitignore` y `.dockerignore` excluyen `.env`, bases SQLite locales, dependencias instaladas, logs y cachés. `composer.lock` sí se versiona.

## Problemas comunes

| Síntoma | Qué revisar |
| --- | --- |
| Error de conexión PostgreSQL | `DB_URL` de Neon en el panel de Render, usuario, base, host y acceso de red. No pongas credenciales en Git. |
| Error SSL | La URL no debe tener `sslmode=disable` ni `prefer`. El arranque exige `DB_SSLMODE=require`; usa una URL de Neon con SSL. |
| Error 502 o puerto no detectado | Revisa logs de arranque, `PORT=10000`, `APP_KEY`, `APP_URL` y conexión a Neon. Apache escucha en `0.0.0.0`. |
| Error 404 en rutas | Comprueba que Apache sirva `public/`, `mod_rewrite` esté habilitado y `public/.htaccess` exista. |
| Contenido mixto HTTP/HTTPS | Usa `APP_URL=https://...`; la aplicación genera enlaces HTTPS en producción. |
| Falta tabla `cache` | Usa `CACHE_STORE=file`; el script crea los directorios y limpia la configuración cacheada. |
| Los datos no aparecen | La base de desarrollo SQLite y la de producción Neon son independientes. No se copian registros ni se cargan ejemplos automáticamente. |

## Seguridad y límites

Laravel valida formularios y protege CSRF; Eloquent parametriza consultas y Blade escapa reseñas. Las portadas solo aceptan URL HTTP o HTTPS. Los enlaces externos abren con `rel="noopener noreferrer"`. Es una demostración pública sin autenticación, destinada únicamente a datos de prueba: cualquier visitante puede crear, editar o borrar registros. Los archivos de sesiones y caché de Render son temporales; los registros de películas persisten en Neon.
