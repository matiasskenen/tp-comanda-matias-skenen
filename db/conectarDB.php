<?php
function conectar()
{
    try
    {
        $host = 'localhost';
        $dbname = 'api-comanda-tp';
        $username = 'root';
        $password = '';

        $conectar = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conectar;
    }
    catch (PDOException $exepcion)
    {
        echo "Erro al conectar: " . $exepcion->getMessage() . "<br>";
        return null;
    }
}

function usuarioExiste($usuario)
{
    $db = conectar();

    $consulta = "SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario";
    $datos = $db->prepare($consulta);
    $datos->bindParam(':usuario', $usuario);
    $datos->execute();
    
    return $datos->fetchColumn() > 0;
}

function validarLogin($usuario, $puesto, $clave)
{
    $db = conectar(); // Suponiendo que esta función establece la conexión PDO

    // Consulta preparada para buscar el usuario por usuario y clave
    $consulta = "SELECT * FROM usuarios WHERE usuario = :usuario AND puesto = :puesto AND clave = :clave";

    try {
        $datos = $db->prepare($consulta);
        $datos->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $datos->bindParam(':puesto', $puesto, PDO::PARAM_STR);
        $datos->bindParam(':clave', $clave, PDO::PARAM_STR);
        $datos->execute();

        $usuario = $datos->fetch(PDO::FETCH_ASSOC);

        if ($usuario) 
        {
            return true;
        } 
        else 
        {
            return false;
        }

    } catch (PDOException $excepcion) {
        // Manejo de errores de la base de datos
        throw new Exception("Error al validar el login: " . $excepcion->getMessage());
    }
}

function productoExiste($tipo, $nombre, $db)
{
    $consulta = "SELECT COUNT(*) FROM productos WHERE tipo = :tipo AND nombre = :nombre";
    $datos = $db->prepare($consulta);
    $datos->bindParam(':tipo', $tipo);
    $datos->bindParam(':nombre', $nombre);
    $datos->execute();
    
    return $datos->fetchColumn() > 0;
}

function comandaExiste($id_mesa, $cliente)
{
    $db = conectar();

    $consulta = "SELECT COUNT(*) FROM comandas WHERE id_mesa = :id_mesa AND cliente = :cliente";
    $datos = $db->prepare($consulta);
    $datos->bindParam(':id_mesa', $id_mesa);
    $datos->bindParam(':cliente', $cliente);
    $datos->execute();
    
    return $datos->fetchColumn() > 0;
}

function mesasExiste($max_comensales, $codigo_comanda)
{
    $db = conectar();

    $consulta = "SELECT COUNT(*) FROM mesas WHERE max_comensales = :max_comensales AND codigo_comanda = :codigo_comanda";
    $datos = $db->prepare($consulta);
    $datos->bindParam(':max_comensales', $max_comensales);
    $datos->bindParam(':codigo_comanda', $codigo_comanda);
    $datos->execute();
    
    return $datos->fetchColumn() > 0;
}

function pedidoExiste($id_mesa, $producto, $cantidad, $db)
{
    $consulta = "SELECT COUNT(*) FROM pedidos WHERE id_mesa = :id_mesa AND producto = :producto AND cantidad = :cantidad";
    $datos = $db->prepare($consulta);
    $datos->bindParam(':id_mesa', $id_mesa);
    $datos->bindParam(':producto', $producto);
    $datos->bindParam(':cantidad', $cantidad);
    $datos->execute();
    
    return $datos->fetchColumn() > 0;
}

?>