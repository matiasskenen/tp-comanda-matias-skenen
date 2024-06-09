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
?>