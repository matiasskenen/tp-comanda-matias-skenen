<?php

/*

php -S localhost:666 -t app

*/

require_once '../vendor/autoload.php';
//require_once "../middlewares/authMid.php";
require_once '../middlewares/UsuariosMiddleware.php';
require_once '../middlewares/UsuariosMiddleware.php';


//rutas
require_once './controllers/UsuarioController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/MesasController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/OrdenController.php';
require_once './controllers/EmpleadosController.php';


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

  $group->post('/login', \UsuarioController::class . ':LogIn'); // token
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');

  //Socios
  $group->post('/alta', \UsuarioController::class . ':CargarUno')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

  $group->put('/modificar', \UsuarioController::class . ':ModificarUno')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

  $group->delete('/borrar', \UsuarioController::class . ':BorrarUno')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
  
  $group->post('/generarCSV', \UsuarioController::class . ':GenerarCSV')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

  /*
  $group->get('/descargar', \UsuarioController::class . ':DescargarCSV')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
  */

});


$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesasController::class . ':TraerTodos');
  $group->get('/{id}', \MesasController::class . ':TraerUno');
  $group->post('[/]', \MesasController::class . ':CargarUno');
  $group->put('/{id}', \MesasController::class . ':ModificarUno');
  $group->delete('/borrar', \MesasController::class . ':BorrarUno')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
});

$app->group('/orden', function (RouteCollectorProxy $group) {
  $group->get('[/]', \OrdenController::class . ':TraerTodos');  
  $group->get('/{id}', \OrdenController::class . ':TraerUno');  
  $group->post('[/]', \OrdenController::class . ':CargarUno');
  $group->put('/modificarpedido', \OrdenController::class . ':ModificarUno');
  $group->put('/modificarestado', \OrdenController::class . ':ModificarEstado'); 
});

$app->group('/comanda', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ComandaController::class . ':TraerTodos');  
  $group->get('/{id}', \ComandaController::class . ':TraerUno');  
  $group->post('[/]', \ComandaController::class . ':CargarUno');  
  $group->put('/{id}', \ComandaController::class . ':ModificarUno');
  $group->delete('/borrar', \ComandaController::class . ':BorrarUno')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');   
});

$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->get('/ingreso', \EmpleadosController::class . ':TraerTodos')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
  $group->get('/cantidad', \EmpleadosController::class . ':cantidadOp')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
  $group->get('/sector', \EmpleadosController::class . ':OpSector')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');   
});

$app->group('/encuesta', function (RouteCollectorProxy $group) {
  $group->post('/alta', \EncuestaController::class . ':CargarUno');
  $group->get('[/]', \EncuestaController::class . ':TraerTodos')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio'); 
  $group->put('/modificar', \EncuestaController::class . ':ModificarUno')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');  
});


$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
?>