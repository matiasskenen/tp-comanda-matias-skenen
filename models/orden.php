<?php
class Orden{

    public $id;
    public $codigo_comanda;
    public $pedido;
    public $area;
    public $demora;
    public $estado;

    public function GetCodigo(){
        return $this->codigo_comanda;
    }
    public function GetPedido(){
        return $this->pedido;
    }
    public function GetEstado(){
        return $this->estado;
    }
    public function GetDemora(){
        return $this->demora;
    }

    public function crearOrden()
    {
        //post
        $db = conectar();

        $insert = "INSERT INTO orden (codigo_comanda, pedido, area, demora, estado) 
           VALUES (:codigo_comanda, :pedido, :area, :demora, :estado)";

            try {
                $consulta = $db->prepare($insert);
                $consulta->bindValue(':codigo_comanda', $this->codigo_comanda);
                $consulta->bindValue(':pedido', $this->pedido);
                $consulta->bindValue(':area', $this->area);
                $consulta->bindValue(':demora', $this->demora);
                $consulta->bindValue(':estado', $this->estado);
                $consulta->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Orden agregada exitosamente"]));

            } catch (PDOException $exepcion) {
                $error = array("error" => $exepcion->getmensaje());
                $response->getBody()->write(json_encode($error));
            }

            return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function obtenerTodos($puesto, $response)
    {
        $db = conectar();
    
        if ($puesto !== "socio") {
            $consulta = "SELECT * FROM comanda_productos WHERE puesto = :puesto";
        } else {
            $consulta = "SELECT * FROM comanda_productos";
        }
    
        try {
            $stmt = $db->prepare($consulta);
    
            if ($puesto !== "socio") {
                $stmt->bindParam(':puesto', $puesto, PDO::PARAM_STR);
            }
    
            $stmt->execute();
    
            $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($ordenes) {
                $response->getBody()->write(json_encode($ordenes));
            } else {
                $response->getBody()->write(json_encode(array("mensaje" => "No hay ordenes disponibles.")));
            }
        } catch (PDOException $excepcion) {
            $error = array("error" => $excepcion->getmensaje());
            $response->getBody()->write(json_encode($error));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarOrdenPedido($codigo_comanda, $pedido)
    {
        parse_str(file_get_contents("php://input"), $parametros);
        
        try {
            $db = conectar();
            
            $consulta = "UPDATE orden SET pedido = :pedido WHERE codigo_comanda = :codigo_comanda";
    
            $update = $db->prepare($consulta);
            $update->bindParam(':pedido', $parametros['pedido']);
            $update->bindParam(':codigo_comanda', $codigo_comanda);
            $update->execute();
        } catch (PDOException $e) {

            echo "Error en la base de datos: " . $e->getmensaje();

        }
    }

    public static function ModificarOrdenEstado($id_comanda, $estado, $puesto, $demora)
    {
        if(!($puesto == "socio"))
        {
            try {
                $db = conectar();
                
                // Ajuste de la consulta SQL
                $consulta = "UPDATE comanda_productos SET estado = :estado, demora = :demora WHERE id_comanda = :id_comanda AND puesto = :puesto";
        
                $update = $db->prepare($consulta);
                // Vincular los parámetros correctamente
                $update->bindParam(':estado', $estado);
                $update->bindParam(':demora', $demora);
                $update->bindParam(':id_comanda', $id_comanda);
                $update->bindParam(':puesto', $puesto);
        
                // Ejecutar la consulta
                $update->execute();
        
            } catch (PDOException $e) {
                // Manejar la excepción y mostrar un mensaje de error
                echo "Error en la base de datos: " . $e->getmensaje();
            }
        }
        else
        {
            try {
                $db = conectar();
                
                // Ajuste de la consulta SQL
                $consulta = "UPDATE comanda_productos SET estado = :estado WHERE id_comanda = :id_comanda";
        
                $update = $db->prepare($consulta);
                // Vincular los parámetros correctamente
                $update->bindParam(':estado', $estado);
                $update->bindParam(':id_comanda', $id_comanda);
        
                // Ejecutar la consulta
                $update->execute();
        
            } catch (PDOException $e) {
                // Manejar la excepción y mostrar un mensaje de error
                echo "Error en la base de datos: " . $e->getmensaje();
            }
        }
        
    }

    public static function ActualizarComanda($demora, $id_mesa)
    {
        try {
            $db = conectar();

            $consulta = "UPDATE comandas SET demora = :demora WHERE id_mesa = :id_mesa";
            $datos = $db->prepare($consulta);
            $datos->bindParam(':demora', $demora);
            $datos->bindParam(':id_mesa', $id_mesa);
            $datos->execute();

            if ($datos->rowCount() > 0) {
                return "La demora ha sido actualizada correctamente.";
            } else {
                return "No se encontró ninguna comanda con el id_mesa proporcionado.";
            }

        } catch (PDOException $e) {
            return "Error en la base de datos: " . $e->getmensaje();
        }
    }

    public static function obtenerMesaPorCodigoComanda($id_comanda)
    {
        try {
            $db = conectar();


            $consulta = "SELECT mesa FROM comanda_productos WHERE id_comanda = :id_comanda";
            $datos = $db->prepare($consulta);
            $datos->bindParam(':id_comanda', $id_comanda);
            $datos->execute();

            $resultado = $datos->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return $resultado['mesa'];
            } else {
                return null;
            }

        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getmensaje();
            return null;
        }
    }

    public static function obtenerDemoraPorCodigoComanda($id_comanda)
    {
        try {
            $db = conectar();


            $consulta = "SELECT demora FROM comanda_productos WHERE id_comanda = :id_comanda";
            $datos = $db->prepare($consulta);
            $datos->bindParam(':id_comanda', $id_comanda);
            $datos->execute();

            $resultado = $datos->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return $resultado['demora'];
            } else {
                return null;
            }

        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getmensaje();
            return null;
        }
    }

    public static function restarDemoraPorIdMesa($demoraARestar, $id_mesa)
    {
        try 
        {
            $db = conectar();

            $consultaSelect = "SELECT demora FROM comandas WHERE id_mesa = :id_mesa";
            $datosSelect = $db->prepare($consultaSelect);
            $datosSelect->bindParam(':id_mesa', $id_mesa);
            $datosSelect->execute();

            $resultado = $datosSelect->fetch(PDO::FETCH_ASSOC);

            if ($resultado) 
            {
                $demoraActual = $resultado['demora'];
                $nuevaDemora = $demoraActual - $demoraARestar;

                if ($nuevaDemora < 0) 
                {
                    $nuevaDemora = 0;
                }

                $consultaUpdate = "UPDATE comandas SET demora = :nueva_demora WHERE id_mesa = :id_mesa";
                $datosUpdate = $db->prepare($consultaUpdate);
                $datosUpdate->bindParam(':nueva_demora', $nuevaDemora);
                $datosUpdate->bindParam(':id_mesa', $id_mesa);
                $datosUpdate->execute();

                if ($datosUpdate->rowCount() > 0) 
                {
                    return "La demora ha sido actualizada correctamente.";
                } else 
                {
                    return "No se encontró ninguna comanda con el id_mesa proporcionado.";
                }
            }

        } 
        catch (PDOException $e) 
        {
            return "Error en la base de datos: " . $e->getmensaje();
        }
    }

    public static function borrarOrden($id)
    {
    }
}

?>