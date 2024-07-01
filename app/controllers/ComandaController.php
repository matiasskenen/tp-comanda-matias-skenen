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
            
            if (comandaExiste($parametros['id_mesa'], $parametros['cliente']))
            {
                $response->getBody()->write(json_encode(["error" => "La comanda ya existe en la base de datos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $header = $request->getHeaderLine('authorization'); 
    
            if(empty($header))
            {
                $response->getBody()->write(json_encode(array("error" => "No se ingreso el token")));
                $response = $response->withStatus(401);
            }
            else
            {
                $token = trim(explode("Bearer", $header)[1]);
                
                $data = AutenticacionJWT::ObtenerData($token);
                $puesto = $data->tipo_usuario;
                $nombre = $data->usuario;

                if($puesto == "mesero" || $puesto == "socio")
                {
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
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "Tiene que ser mesero para registrar/Socio")));
                    $response = $response->withStatus(401);
                }


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
                $response = $response->withStatus(401);
            }
            else
            {
                Comandas::obtenerTodos($parametros['codigo_comanda'], $response);
            }
        }
        else
        {
            $db = conectar();

            $token = trim(explode("Bearer", $header)[1]);
            
            $data = AutenticacionJWT::ObtenerData($token);
            $puesto = $data->tipo_usuario;
            if($puesto == "socio")
            {
                $consulta = "SELECT * FROM comandas";
    
                try {
                    $insert = $db->query($consulta);
                    $usuarios = $insert->fetchAll(PDO::FETCH_ASSOC);
            
                    if (count($usuarios) > 0) {
                        $response->getBody()->write(json_encode($usuarios));
                    } else {
                        $mensaje = array("mensaje" => "No se encontraron registros en la tabla.");
                        $response->getBody()->write(json_encode($mensaje));
                    }
                } catch (PDOException $excepcion) {
                    $error = array("error" => $excepcion->getmensaje());
                    $response->getBody()->write(json_encode($error));
                }
            }
        }
            

        $mensaje = json_encode(array("mensaje" => "Orden Estado actualizada con exito")); 
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function ModificarUno($request, $response, $args)
    {
        $header = $request->getHeaderLine('authorization'); 
    
        if(empty($header))
        {
            $response->getBody()->write(json_encode(array("error" => "No se ingreso el token")));
            $response = $response->withStatus(401);
        }
        else
        {
                $token = trim(explode("Bearer", $header)[1]);
                
                $data = AutenticacionJWT::ObtenerData($token);
                $puesto = $data->tipo_usuario;
                $nombre = $data->usuario;

                if($puesto == "mesero" || $puesto == "socio")
                {
                    registrarOperacion($nombre, $puesto, "ModificarComanda", $response);

                    $id = $args['id'];
                    parse_str(file_get_contents("php://input"), $parametros);
        
                    if (!isset($parametros["estado"]) || empty($parametros["estado"]))
                    {
                        $response->getBody()->write(json_encode(["error" => "Completar [estado]"]));
                        return $response->withHeader('Content-Type', 'application/json');
                    } 
                    else 
                    {
                        $db = conectar();
        
                        Comandas::modificarComanda($id, $parametros['estado']);
                        $mensaje = json_encode(array("mensaje" => "Estado de Comanda modificado exitosamente"));
        
                    }
        
                    $response->getBody()->write($mensaje);
                }
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "Tiene que ser mesero para registrar/Socio")));
                    $response = $response->withStatus(401);
                }


        }
        return $response->withHeader('Content-Type', 'application/json');
    }


    //Terminar
    public function BorrarUno($request, $response, $args)
    {

        $header = $request->getHeaderLine('authorization'); 
    
        if(empty($header))
        {
            $response->getBody()->write(json_encode(array("error" => "No se ingreso el token")));
            $response = $response->withStatus(401);
        }
        else
        {
                $token = trim(explode("Bearer", $header)[1]);
                
                $data = AutenticacionJWT::ObtenerData($token);
                $puesto = $data->tipo_usuario;
                $nombre = $data->usuario;

                if($puesto == "mesero" || $puesto == "socio")
                {
                    registrarOperacion($nombre, $puesto, "BorrarComanda", $response);

                    $id_comanda = $args['id_comanda'];

                    if(!isset($id_comanda) || empty($estado)){
                       
                        $mensaje = json_encode(array("mensaje" => "Datos invalidos"));
                    }
                    else{
                        Comandas::borrarComanda($id_comanda);
                        $mensaje = json_encode(array("mensaje" => "Comanda borrada con exito"));
                    }
            
                    $response->getBody()->write($mensaje);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "Tiene que ser mesero para registrar/Socio")));
                    $response = $response->withStatus(401);
                }


        }
    }
}
?>
