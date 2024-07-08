<?php

class Encuestas{
    public $numero_mesa;
    public $codigo_pedido;
    public $nombre;
    public $puntuacion;


    public function crearEncuesta($response) 
    {
        $db = conectar();
        
        $fecha = new DateTime();
        $fechaStr = $fecha->format('Y-m-d');
        
        $insertProductos = "INSERT INTO encuestas (numero_mesa, nombre, puntuacion, codigo_pedido, fecha) 
                            VALUES (:numero_mesa, :nombre, :puntuacion, :codigo_pedido, :fecha)";
        
        $consultaProductos = $db->prepare($insertProductos);
        $consultaProductos->bindValue(':numero_mesa', $this->numero_mesa);
        $consultaProductos->bindValue(':nombre', $this->nombre);
        $consultaProductos->bindValue(':codigo_pedido', $this->codigo_pedido);
        $consultaProductos->bindValue(':puntuacion', $this->puntuacion);
        $consultaProductos->bindValue(':fecha', $fechaStr);
        $consultaProductos->execute();
        
        $response->getBody()->write(json_encode(['message' => 'Encuesta Creada']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerTodos($response)
    {
        $db = conectar();
        $consulta = "SELECT * FROM encuestas ORDER BY puntuacion DESC";
        $datos = $db->prepare($consulta);
        $datos->execute();
    
        $resultado = $datos->fetchAll();
    
        if ($resultado) 
        {
            $response->getBody()->write(json_encode($resultado));
        } else 
        {
            $mensaje = array("mensaje" => "no se encontraron registros");
            $response->getBody()->write(json_encode($mensaje));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function modificarEncuesta($codigo_pedido, $puntuacion, $response)
    {
        $header = $request->getHeaderLine('authorization'); 
    
        if(empty($header))
        {
            $response->getBody()->write(json_encode(array("error" => "No se ingreso el token")));
            $response = $response->withStatus(401);
        }
        else
        {
            $token = trim(explode("Bearer", $header)[1]);
            
            $data = AutenticacionJWT::ObtenerData($token);
            $puesto = $data->tipo_usuario;
            $nombre = $data->usuario;

            parse_str(file_get_contents("php://input"), $parametros);
            $db = conectar();

            $consulta = "UPDATE encuestas SET puntuacion = :puntuacion WHERE codigo_pedido = :codigo_pedido";

            $update = $db->prepare($consulta);
            $update->bindValue(':puntuacion', $puntuacion);
            $update->bindValue(':codigo_pedido', $codigo_pedido);

            $update->execute();

            $response->getBody()->write(json_encode(['message' => 'Encuesta Modificada']));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

}