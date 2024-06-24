<?php
class Comandas{

    public $id_comanda;
    public $id_mesa;
    public $cliente;
    public $precio;
    public $estado;
    public $demora; 
    public $fecha;
    
    public function crearComanda($response)
    {
        $db = conectar();
                
        $fecha = new DateTime();

        $insert = "INSERT INTO comandas (id_mesa, cliente, precio, estado, demora, fecha) 
           VALUES (:id_mesa, :cliente, :precio, :estado, :demora, :fecha)";

            try {
                $consulta = $db->prepare($insert);
                $consulta->bindValue(':id_mesa', $this->id_mesa);
                $consulta->bindValue(':cliente', $this->cliente);
                $consulta->bindValue(':precio', $this->precio);
                $consulta->bindValue(':estado', $this->estado);
                $consulta->bindValue(':demora', $this->demora);
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

        $consulta = "SELECT * FROM comandas";
    
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

    public static function modificarComanda($id, $estado)
    {
        try {
            parse_str(file_get_contents("php://input"), $parametros);
            $db = conectar();
    
            $consulta = "UPDATE comandas SET estado = :estado WHERE id_comanda = :id";
    
            $update = $db->prepare($consulta);
            $update->bindValue(':estado', $estado);
            $update->bindValue(':id', $id);
    
            $update->execute();
    
            echo "Comanda modificada exitosamente";
        } catch (PDOException $e) {
            // Manejo del error
            echo "Error: " . $e->getMessage();
        }
    }

    public static function borrarComanda($id_comanda)
    {
        $db = conectar();
        $consulta = "DELETE FROM comandas WHERE id_comanda  = :id_comanda";
        $consulta->bindValue(':id_comanda', $id_comanda);
        $consulta->execute();
    }

    public static function comandaEstado($valor)
    {
        switch($valor)
        {
            case 1:
                return 'ingresada';
            case 2:
                return "en preparacion";
            case 3:
                return "lista para servir";
            default:
                return 'error';
    
        }
    }

    public static function obtenerComandaCodigo($codigo_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_mesa, nombre_cliente, codigo_comanda,
                                                            importe, estado, demora, baja
                                                        FROM comanda WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();

        //return $consulta->fetchObject('Comanda');
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }

}


?>