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
        parse_str(Filee_get_contents("php://input"), $parametros);
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

            $rowCount = $datos->rowCount(); // numero de Fileas afectadas
            
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

    public static function descargarArchivoCSV()
    {
        $filename = "usuarios.csv";
        $delimiter = ",";

        $f = fopen('php://memory', 'w');

        // Encabezados de las columnas
        $fields = array('id_usuario', 'usuario', 'puesto', 'clave', 'estado', 'mail', 'fecha_ingreso', 'fecha_salida');
        fputcsv($f, $fields, $delimiter);

        $db = conectar();
        $query = "SELECT id_usuario, usuario, puesto, clave, estado, mail, fecha_ingreso, fecha_salida FROM usuarios";
        foreach ($db->query($query) as $row) {
            fputcsv($f, $row, $delimiter);
        }

        fseek($f, 0);

        return $response->withHeader('Content-Type', 'text/csv')
                        ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '";')
                        ->withBody(new \Slim\Psr7\Stream($f));

    }
  
    public static function procesarCSV($File, $response)
    {
        $db = conectar();

        $consulta = "INSERT INTO usuarios (usuario, puesto, clave, estado, mail, fecha_ingreso, fecha_salida) 
                     VALUES (:usuario, :puesto, :clave, :estado, :mail, :fecha_ingreso, :fecha_salida)";

        $insert = $db->prepare($consulta);

        if (($handle = fopen($File, "r")) !== FALSE) {
            fgetcsv($handle);
            

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
            {
                $insert->bindValue(':usuario', $data[0]);
                $insert->bindValue(':puesto', $data[1]);
                $insert->bindValue(':clave', password_hash($data[2], PASSWORD_DEFAULT)); 
                $insert->bindValue(':estado', $data[3]);
                $insert->bindValue(':mail', $data[4]);
                $insert->bindValue(':fecha_ingreso', $data[5]);
                $insert->bindValue(':fecha_salida', $data[6]);
                $insert->execute(); 
            }

            $response->getBody()->write(json_encode(['Mensaje' => 'Datos ingresados']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);

            fclose($handle);
        }
    }


    

}