<?php
require_once 'menu.php';
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

        // Insertar los productos de la comanda
        $insertProductos = "INSERT INTO comanda_productos (mesa, id_producto, cantidad, estado, puesto) 
                            VALUES (:mesa, :id_producto, :cantidad, :estado, :puesto)";

        $productosArray = json_decode($this->productos, true);

        foreach ($productosArray as $producto) {
            // Verificar si el producto existe
            $puesto = self::comandaProductos($producto['id']);

            // Verificar la cantidad del producto
            if ($producto['cantidad'] <= 0) {
                throw new Exception("Ingrese una cantidad válida para el Producto");
            }

            // Insertar producto en la tabla comanda_productos
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
            

            $response->getBody()->write(json_encode(["mensaje" => "Comanda agregada exitosamente: Codigo = ". $this->$codigo_comanda]));

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
        /*
        foreach($valor as $comida)
        {
            if(Menu::verificarMenu($valor))
            {
                $precioTotal += Menu::valorMenu($valor);
            }
        }
            */
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