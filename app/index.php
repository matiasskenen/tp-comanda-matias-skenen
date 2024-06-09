<?php
require_once '../vendor/autoload.php';
require_once "../db/conectarDB.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();


$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Funciona!");
    
    return $response;
});

//http://localhost:666/usuarios
$app->post("/usuarios", function(Request $request, Response $response, $args)
{
    $params = $request->getParsedBody();
    
    if (!isset($params['usuario']) || empty($params["usuario"]) || !isset($params['tipo']) || empty($params["tipo"]) 
    || !isset($params['nombre']) || empty($params["nombre"]) || !isset($params['apellido']) || empty($params["apellido"]))
    {
        
        $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][tipo][nombre][apellido]"]));
        return $response->withHeader('Content-Type', 'application/json');
        
    }
    else
    {
        $db = conectar();

        $consulta = "INSERT INTO usuarios (usuario, tipo, nombre, apellido) 
        VALUES (:usuario, :tipo, :nombre, :apellido)";
    
        try
        {
            $insert = $db->prepare($consulta);
            $insert->bindParam(':usuario', $params['usuario']);
            $insert->bindParam(':tipo', $params['tipo']);
            $insert->bindParam(':nombre', $params['nombre']);
            $insert->bindParam(':apellido', $params['apellido']);
            
            $insert->execute();
            $response->getBody()->write(json_encode(["mensaje" => "Usuario agregado exitosamente"]));

        }
        catch (PDOException $exepcion)
        {
            $error = array("error" => $exepcion->getMessage());
            $response->getBody()->write(json_encode($error));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
});



/*
$app->get("/usuarios", function(Request $request, Response $response, $args)
{
    $params = $request->getQueryParams();
    
    $response->getBody()->write(json_encode($params)); 
    return $response;
});
*/

/*
http://localhost:666/
php -S localhost:666 -t app
*/

$app->run();
?>