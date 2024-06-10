<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      //post
      $params = $request->getParsedBody();
    
      if (!isset($params['usuario']) || empty($params["usuario"]) || !isset($params['tipo']) || empty($params["tipo"]) 
      || !isset($params['clave']) || empty($params["clave"]) || !isset($params['nombre']) || empty($params["nombre"])
      || !isset($params['apellido']) || empty($params["apellido"]))
      {
          
          $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
          return $response->withHeader('Content-Type', 'application/json');
          
      }
      else
      {
          $db = conectar();
  
          if (usuarioExiste($params['usuario'], $db)) {
              $response->getBody()->write(json_encode(["error" => "El usuario ya existe en la base de datos"]));
              return $response->withHeader('Content-Type', 'application/json');
          }
  
          $consulta = "INSERT INTO usuarios (usuario, tipo, clave, nombre, apellido) 
          VALUES (:usuario, :tipo, :clave, :nombre, :apellido)";
      
          try
          {
              $insert = $db->prepare($consulta);
              $insert->bindParam(':usuario', $params['usuario']);
              $insert->bindParam(':tipo', $params['tipo']);
              $insert->bindParam(':clave', $params['clave']);
              $insert->bindParam(':nombre', $params['nombre']);
              $insert->bindParam(':apellido', $params['apellido']);
              $insert->execute();
              $response->getBody()->write(json_encode(["mensaje" => "Usuario agregado exitosamente"]));
  
          }
          catch (PDOException $exepcion)
          {
              $error = array("error" => $exepcion->getMessage());
              $response->getBody()->write(json_encode($error));
          }
      
          return $response->withHeader('Content-Type', 'application/json');
      }
    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        $db = conectar();
    
        $consulta = "SELECT * FROM usuarios";
    
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
    
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $params);
    
        if (!isset($params['usuario']) || empty($params["usuario"]) || !isset($params['tipo']) || empty($params["tipo"])
            || !isset($params['clave']) || empty($params["clave"]) || !isset($params['nombre']) || empty($params["nombre"])
            || !isset($params['apellido']) || empty($params["apellido"])) {
    
            $response->getBody()->write(json_encode(["error" => "Completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $db = conectar();
    
            // Se usa 'id_usuario' en lugar de 'id'
            $consulta = "UPDATE usuarios SET usuario = :usuario, tipo = :tipo, clave = :clave, nombre = :nombre, apellido = :apellido WHERE id_usuario = :id";
    
            try {
                $update = $db->prepare($consulta);
                $update->bindParam(':usuario', $params['usuario']);
                $update->bindParam(':tipo', $params['tipo']);
                $update->bindParam(':clave', $params['clave']);
                $update->bindParam(':nombre', $params['nombre']);
                $update->bindParam(':apellido', $params['apellido']);
                $update->bindParam(':id', $id);
                $update->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Usuario actualizado exitosamente"]));
            } catch (PDOException $exepcion) {
                $error = ["error" => $exepcion->getMessage()];
                $response->getBody()->write(json_encode($error));
            }
    
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function BorrarUno($request, $response, $args)
    {

    }
}
