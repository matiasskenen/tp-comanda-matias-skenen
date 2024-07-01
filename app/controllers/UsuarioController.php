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
        || !isset($parametros['fecha_ingreso']) || empty($parametros["fecha_ingreso"])  
        || !isset($parametros['fecha_salida']) || empty($parametros["fecha_salida"]))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
        {
            if (usuarioExiste($parametros['usuario'])) {
                $response->getBody()->write(json_encode(["error" => "El usuario ya existe en la base de datos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $fecha = new DateTime();
            $nuevoUsuario = new Usuario();
            $nuevoUsuario->nombre = $parametros['usuario'];
            $nuevoUsuario->mail = $parametros['mail'];
            $nuevoUsuario->clave = $parametros['clave'];
            $nuevoUsuario->puesto = $parametros['puesto'];
            $nuevoUsuario->estado = $parametros['estado'];
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


    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $parametros);

        Usuario::borrarUsuario($id, $response);

        return $response->withHeader('Content-Type', 'application/json');


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

    public function GenerarCSV($request, $response, $args)
    {
        try
        {
            $lista = Usuario::obtenerTodos();
            $archivo = fopen('./csv/usuarios.csv', 'w');

            foreach($lista as $datos)
            {
                $fila = get_object_vars($datos);
                fputcsv($archivo, $fila);
            }
            fclose($archivo);

            $response->getBody()->write("Archivo guardado correctamente");
            //Entregar csv repo
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch(Exeption)
        {
            $response->getBody()->write("Error al guardar");
            return $response->withHeader('Content-Type', 'application/json');
        }
    
    }
}
