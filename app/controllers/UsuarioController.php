<?php
require_once '../models/usuario.php';
require_once './interfaces/IApiUsable.php';
require_once '../middlewares/AutenticacionJWT.php';


class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (!isset($parametros['usuario']) || empty($parametros["usuario"]) 
        || !isset($parametros['mail']) || empty($parametros["mail"]) 
        || !isset($parametros['clave']) || empty($parametros["clave"]) 
        || !isset($parametros['puesto']) || empty($parametros["puesto"]) 
        || !isset($parametros['estado']) || empty($parametros["estado"]) 
        || !isset($parametros['fecha_ingreso']) || empty($parametros["fecha_ingreso"]))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
        {
            registrarOperacion($parametros['usuario'], $parametros['puesto'], "Crear Usuario", $response);
            if (usuarioExiste($parametros['usuario'])) {
                $response->getBody()->write(json_encode(["error" => "El usuario ya existe en la base de datos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $Nuevoestado = Usuario::Estado($parametros['estado']);

            $fecha = new DateTime();
            $nuevoUsuario = new Usuario();
            $nuevoUsuario->nombre = $parametros['usuario'];
            $nuevoUsuario->mail = $parametros['mail'];
            $nuevoUsuario->clave = $parametros['clave'];
            $nuevoUsuario->puesto = $parametros['puesto'];

            $nuevoUsuario->estado = $Nuevoestado;
            $nuevoUsuario->fecha_ingreso = $fecha->format('d-m-Y'); 
            $nuevoUsuario->fecha_salida = '---';
            $nuevoUsuario->crearUsuario($response);

            

        }

        return $response->withHeader('Content-Type', 'application/json');

        
    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $mensaje = json_encode(array("lista usuarios" => $lista));

        $response->getBody()->write($mensaje);
        return $response
          ->withHeader('Content-Type', 'application/json');

        registrarOperacion($parametros['usuario'], $parametros['puesto'], "TraerUsuarios", $response);
    }
    
    public function ModificarUno($request, $response, $args)
    {

        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $parametros);

        $parametros = $request->getParsedBody();

        if (!isset($parametros['usuario']) || empty($parametros["usuario"]) || !isset($parametros['estado']) || empty($parametros["estado"])
        || !isset($parametros['clave']) || empty($parametros["clave"]) || !isset($parametros['puesto']) || empty($parametros["puesto"]))
        {
            $mensaje = json_encode(array("mensaje" => "Complete los campos"));
            $response->getBody()->write($mensaje);
            return $response->withHeader('Content-Type', 'application/json');
        }
        else
        {
            Usuario::modificarUsuario($id, $response);
        }

        return $response->withHeader('Content-Type', 'application/json');

        registrarOperacion($parametros['usuario'], $parametros['puesto'], "Modificar", $response);


    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!isset($parametros['usuario']))
        {
            $response->getBody()->write(json_encode(["error" => "Completa campos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }else
        {
            registrarOperacion("Matias", "Socio", "BorrarUsuario", $response);
            Usuario::borrarUsuario($parametros['usuario'], $response);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function LogIn($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();
        if (!isset($parametros['puesto']) || empty($parametros["puesto"]) 
        || !isset($parametros['usuario']) || empty($parametros["usuario"]) 
        || !isset($parametros['clave']) || empty($parametros["clave"]))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        if(!(validarLogin($parametros['usuario'], $parametros['puesto'], $parametros['clave'])))
        {
            $response->getBody()->write(json_encode(["error" => "El usuario/Clave no son correctos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        registrarOperacion($parametros['usuario'], $parametros['puesto'], "Login", $response);

        $datos = array("usuario" => $parametros['usuario'], "tipo_usuario" => $parametros['puesto']);
        try {
            $token = AutenticacionJWT::CrearToken($datos);
            $response->getBody()->write(json_encode(array("token" => $token)));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Error al generar el token: " . $e->getmensaje())));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json'); // Internal Server Error
        }



    }

    public function InsertarCSV($request, $response, $args)
    {
        $uploadedFiles = $request->getUploadedFiles();

        // Verificar que el archivo se haya subido
        if (isset($uploadedFiles['csv']) && $uploadedFiles['csv']->getError() === UPLOAD_ERR_OK) {
            $csvFile = $uploadedFiles['csv'];
            $csvFilePath = $csvFile->getStream()->getMetadata('uri');
            
            $datos = Usuario::procesarCSV($csvFilePath, $response);

            $response->getBody()->write(json_encode($datos));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else {
            $response->getBody()->write(json_encode(['error' => 'Error al subir el archivo CSV']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
    }
    
    public function ArchivoCSV($request, $response, $args)
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
}
