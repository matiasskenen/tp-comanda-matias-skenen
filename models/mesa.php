<?php
require_once '../db/conectarDB.php';
class Mesas{

    public $mesas_Maximas = 5;
    public $id;
    public $codigo_comanda;
    public $max_comensales;
    public $codigo_mesa;
    public $estado;
    public $mozo;
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
        $this->estado = self::mesaEstado($this->estado);

        $insert = "INSERT INTO mesas (max_comensales, codigo_comanda, estado, mozo, fecha) 
            VALUES (:max_comensales, :codigo_comanda, :estado, :mozo, :fecha)";

            try {
                $consulta = $db->prepare($insert);
                $consulta->bindValue(':max_comensales', $this->max_comensales);
                $consulta->bindValue(':codigo_comanda', $this->codigo_comanda);
                $consulta->bindValue(':estado', $this->estado);
                $consulta->bindValue(':mozo', $this->mozo);
                $consulta->bindValue(':fecha', $fecha->format('d-m-Y'));
                $consulta->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Mesa agregada exitosamente"]));

            } catch (PDOException $exepcion) {
                $error = array("error" => $exepcion->getmensaje());
                $response->getBody()->write(json_encode($error));
            }



        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerTodos($response)
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
            $error = array("error" => $exepcion->getmensaje());
            $response->getBody()->write(json_encode($error));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function borrarMesa($codigo_mesa, $response)
    {
        try {
            $db = conectar(); 
            
            $consulta = "DELETE FROM mesas WHERE codigo_mesa = :codigo_mesa";
            $datos = $db->prepare($consulta);
            $datos->bindParam(':codigo_mesa', $codgio_mesa);
            $datos->execute();

            $rowCount = $datos->rowCount(); // numero de filas afectadas
            
            if ($rowCount > 0) {

                $response->getBody()->write(json_encode(["mensaje" => "Mesa eliminado correctamente."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(["mensaje" => "No se encontró ningún Mesa"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

    }

    public static function modificarMesa($id, $codigo_comanda, $estado, $max_comensales, $response)
    {
        $db = conectar();

        $estadoNuevo = self::mesaEstado($estado); 
        $consulta = "UPDATE mesas SET estado = :estado, codigo_comanda = :codigo_comanda, max_comensales = :max_comensales WHERE codigo_mesa = :id";

        $update = $db->prepare($consulta);
        $update->bindValue(':estado', $estadoNuevo);
        $update->bindValue(':codigo_comanda', $codigo_comanda);
        $update->bindValue(':max_comensales', $max_comensales);
        $update->bindValue(':id', $id);
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
                return 'error';
    
        }
    }

    public static function ingresarVentaImagen($nombre, $response)
    {
        $nombre_archivo = $_FILES['imagen']['name'];
        $ubicacionTemporal = $_FILES['imagen']['tmp_name'];
        $carpetaDestino = '../ImagenesMesa/2024/';
    
        $fechaVenta = date('Y-m-d');
        $nuevo_nombre_archivo = $nombre . '.' . $fechaVenta . '.' . $nombre_archivo;
        $rutaDestino = $carpetaDestino . $nuevo_nombre_archivo;
    
        if (move_uploaded_file($ubicacionTemporal, $rutaDestino)) {
            $response->getBody()->write(json_encode(["mensaje" => "Imagen ingresada correctamente."]));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(["error" => "Imagen NO ingresada."]));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

}
?>