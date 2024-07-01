<?php

require_once '../db/conectarDB.php';

class Usuario{

    public $nombre;
    public $mail;
    public $clave;
    public $puesto;
    public $estado;
    public $idPuesto;
    public $idEstado;
    public $fecha_ingreso;
    public $fecha_salida;
    public $id_usuario;
    public $usuario;


    public function GetIdPuesto(){
        return $this->idPuesto;
    }
    public function GetPuesto(){
        return $this->puesto;
    }

    public function GetIdUsuario(){
        return $this->idUsuario;
    }

    public function crearUsuario($response)
    {
        //post
        $db = conectar();

        $insert = "INSERT INTO usuarios (usuario, puesto, clave, estado, mail, fecha_ingreso, fecha_salida) 
           VALUES (:usuario, :puesto, :clave, :estado, :mail, :fecha_ingreso, :fecha_salida)";

            try {
                $consulta = $db->prepare($insert);
                $consulta->bindValue(':usuario', $this->nombre);
                $consulta->bindValue(':mail', $this->mail);
                $consulta->bindValue(':clave', $this->clave);
                $consulta->bindValue(':puesto', $this->puesto);
                $consulta->bindValue(':estado', $this->estado);
                $consulta->bindValue(':fecha_ingreso', $this->fecha_ingreso);
                $consulta->bindValue(':fecha_salida', $this->fecha_salida);
                $consulta->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Usuario agregado exitosamente"]));

            } catch (PDOException $exepcion) {
                $error = array("error" => $exepcion->getmensaje());
                $response->getBody()->write(json_encode($error));
            }

            return $response->withHeader('Content-Type', 'application/json');
    }

    public static function modificarUsuario($id, $response)
    {
        parse_str(file_get_contents("php://input"), $parametros);
        $db = conectar();


            $consulta = "UPDATE usuarios SET usuario = :usuario, puesto = :puesto, clave = :clave, estado = :estado WHERE id_usuario = :id";

            try {
                $update = $db->prepare($consulta);
                $update->bindParam(':usuario', $parametros['usuario']);
                $update->bindParam(':puesto', $parametros['puesto']);
                $update->bindParam(':clave', $parametros['clave']);
                $update->bindParam(':estado', $parametros['estado']);
                $update->bindParam(':id', $id);
                $update->execute();
                $response->getBody()->write(json_encode(["mensaje" => "Usuario actualizado exitosamente"]));

            } catch (PDOException $exepcion) {
                $error = ["error" => $exepcion->getmensaje()];
                $response->getBody()->write(json_encode($error));
            }

            return $response->withHeader('Content-Type', 'application/json');
        
    }

    public static function borrarUsuario($usuario, $response)
    {

        try {
            $db = conectar(); 
            
            $consulta = "DELETE FROM usuarios WHERE usuario = :usuario";
            $datos = $db->prepare($consulta);
            $datos->bindParam(':usuario', $usuario);
            $datos->execute();

            $rowCount = $datos->rowCount(); // numero de filas afectadas
            
            if ($rowCount > 0) {

                $response->getBody()->write(json_encode(["mensaje" => "Usuario eliminado correctamente."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(["mensaje" => "No se encontró ningún usuario con el ID especificado."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

    }

    public static function obtenerTodos()
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
            $error = array("error" => $exepcion->getmensaje());
            $response->getBody()->write(json_encode($error));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Puesto($valor)
    {
        switch($valor)
        {
            case 'Socio':
            case 'Bartender':
            case 'Cervecero':
            case 'Cocinero':
            case 'Mozo':
                return true;
            break;
            default:
                return false;
            break;

        }
    }

    public static function Estado($valor)
    {
        switch($valor)
        {
            case "Disponible":
            case "Suspendido":
            case "Baja":
                return true;
            break;
            default:
                return false;
            break;

        }
    }

    public static function validarEstado($puesto, $estado)
    {
        if(Puesto($puesto) &&  Estado($estado))
        {
            return true;
        }
    
    }

    //Hacer un metodo para consultar el id de usuario.

    public static function obtenerUsuarioId($usuario)
    {

        $db = conectar();

        // Se usa 'id_usuario' en lugar de 'id'
        $consulta = "UPDATE usuarios SET usuario = :usuario, tipo = :tipo, clave = :clave, nombre = :nombre, apellido = :apellido 
        WHERE id_usuario = :id";
    }

    public static function ObtenerUsuarioLogIn($id_usuario)
    {
        $db = conectar();
    
        $consulta = "SELECT usuario, puesto, clave, estado, mail, fecha_ingreso, fecha_salida 
                     FROM usuarios 
                     WHERE id_usuario = :id_usuario";
    
        try {
            $datos = $db->prepare($consulta);
            
            $datos->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            
            $datos->execute();
    
            return $datos->fetchObject('Usuario');
            
        } catch (PDOException $ex) {
            $error = ["error" => $ex->getmensaje()];
            return $error;
        }
    }

    

}