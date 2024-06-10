<?php
require_once './models/Mesas.php';
require_once './interfaces/IApiUsable.php';

class PedidosController extends Mesas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
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
            catch (PDOException $exepcion)
            {
                $error = array("error" => $e->getMessage());
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
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $params);

        if (!isset($params["id_mesa"]) || !is_numeric($params["id_mesa"]) || empty($params["producto"]) || empty($params["cantidad"])) {
            $response->getBody()->write(json_encode(["error" => "Completar todos los campos: [id_mesa][producto][cantidad]"]));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else 
        {
            $db = conectar();

            $consulta = "UPDATE pedidos SET id_mesa = :id_mesa, producto = :producto, cantidad = :cantidad WHERE id_pedido = :id";

            try {
                $update = $db->prepare($consulta);
                $update->bindParam(':id_mesa', $params['id_mesa']);
                $update->bindParam(':producto', $params['producto']);
                $update->bindParam(':cantidad', $params['cantidad']);
                $update->bindParam(':id', $id);
                $update->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Pedido actualizado exitosamente"]));
            } catch (PDOException $exepcion) {
                $error = array("error" => $exepcion->getMessage());
                $response->getBody()->write(json_encode($error));
            }

            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        
    }
}
