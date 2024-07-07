<?php

require_once '../models/mesa.php';
require_once './interfaces/IApiUsable.php';

class MesasController extends Mesas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (!isset($parametros['max_comensales']) || empty($parametros["max_comensales"]) 
        || !isset($parametros['numero_mesa']) || empty($parametros["numero_mesa"]) 
        || !isset($parametros['estado']) || empty($parametros["estado"]) 
        || !isset($parametros['mozo']) || empty($parametros["mozo"]) 
        || !isset($_FILES['imagen']))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [max_comensales][codigo_comanda][estado][mozo][imagen]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
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
            
                    if(mesasExiste($parametros['max_comensales'], $parametros['numero_mesa']))
                    {
                        $response->getBody()->write(json_encode(["error" => "La mesa ya existe en la base de datos"]));
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                    
                    if(!(mozoExiste($parametros['mozo'])))
                    {
                        $response->getBody()->write(json_encode(["error" => "El mesero no existe"]));
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                    

                    $fecha = new DateTime();
                    $nuevaMesa = new Mesas();
                    $nuevaMesa->max_comensales = $parametros['max_comensales'];
                    $nuevaMesa->numero_mesa = $parametros['numero_mesa'];
                    $nuevaMesa->estado = $parametros['estado'];
                    $nuevaMesa->mozo = $parametros['mozo'];
                    $nuevaMesa->crearMesa($response);
                    Mesas::ingresarVentaImagen($parametros['mozo'], $response);
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
                    $lista = Mesas::obtenerTodos($response);
                    $mensaje = json_encode(array("lista mesas" => $lista));
            
                    $response->getBody()->write($mensaje);
                    return $response->withHeader('Content-Type', 'application/json');
                }
        }
        return $response->withHeader('Content-Type', 'application/json');


    }
    
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $parametros);

        if (!isset($parametros["max_comensales"]) || empty($parametros["max_comensales"])
        || !isset($parametros["estado"]) || empty($parametros["estado"]))
        {
            $response->getBody()->write(json_encode(["error" => "Completar la cantidad de maxima de comensales: [max_comensales]"]));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else 
        {
            $db = conectar();

            Mesas::modificarMesa($id, $parametros['estado'], $parametros['max_comensales'], $response);
            $mensaje = json_encode(array("mensaje" => "Estado de mesa modificado exitosamente"));

        }

        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!isset($parametros['codigo_mesa']))
        {
            $response->getBody()->write(json_encode(["error" => "Completa campos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else
        {
            registrarOperacion("Matias", "Socio", "BorrarMesa", $response);
            Mesas::borrarMesa($parametros['codigo_mesa'], $response);
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
