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
        || !isset($parametros['fecha_salida']) || empty($parametros["fecha_salida"])  
        || !isset($parametros['credenciales']) || empty($parametros["credenciales"])  )
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
        {
            $db = conectar();
    
            if (usuarioExiste($parametros['usuario'], $db)) {
                $response->getBody()->write(json_encode(["error" => "El usuario ya existe en la base de datos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
            /*
            if(!(usuario::validarEstado($puesto, $estado)))
            {
                $response->getBody()->write(json_encode(["error" => "El puesto o El estado no existe"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
                */

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
    
        if (isset($parametros['id_usuario']) && isset($parametros['nombre']) && isset($parametros['puesto'])) 
        {
            $datos = array("usuario" => $parametros['nombre'], "tipo_usuario" => $parametros['puesto']);

            try {
                var_dump($datos);
                $token = AutenticacionJWT::CrearToken($datos);
                $response->getBody()->write(json_encode(array("token" => $token)));
                return $response->withHeader('Content-Type', 'application/json');
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(array("error" => "Error al generar el token: " . $e->getMessage())));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json'); // Internal Server Error
            }
        } 
        else {
            $response->getBody()->write(json_encode(array("error" => "Los datos ingresados son invalidos")));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
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
