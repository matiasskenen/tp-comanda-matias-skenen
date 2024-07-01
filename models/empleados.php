<?php
require_once '../db/conectarDB.php';
class Empleados
{
    
    public static function obtenerOperacionesPorTipo($operacion, $response) 
    {
        try {
            $db = conectar();
            
            $consulta = "SELECT * FROM operaciones WHERE operacion = :operacion";
            $stmt = $db->prepare($consulta);
            $stmt->bindParam(':operacion', $operacion);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($resultados) {
                $response->getBody()->write(json_encode($resultados));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(["mensaje" => "No se encontraron operaciones para el tipo especificado."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public static function cantidadOperaciones($operacion, $response) 
    {
        try {
            $db = conectar();
            
            // Consulta para obtener las operaciones del tipo especificado
            $consulta = "SELECT * FROM operaciones WHERE operacion = :operacion";
            $stmt = $db->prepare($consulta);
            $stmt->bindParam(':operacion', $operacion);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($resultados) {
                // Calcular la cantidad total de operaciones
                $cantidad = count($resultados);
                
                // Crear un array de respuesta con la cantidad y las operaciones
                $respuesta = [
                    "operacion" => $operacion,
                    "cantidad" => $cantidad,
                ];
                
                $response->getBody()->write(json_encode($respuesta));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(["mensaje" => "No se encontraron operaciones para el tipo especificado."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public static function OpSectorempleado($response) 
    {
        try {
            $db = conectar();
            
            $consulta = "
                SELECT 
                    nombre, 
                    puesto, 
                    COUNT(operacion) AS cantidad_operaciones 
                FROM 
                    operaciones 
                GROUP BY 
                    nombre, 
                    puesto
                ORDER BY 
                    nombre, 
                    puesto
            ";
            $stmt = $db->prepare($consulta);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($resultados) {
                $response->getBody()->write(json_encode($resultados));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(["mensaje" => "No se encontraron operaciones."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

}


?>