<?php


require_once '../models/comanda.php';
require_once '../middlewares/UsuariosMiddleware.php';
require_once './interfaces/IApiUsable.php';

class ComandaController extends Comandas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        
        $parametros = $request->getParsedBody();
        if (!isset($parametros['id_mesa']) || empty($parametros["id_mesa"]) 
        || !isset($parametros['cliente']) || empty($parametros["cliente"]) 
        || !isset($parametros['estado']) || empty($parametros["estado"]) 
        || !isset($parametros['productos']) || empty($parametros["productos"]))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
        {
            $header = $request->getHeaderLine('authorization'); 
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutenticacionJWT::ObtenerData($token);
            $puesto = $data->tipo_usuario;
            $nombre = $data->usuario;

            if($puesto == "mesero" || $puesto == "socio")
            {
                if (comandaExiste($parametros['id_mesa'], $parametros['cliente']))
            {
                $response->getBody()->write(json_encode(["error" => "La comanda ya existe en la base de datos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }


                registrarOperacion($nombre, $puesto, "CargarComanda", $response);

                $fecha = new DateTime();
                $nuevoUsuario = new Comandas();
                $nuevoUsuario->id_mesa = $parametros['id_mesa'];
                $nuevoUsuario->cliente = $parametros['cliente'];
                $nuevoUsuario->estado = $parametros['estado'];
                $nuevoUsuario->productos = $parametros['productos'];
                $nuevoUsuario->fecha = $fecha->format('d-m-Y'); 
                $nuevoUsuario->crearComanda($response);
            }
        }

        return $response->withHeader('Content-Type', 'application/json');
        
    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $header = $request->getHeaderLine('authorization'); 
        if(empty($header))
        {
            if(!(isset($parametros['codigo_comanda'])))
            {
                $response->getBody()->write(json_encode(array("error" => "No se ingreso codigo_comanda")));
                $response = $response->withStatus();
            }
            else
            {
                Comandas::obtenerTodos($parametros['codigo_comanda'], $response);
            }
        }
        else
        {
            // Si sos admin, Trae Todo
            $db = conectar();

            $token = trim(explode("Bearer", $header)[1]);
            
            $data = AutenticacionJWT::ObtenerData($token);
            $puesto = $data->tipo_usuario;
            if($puesto == "socio")
            {
                $consulta = "SELECT * FROM comandas";

                $insert = $db->query($consulta);
                $usuarios = $insert->fetchAll();
        
                if (count($usuarios) > 0) 
                {
                    $response->getBody()->write(json_encode($usuarios));
                } 
                else 
                {
                    $mensaje = array("mensaje" => "No se encontraron registros en la tabla.");
                    $response->getBody()->write(json_encode($mensaje));
                }

            }
        }
            

        $mensaje = json_encode(array("mensaje" => "Orden Estado actualizada con exito")); 
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $header = $request->getHeaderLine('authorization'); 
        $token = trim(explode("Bearer", $header)[1]);
        
        $data = AutenticacionJWT::ObtenerData($token);
        $puesto = $data->tipo_usuario;
        $nombre = $data->usuario;

        if($puesto == "mesero" || $puesto == "socio")
        {
            $id = $args['id'];
            parse_str(file_get_contents("php://input"), $parametros);

            if (!isset($parametros["estado"]) || empty($parametros["estado"]))
            {
                $response->getBody()->write(json_encode(["error" => "Completar [estado]"]));
                return $response->withHeader('Content-Type', 'application/json');
            } 
            else 
            {
                registrarOperacion($nombre, $puesto, "ModificarComanda", $response);
                Comandas::modificarComanda($id, $parametros['estado']);
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    //Terminar
    public function BorrarUno($request, $response, $args)
    {
        $header = $request->getHeaderLine('authorization'); 
        $token = trim(explode("Bearer", $header)[1]);
        
        $data = AutenticacionJWT::ObtenerData($token);
        $puesto = $data->tipo_usuario;
        $nombre = $data->usuario;

        $parametros = $request->getParsedBody();

        if(!isset($parametros['codigo_comanda']))
        {
            $response->getBody()->write(json_encode(["error" => "Completa campos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else
        {
            registrarOperacion($nombre, $puesto, "BorrarComanda", $response);
            Comandas::borrarComanda($parametros['codigo_comanda'], $response);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}
?>
