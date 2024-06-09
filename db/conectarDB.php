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
function usuarioExiste($usuario, $db)
{
    $consulta = "SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario";
    $insert = $db->prepare($consulta);
    $insert->bindParam(':usuario', $usuario);
    $insert->execute();
    
    return $insert->fetchColumn() > 0;
}


function productoExiste($tipo, $nombre, $db)
{
    $consulta = "SELECT COUNT(*) FROM productos WHERE tipo = :tipo AND nombre = :nombre";
    $insert = $db->prepare($consulta);
    $insert->bindParam(':tipo', $tipo);
    $insert->bindParam(':nombre', $nombre);
    $insert->execute();
    
    return $insert->fetchColumn() > 0;
}

function mesasCompletas($db, $mesasMaximas)
{
    $consulta = "SELECT COUNT(*) FROM mesas";
    $insert = $db->prepare($consulta);
    $insert->execute();
    
    $cantidadMesas = $insert->fetchColumn();

    return $cantidadMesas >= $mesasMaximas;
}

?>