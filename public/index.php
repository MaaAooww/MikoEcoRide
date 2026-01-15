<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

// URI demandé (ex: /EcoRide/public/ ou /EcoRide/public/home)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Base path réel (ex: /EcoRide/public)
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// On retire le basePath du chemin demandé
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Normalisation
if ($path === '' || $path === false) $path = '/';

// Router très simple
switch ($path) {
    case '/':
    case '/home':
        echo "EcoRide - Home (OK)";
        break;

    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}
