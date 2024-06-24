<?php

require_once '../models/mesa.php';
require_once './interfaces/IApiUsable.php';

class MesasController extends Mesas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $mesasMaximas = 5;
        $parametros = $request->getParsedBody();
        if (!isset($parametros['max_comensales']) || empty($parametros["max_comensales"]) 
        || !isset($parametros['codigo_comanda']) || empty($parametros["codigo_comanda"]) 
        || !isset($parametros['estado']) || empty($parametros["estado"]) 
        || !isset($parametros['mozo']) || empty($parametros["mozo"]) 
        || !isset($parametros['foto']) || empty($parametros["foto"]))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar todos los campos [usuario][clave][tipo][nombre][apellido]"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
        {
            $db = conectar();
            
            /*
            VALIDAR MESA.
            if (usuarioExiste($parametros['usuario'], $db)) {
                $response->getBody()->write(json_encode(["error" => "La mesa ya existe en la base de datos"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
            */

            $fecha = new DateTime();
            $nuevaMesa = new Mesas();
            $nuevaMesa->max_comensales = $parametros['max_comensales'];
            $nuevaMesa->codigo_comanda = $parametros['codigo_comanda'];
            $nuevaMesa->estado = $parametros['estado'];
            $nuevaMesa->mozo = $parametros['mozo'];
            $nuevaMesa->foto = $parametros['foto']; 
            $nuevaMesa->crearMesa($response);

        }
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesas::obtenerTodos();
        $mensaje = json_encode(array("lista mesas" => $lista));

        $response->getBody()->write($mensaje);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        parse_str(file_get_contents("php://input"), $parametros);

        if (!isset($parametros["max_comensales"]) || empty($parametros["max_comensales"])
        || !isset($parametros["estado"]) || empty($parametros["estado"])
        || !isset($parametros["codigo_comanda"]) || empty($parametros["codigo_comanda"]))
        {
            $response->getBody()->write(json_encode(["error" => "Completar la cantidad de maxima de comensales: [max_comensales]"]));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else 
        {
            $db = conectar();

            // Se usa 'id_usuario' en lugar de 'id'
            Mesas::modificarMesa($id, $parametros['codigo_comanda'], $parametros['estado'], $parametros['max_comensales'], $response);
            $mensaje = json_encode(array("mensaje" => "Estado de mesa modificado exitosamente"));

        }

        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigo_mesa = $args['codigo_mesa'];
        $mesa = Mesa::obtenerMesaCodigo($codigo_mesa);

        if(!isset($codigo_mesa) || empty($mesa)){
           
            $mensaje = json_encode(array("mensaje" => "Datos invalidos"));
        }
        else{
            Mesas::borrarMesa($codigo_mesa);
            $mensaje = json_encode(array("mensaje" => "Mesa borrada con exito"));
        }

        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
