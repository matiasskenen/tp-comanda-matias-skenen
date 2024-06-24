<?php
class Mesas{

    public $id;
    public $codigo_comanda;
    public $max_comensales;
    public $codigo_mesa;
    public $estado;
    public $mozo;
    public $foto;
    public $fecha;
    public $id_puntuacion; 

    public function GetEstado(){
        return $this->estado;
    }

    public function GetMesa(){
        return $this->codigo_mesa;
    }

    public function GetComanda(){
        return $this->codigo_comanda;
    }

    public function crearMesa($response)
    {
                $db = conectar();
                
                $fecha = new DateTime();

                $insert = "INSERT INTO mesas (max_comensales, codigo_comanda, estado, mozo, foto, fecha) 
                   VALUES (:max_comensales, :codigo_comanda, :estado, :mozo, :foto, :fecha)";

                    try {
                        $consulta = $db->prepare($insert);
                        $consulta->bindValue(':max_comensales', $this->max_comensales);
                        $consulta->bindValue(':codigo_comanda', $this->codigo_comanda);
                        $consulta->bindValue(':estado', $this->estado);
                        $consulta->bindValue(':mozo', $this->mozo);
                        $consulta->bindValue(':foto', $this->foto);
                        $consulta->bindValue(':fecha', $fecha->format('d-m-Y'));
                        $consulta->execute();
                        $response->getBody()->write(json_encode(["mensaje" => "Mesa agregada exitosamente"]));
        
                    } catch (PDOException $exepcion) {
                        $error = array("error" => $exepcion->getMessage());
                        $response->getBody()->write(json_encode($error));
                    }
        
                    return $response->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerTodos()
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

    public static function borrarMesa($codigo_mesa)
    {
        $db = conectar();
        $consulta = "DELETE FROM mesas WHERE codigo_mesa = :codigo_mesa";
        $consulta->bindValue(':codigo_mesa', $codigo_mesa);
        $consulta->execute();
    }

    public static function modificarMesa($id, $codigo_comanda, $estado, $max_comensales, $response)
    {
        parse_str(file_get_contents("php://input"), $parametros);
        $db = conectar();


            $consulta = "UPDATE mesas SET estado = :estado, codigo_comanda = :codigo_comanda, max_comensales = :max_comensales WHERE codigo_mesa = :id";

            $update = $db->prepare($consulta);
            $update->bindParam(':estado', $parametros['estado']);
            $update->bindParam(':codigo_comanda', $parametros['codigo_comanda']);
            $update->bindParam(':max_comensales', $parametros['max_comensales']);
            $update->bindParam(':id', $id);
            $update->execute();
    }

    public static function mesaEstado($valor)
    {
        switch($valor)
        {
            case 1:
                return 'cliente esperando pedido';
            case 2:
                return "cliente comiendo";
            case 3:
                return "cliente pagando";
            case 4:
                return "cerrada";
            default:
                return 'Error';
    
        }
    }

}
?>