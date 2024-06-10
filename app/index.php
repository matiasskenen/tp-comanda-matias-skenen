<?php
require_once '../vendor/autoload.php';
require_once "../db/conectarDB.php";
require_once "../middllewares/authmiddllewares.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->add(new authmiddllewares()); 

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Funciona!");
    
    return $response;
});

//http://localhost:666/usuarios
$app->post("/usuarios", function(Request $request, Response $response, $args)
{
    $params = $request->getParsedBody();
    
    if (!isset($params['usuario']) || empty($params["usuario"]) || !isset($params['tipo']) || empty($params["tipo"]) 
    || !isset($params['clave']) || empty($params["clave"]))
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

        $consulta = "INSERT INTO usuarios (usuario, tipo, clave, nombre, apellido) 
        VALUES (:usuario, :tipo, :clave, :nombre, :apellido)";
    
        try
        {
            $insert = $db->prepare($consulta);
            $insert->bindParam(':usuario', $params['usuario']);
            $insert->bindParam(':tipo', $params['tipo']);
            $insert->bindParam(':clave', $params['clave']);
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


//http://localhost:666/productos
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
//http://localhost:666/mesas
$app->post("/mesas", function ($request, $response, array $args) {

    $mesasMaximas = 1;

    $data = $request->getParsedBody();

    if (!isset($data["max_comensales"]) || empty($data["max_comensales"]))
    {
        $response->getBody()->write(json_encode(["error" => "Completar la cantidad de maxima de comensales: [max_comensales]"]));
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
            $insert = $db->prepare($consulta);
            $insert->bindParam(':max_comensales', $data['max_comensales']);
            $insert->execute();
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

$app->post("/pedidos", function ($request, $response, array $args)
{
    $data = $request->getParsedBody();

    if (!isset($data["id_mesa"]) || empty($data["id_mesa"]) || !isset($data["producto"]) || empty($data["producto"]) 
    || !isset($data["cantidad"]) || empty($data["cantidad"]))
    {
        $response->getBody()->write(json_encode(["error" => "Completar todos los campos: [id_mesa][producto][cantidad]"]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
        $db = conectar();

        if (pedidoExiste($data["id_mesa"], $data["producto"], $data["cantidad"], $db)) {
            $response->getBody()->write(json_encode(["error" => "El pedido ya existe en la base de datos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $consulta = "INSERT INTO pedidos (id_mesa, producto, cantidad) VALUES (:id_mesa, :producto, :cantidad)";

        try
        {
            $insert = $db->prepare($consulta);
            $insert->bindParam(':id_mesa', $data['id_mesa']);
            $insert->bindParam(':producto', $data['producto']);
            $insert->bindParam(':cantidad', $data['cantidad']);
            $insert->execute();
            $response->getBody()->write(json_encode(["mensaje" => "Pedido agregado exitosamente"]));
        }
        catch (PDOException $e)
        {
            $error = array("error" => $e->getMessage());
            $response->getBody()->write(json_encode($error));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
});


//Get

$app->get("/usuarios", function ($request, $response, array $args) {

    $db = conectar();

    $consulta = "SELECT * FROM usuarios";

    try
    {
        $insert = $db->query($consulta);
        $usuarios = $insert->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($usuarios));
    }
    catch (PDOException $exepcion)
    {
        $error = array("error" => $exepcion->getMessage());
        $response->getBody()->write(json_encode($error));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get("/productos", function ($request, $response, array $args) {

    $db = conectar();

    $consulta = "SELECT * FROM productos";

    try
    {
        $insert = $db->query($consulta);
        $usuarios = $insert->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($usuarios));
    }
    catch (PDOException $exepcion)
    {
        $error = array("error" => $exepcion->getMessage());
        $response->getBody()->write(json_encode($error));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get("/mesas", function ($request, $response, array $args) {

    $db = conectar();

    $consulta = "SELECT * FROM mesas";

    try
    {
        $insert = $db->query($consulta);
        $usuarios = $insert->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($usuarios));
    }
    catch (PDOException $exepcion)
    {
        $error = array("error" => $exepcion->getMessage());
        $response->getBody()->write(json_encode($error));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get("/pedidos", function ($request, $response, array $args) {

    $db = conectar();

    $consulta = "SELECT * FROM pedidos";

    try
    {
        $insert = $db->query($consulta);
        $usuarios = $insert->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($usuarios));
    }
    catch (PDOException $exepcion)
    {
        $error = array("error" => $exepcion->getMessage());
        $response->getBody()->write(json_encode($error));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

/*
http://localhost:666/
php -S localhost:666 -t app
*/

$app->run();
?>