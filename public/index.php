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
require __DIR__ . '/../src/Core/Security.php';

// Controllers
require __DIR__ . '/../src/Controllers/HomeController.php';
require __DIR__ . '/../src/Controllers/AuthController.php';
require __DIR__ . '/../src/Controllers/CovoiturageController.php';
require __DIR__ . '/../src/Controllers/StaticController.php';
require __DIR__ . '/../src/Controllers/AccountController.php';
require __DIR__ . '/../src/Controllers/EmployeController.php';
require __DIR__ . '/../src/Controllers/AdminController.php';

// Repositories
require __DIR__ . '/../src/Repositories/UtilisateurRepository.php';
require __DIR__ . '/../src/Repositories/CovoiturageRepository.php';
require __DIR__ . '/../src/Repositories/AdminRepository.php';

/*
|--------------------------------------------------------------------------
| Définition des routes
|--------------------------------------------------------------------------
*/
$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/covoiturages', [CovoiturageController::class, 'list']);
$router->get('/covoiturage', [CovoiturageController::class, 'detail']); // ?id=...

$router->get('/covoiturage/participer', [CovoiturageController::class, 'participerConfirm']); // ?id=...
$router->post('/covoiturage/participer', [CovoiturageController::class, 'participer']); // POST id_covoiturage

$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/contact', [StaticController::class, 'contact']);
$router->get('/mentions-legales', [StaticController::class, 'mentionsLegales']);

$router->get('/account', [AccountController::class, 'index']);
$router->post('/account/role', [AccountController::class, 'updateDriverRole']);

$router->get('/account/vehicles', [AccountController::class, 'vehicles']);
$router->post('/account/vehicles', [AccountController::class, 'addVehicle']);

$router->get('/account/trips/new', [AccountController::class, 'newTrip']);
$router->post('/account/trips/new', [AccountController::class, 'createTrip']);

$router->get('/account/history', [AccountController::class, 'history']);
$router->post('/account/history/cancel-participation', [AccountController::class, 'cancelParticipation']);
$router->post('/account/history/cancel-trip', [AccountController::class, 'cancelTripAsDriver']);

// US11 Chauffeur : démarrer / terminer
$router->post('/covoiturage/start', [CovoiturageController::class, 'startTrip']);
$router->post('/covoiturage/finish', [CovoiturageController::class, 'finishTrip']);

// US11 Passager : valider / incident
$router->get('/account/validate-trips', [AccountController::class, 'validateTrips']);
$router->post('/account/validate-trips', [AccountController::class, 'submitTripValidation']);

// US12 Employé : incidents / validation / refus
$router->get('/employe/incidents', [EmployeController::class, 'incidents']);
$router->post('/employe/incidents/validate', [EmployeController::class, 'validate']);
$router->post('/employe/incidents/refuse', [EmployeController::class, 'refuse']);

// US13 Admin
$router->get('/admin', [AdminController::class, 'dashboard']);

$router->get('/admin/employees', [AdminController::class, 'employees']);
$router->post('/admin/employees/create', [AdminController::class, 'createEmployee']);

$router->post('/admin/users/suspend', [AdminController::class, 'suspendUser']);
$router->post('/admin/users/unsuspend', [AdminController::class, 'unsuspendUser']);

/*
|--------------------------------------------------------------------------
| Normalisation du chemin (indispensable sous /EcoRide/public)
|--------------------------------------------------------------------------
*/
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

// BASE_URL ex: /EcoRide/public
define('BASE_URL', $basePath === '' ? '' : $basePath);

$path = $uriPath;

// Retirer le basePath du début
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

if ($path === '' || $path === false) {
    $path = '/';
}

// Blocage global si l'utilisateur en session est suspendu
if (isset($_SESSION['user'])) {
    $userRepo = new UtilisateurRepository();
    $id = (int)$_SESSION['user']['id_utilisateur'];
    if ($userRepo->isSuspended($id)) {
        unset($_SESSION['user']);
        $_SESSION['flash_error'] = "Votre compte est suspendu.";
    }
}

/*
|--------------------------------------------------------------------------
| Dispatch
|--------------------------------------------------------------------------
*/
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// echo "<pre>METHOD=$method\nPATH=$path\n</pre>";
//exit;

try {
    $router->dispatch($method, $path);
} catch (Throwable $e) {
    //http_response_code(404);
    //echo "404 - Page non trouvée";
    http_response_code(500);
    echo "<h1>Erreur (debug)</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getFile() . ":" . $e->getLine()) . "</pre>";
}
