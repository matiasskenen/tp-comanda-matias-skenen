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

        if (usuarioExiste($params['usuario'], $db)) {
            $response->getBody()->write(json_encode(["error" => "El usuario ya existe en la base de datos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }

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

$app->post("/productos", function ($request, $response, array $args) {

    $params = $request->getParsedBody();

    if (!isset($params["tipo"]) || empty($params["tipo"]) || !in_array($params["tipo"], ["bebida", "comida"]) 
        || !isset($params["nombre"]) || empty($params["nombre"]))
    {
        $response->getBody()->write(json_encode(["error" => "Completar todos los campos [tipo](bebida/comida)[nombre]"]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
        $db = conectar();
        
        if (productoExiste($params['tipo'], $params['nombre'], $db)) {
            $response->getBody()->write(json_encode(["error" => "El producto ya existe en la base de datos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $consulta = "INSERT INTO productos (tipo, nombre) VALUES (:tipo, :nombre)";

        try
        {
            $insert = $db->prepare($consulta);
            $insert->bindParam(':tipo', $params['tipo']);
            $insert->bindParam(':nombre', $params['nombre']);
            
            $insert->execute();
            $response->getBody()->write(json_encode(["mensaje" => "Producto agregado exitosamente"]));
        }
        catch (PDOException $exepcion)
        {
            $error = array("error" => $exepcion->getMessage());
            $response->getBody()->write(json_encode($error));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
});

$app->post("/mesas", function ($request, $response, array $args) {

    // Obtener los datos del cuerpo de la solicitud
    $mesasMaximas = 10;

    $data = $request->getParsedBody();

    if (!isset($data["max_comensales"]) || empty($data["max_comensales"]))
    {
        $response->getBody()->write(json_encode(["error" => "Completar la cantidad de max comensales [max_comensales]"]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
        $db = conectar();

        if (mesasCompletas($db, $mesasMaximas)) {
            $response->getBody()->write(json_encode(["error" => "No se pueden agregar más mesas, se ha alcanzado el límite máximo"]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $consulta = "INSERT INTO mesas (max_comensales) VALUES (:max_comensales)";

        try
        {
            $stmt = $db->prepare($consulta);
            $stmt->bindParam(':max_comensales', $data['max_comensales']);
            $stmt->execute();
            $response->getBody()->write(json_encode(["mensaje" => "Mesa agregada exitosamente"]));
        }
        catch (PDOException $e)
        {
            $error = array("error" => $e->getMessage());
            $response->getBody()->write(json_encode($error));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

});

/*
http://localhost:666/
php -S localhost:666 -t app
*/

$app->run();
?>