# Stock de Muestras — PHP + SQLite

Aplicación para gestión de muestras de laboratorio, desarrollada en PHP puro con SQLite como base de datos.

## Requisitos

- PHP 7.4 o superior con extensiones: `pdo`, `pdo_sqlite`
- Apache con `mod_rewrite` habilitado (o servidor con soporte de URL rewriting)

## Estructura de archivos

```
stockmuestras/
├── index.php           ← Frontend (UI completa, SPA)
├── init_db.php         ← Inicialización de la base de datos
├── muestras.db         ← Base de datos SQLite (se crea al inicializar)
├── .htaccess           ← Reglas de routing para Apache
├── README.md
└── api/
    ├── index.php       ← Router REST
    ├── db.php          ← Clase de conexión a la base de datos
    └── muestrasController.php ← Lógica CRUD
```

## Instalación y puesta en marcha

### 1. Copiar archivos al servidor web

Copiar toda la carpeta al directorio raíz del servidor web (ej: `/var/www/html/stockmuestras/` o `htdocs/stockmuestras/`).

### 2. Inicializar la base de datos

Acceder una única vez a:
```
http://localhost/stockmuestras/init_db.php
```
Esto crea la tabla `muestras` en `muestras.db`.

> ⚠️ Eliminar o proteger `init_db.php` después de usarlo, ya que elimina todos los datos si se ejecuta nuevamente.

### 3. Usar la aplicación

Acceder a:
```
http://localhost/stockmuestras/
```

## Endpoints de la API

| Método | Endpoint                        | Descripción           |
|--------|---------------------------------|-----------------------|
| GET    | `/api/muestras`                 | Listar todas          |
| GET    | `/api/muestras/:id`             | Obtener una           |
| POST   | `/api/muestras`                 | Crear nueva           |
| PUT    | `/api/muestras/:id`             | Actualizar            |
| DELETE | `/api/muestras/:id`             | Eliminar              |
| GET    | `/api/muestras/:id/qr`          | Datos para QR         |

## Prueba sin Apache (PHP built-in server)

```bash
cd stockmuestras
php init_db.php
php -S localhost:8080
```

Luego acceder a `http://localhost:8080`

> Nota: Con el servidor built-in de PHP el routing del `.htaccess` no aplica.  
> Usar en su lugar: `php -S localhost:8080 router.php` (ver abajo).

### router.php para desarrollo con PHP built-in

```php
<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($uri, '/api/') === 0) {
    $_SERVER['REQUEST_URI'] = str_replace('/api', '/api/index.php', $_SERVER['REQUEST_URI']);
    require __DIR__ . '/api/index.php';
} else {
    require __DIR__ . '/index.php';
}
```
