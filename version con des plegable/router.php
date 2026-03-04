<?php
// Router para desarrollo con: php -S localhost:8080 router.php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($uri, '/api/') === 0 || $uri === '/api') {
    // Strip /api prefix and pass to API router
    $_SERVER['PATH_INFO'] = substr($uri, 4) ?: '/';
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 4) ?: '/';
    require __DIR__ . '/api/index.php';
} elseif ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
} elseif (file_exists(__DIR__ . $uri)) {
    return false; // Serve static file
} else {
    require __DIR__ . '/index.php';
}
