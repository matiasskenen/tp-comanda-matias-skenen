<?php
require_once './models/Mesas.php';
require_once './interfaces/IApiUsable.php';

class ProductosController extends Mesas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
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

    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {

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

    }
    
    public function ModificarUno($request, $response, $args)
    {
        
    }

    public function BorrarUno($request, $response, $args)
    {
        
    }
}
