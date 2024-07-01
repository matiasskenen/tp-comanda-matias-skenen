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
                $error = array("error" => $exepcion->getMessage());
                $response->getBody()->write(json_encode($error));
            }

            return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function obtenerTodos()
    {
        $db = conectar();
    
        $consulta = "SELECT * FROM orden";
    
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

            echo "Error en la base de datos: " . $e->getMessage();

        }
    }

    public static function ModificarOrdenEstado($id_comanda, $estado, $puesto)
    {
        if(!($puesto == "socio"))
        {
            try {
                $db = conectar();
                
                // Ajuste de la consulta SQL
                $consulta = "UPDATE comanda_productos SET estado = :estado WHERE id_comanda = :id_comanda AND puesto = :puesto";
        
                $update = $db->prepare($consulta);
                // Vincular los parámetros correctamente
                $update->bindParam(':estado', $estado);
                $update->bindParam(':id_comanda', $id_comanda);
                $update->bindParam(':puesto', $puesto);
        
                // Ejecutar la consulta
                $update->execute();
        
            } catch (PDOException $e) {
                // Manejar la excepción y mostrar un mensaje de error
                echo "Error en la base de datos: " . $e->getMessage();
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
                echo "Error en la base de datos: " . $e->getMessage();
            }
        }
        
    }

    public static function borrarOrden($id)
    {
    }
}

?>