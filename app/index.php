<?php

require_once '../vendor/autoload.php';

require_once "../db/conectarDB.php";

require_once "../middllewares/authmiddllewares.php";

//rutas
require_once './controllers/UsuarioController.php';
require_once './controllers/MesasController.php';
require_once './controllers/PedidosController.php';
require_once './controllers/ProductoController.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
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
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesasController::class . ':TraerTodos');
  $group->get('/{id}', \MesasController::class . ':TraerUno');
  $group->post('[/]', \MesasController::class . ':CargarUno');
  $group->put('/{id}', \MesasController::class . ':ModificarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductosController::class . ':TraerTodos');  // Ajuste aquí
  $group->get('/{id}', \ProductosController::class . ':TraerUno');  // Ajuste aquí
  $group->post('[/]', \ProductosController::class . ':CargarUno');  // Ajuste aquí
  $group->put('/{id}', \ProductosController::class . ':ModificarUno');  // Ajuste aquí
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidosController::class . ':TraerTodos');  // Ajuste aquí
  $group->get('/{id}', \PedidosController::class . ':TraerUno');  // Ajuste aquí
  $group->post('[/]', \PedidosController::class . ':CargarUno');  // Ajuste aquí
  $group->put('/{id}', \PedidosController::class . ':ModificarUno');  // Ajuste aquí
});


$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
