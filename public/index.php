<?php
declare(strict_types=1);

session_start();

/*
|--------------------------------------------------------------------------
| Chargement des dépendances
|--------------------------------------------------------------------------
*/
require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Core/Router.php';

// Controllers
require __DIR__ . '/../src/Controllers/HomeController.php';
require __DIR__ . '/../src/Controllers/AuthController.php';
require __DIR__ . '/../src/Controllers/CovoiturageController.php';

// Repositories
require __DIR__ . '/../src/Repositories/UtilisateurRepository.php';
require __DIR__ . '/../src/Repositories/CovoiturageRepository.php';

/*
|--------------------------------------------------------------------------
| Définition des routes
|--------------------------------------------------------------------------
*/
$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/covoiturages', [CovoiturageController::class, 'list']);
$router->get('/covoiturage', [CovoiturageController::class, 'detail']); // ?id=...

$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Normalisation du chemin (indispensable sous /EcoRide/public)
|--------------------------------------------------------------------------
*/

// URI demandée (ex: /EcoRide/public/covoiturages)
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// SCRIPT_NAME (ex: /EcoRide/public/index.php)
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

// Base path réel (ex: /EcoRide/public)
$basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');
// BASE_URL (ex: /EcoRide/public)
define('BASE_URL', $basePath === '' ? '' : $basePath);

$path = $uriPath;

// On retire le basePath du début de l’URL
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Cas racine
if ($path === '' || $path === false) {
    $path = '/';
}

/*
|--------------------------------------------------------------------------
| Dispatch (appel du contrôleur)
|--------------------------------------------------------------------------
*/
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
