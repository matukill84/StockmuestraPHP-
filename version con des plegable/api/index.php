<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/muestrasController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip base path - support both /api/ and direct calls
$uri = preg_replace('#^.*/api#', '', $uri);
$uri = trim($uri, '/');
$parts = explode('/', $uri);

// parts[0] = 'muestras', parts[1] = id (optional), parts[2] = 'qr' (optional)
if (($parts[0] ?? '') !== 'muestras') {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

$controller = new MuestrasController();
$id = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
$action = $parts[2] ?? null;

$body = [];
if (in_array($method, ['POST', 'PUT'])) {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? [];
}

if ($action === 'qr' && $id !== null && $method === 'GET') {
    $controller->generateQR($id);
} elseif ($id === null && $method === 'GET') {
    $controller->getAll();
} elseif ($id !== null && $method === 'GET') {
    $controller->getById($id);
} elseif ($id === null && $method === 'POST') {
    $controller->create($body);
} elseif ($id !== null && $method === 'PUT') {
    $controller->update($id, $body);
} elseif ($id !== null && $method === 'DELETE') {
    $controller->delete($id);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
