<?php
require_once './models/Mesas.php';
require_once './interfaces/IApiUsable.php';

class MesasController extends Mesas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
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

    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
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
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $params);

        if (!isset($params["max_comensales"]) || empty($params["max_comensales"]))
        {
            $response->getBody()->write(json_encode(["error" => "Completar la cantidad de maxima de comensales: [max_comensales]"]));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else 
        {
            $db = conectar();

            // Se usa 'id_usuario' en lugar de 'id'
            $consulta = "UPDATE mesas SET max_comensales = :max_comensales WHERE mesa_numero = :id";

            try {
                $update = $db->prepare($consulta);
                $update->bindParam(':max_comensales', $params['max_comensales']);
                $update->bindParam(':id', $id);
                $update->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Mesa actualizada exitosamente"]));
            } catch (PDOException $exepcion) {
                $error = ["error" => $exepcion->getMessage()];
                $response->getBody()->write(json_encode($error));
            }

            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        
    }
}
