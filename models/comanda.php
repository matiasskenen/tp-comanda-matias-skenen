<?php
require_once '../db/conectarDB.php';
class Comandas{

    public $codigo_comanda;
    public $id_mesa;
    public $cliente;
    public $estado;
    public $productos;
    public $cantidad;
    public $demora = 0; 
    public $fecha;
    public $precio = 0;
    
    public function crearComanda($response) 
    {
        $db = conectar();
                
        $fecha = new DateTime();
        $this->estado = self::comandaEstado($this->estado);
        if ($this->estado == "error") {
            $response->getBody()->write(json_encode(["error" => "No existe el estado de Comanda ingresado"]));
            return $response = $response->withStatus(401);
        }

        $this->codigo_comanda = rand(10000, 99999);

        $insertProductos = "INSERT INTO comanda_productos (mesa, id_producto, cantidad, estado, puesto) 
                            VALUES (:mesa, :id_producto, :cantidad, :estado, :puesto)";

        $productosArray = json_decode($this->productos, true);

        foreach ($productosArray as $producto) {
            $puesto = self::comandaProductos($producto['id']);
            $this->precio += self::comandaPrecio($producto['id']);

            if ($producto['cantidad'] <= 0) {
                throw new Exception("Ingrese una cantidad válida para el Producto");
            }

            $consultaProductos = $db->prepare($insertProductos);
            $consultaProductos->bindValue(':mesa', $this->id_mesa);
            $consultaProductos->bindValue(':id_producto', $producto['id']);
            $consultaProductos->bindValue(':cantidad', $producto['cantidad']);
            $consultaProductos->bindValue(':estado', "Sin empezar");
            $consultaProductos->bindValue(':puesto', $puesto);
            $consultaProductos->execute();
        }


        $insertComanda = "INSERT INTO comandas (id_mesa, cliente, estado, demora, fecha, precio, codigo_comanda) 
                          VALUES (:id_mesa, :cliente, :estado, :demora, :fecha, :precio, :codigo_comanda)";
        
        try {
            $consultaComanda = $db->prepare($insertComanda);
            $consultaComanda->bindValue(':id_mesa', $this->id_mesa);
            $consultaComanda->bindValue(':cliente', $this->cliente);
            $consultaComanda->bindValue(':estado', $this->estado);
            $consultaComanda->bindValue(':demora', $this->demora);
            $consultaComanda->bindValue(':precio', $this->precio);
            $consultaComanda->bindValue(':codigo_comanda', $this->codigo_comanda);
            $consultaComanda->bindValue(':fecha', $fecha->format('Y-m-d'));
            $consultaComanda->execute();
            

            $response->getBody()->write(json_encode(["mensaje" => "Comanda agregada exitosamente: Codigo = ". $this->codigo_comanda]));

        } catch (PDOException $exepcion) {
            $error = array("error" => $exepcion->getmensaje());
            $response->getBody()->write(json_encode($error));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerTodos($codigo_comanda, $response)
    {
        $db = conectar();
        $consulta = "SELECT * FROM comandas WHERE codigo_comanda = :codigo_comanda";
        $datos = $db->prepare($consulta);
        $datos->bindParam(':codigo_comanda', $codigo_comanda);
        $datos->execute();

        $resultado = $datos->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $response->getBody()->write(json_encode($resultado));
        } else {
            $mensaje = array("mensaje" => "No se encontraron registros en la tabla.");
            $response->getBody()->write(json_encode($mensaje));
        }
    }

    public static function modificarComanda($id, $estado)
    {
        parse_str(file_get_contents("php://input"), $parametros);
        $db = conectar();

        $consulta = "UPDATE comandas SET estado = :estado WHERE id_comanda = :id";

        $update = $db->prepare($consulta);
        $update->bindValue(':estado', $estado);
        $update->bindValue(':id', $id);

        $update->execute();

        $response->getBody()->write(json_encode(['message' => 'Encuesta Modificada']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function borrarComanda($codigo_comanda, $response)
    {
            echo $codigo_comanda;
            $db = conectar(); 
            
            $consulta = "DELETE FROM comandas WHERE codigo_comanda = :codigo_comanda";
            $datos = $db->prepare($consulta);
            $datos->bindParam(':codigo_comanda', $codigo_comanda);
            $datos->execute();

            $filasContadas = $datos->filasContadas(); // numero de filas afectadas
            
            if ($filasContadas > 0) 
            {
                $response->getBody()->write(json_encode(["mensaje" => "Comanda eliminado correctamente."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } 
            else 
            {
                $response->getBody()->write(json_encode(["mensaje" => "No se encontró ningún Comanda"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
    }

    public static function comandaEstado($valor)
    {
        switch($valor)
        {
            case 1:
                return 'ingresada';
                break;
            case 2:
                return "en preparacion";
                break;
            case 3:
                return "lista para servir";
                break;
            default:
                return 'error';
                break;
    
        }
    }

    public static function comandaProductos($valor)
    {
        $producto = Menu::verificarMenu($valor);
        $puesto = Menu::puestoMenu($producto);
        return $puesto;
    }

    public static function comandaPrecio($valor)
    {
        $precio = Menu::valorMenu($valor);
        return $precio;
    }

}


?>