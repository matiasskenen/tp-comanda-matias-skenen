<?php

/*

php -S localhost:666 -t app

*/

require_once '../vendor/autoload.php';
//require_once "../middlewares/authMid.php";

//rutas
require_once './controllers/UsuarioController.php';
require_once './controllers/MesasController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/OrdenController.php';


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
require_once '../db/AccesoDatos.php';
require_once "../db/conectarDB.php";
// require_once './middlewares/Logger.php';

/*// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
*/

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

//BM

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
  $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
});


$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesasController::class . ':TraerTodos');
  $group->get('/{id}', \MesasController::class . ':TraerUno');
  $group->post('[/]', \MesasController::class . ':CargarUno');
  $group->put('/{id}', \MesasController::class . ':ModificarUno');
});



$app->group('/orden', function (RouteCollectorProxy $group) {
  $group->get('[/]', \OrdenController::class . ':TraerTodos');  // Ajuste aquí
  $group->get('/{id}', \OrdenController::class . ':TraerUno');  // Ajuste aquí
  $group->post('[/]', \OrdenController::class . ':CargarUno');  // Ajuste aquí
  $group->put('/{id}', \OrdenController::class . ':ModificarUno');  // Ajuste aquí
});



$app->group('/comanda', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ComandaController::class . ':TraerTodos');  // Ajuste aquí
  $group->get('/{id}', \ComandaController::class . ':TraerUno');  // Ajuste aquí
  $group->post('[/]', \ComandaController::class . ':CargarUno');  // Ajuste aquí
  $group->put('/{id}', \ComandaController::class . ':ModificarUno');  // Ajuste aquí
  //$group->('/{id}', \ComandaController::class . ':BorrarUno');  // Ajuste aquí
});


$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
?>
