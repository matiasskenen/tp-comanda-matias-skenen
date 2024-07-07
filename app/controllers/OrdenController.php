<?php
require_once '../models/menu.php';
require_once '../models/orden.php';
require_once './interfaces/IApiUsable.php';

class OrdenController extends Orden implements IApiUsable{
   
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        registrarOperacion($parametros['usuario'], $parametros['puesto'], "Modificar", $response);

        if(isset($parametros['codigo_comanda']) && isset($parametros['pedido']))
        {
            
            $menuValidate = Menu::verificarMenu($parametros['pedido']);

            if($menuValidate != 'error' && !empty($parametros['pedido']))
            {       
                $ord = new Orden();
                $ord->codigo_comanda = $parametros['codigo_comanda'];
                $ord->pedido = $parametros['pedido'];
                $ord->area = Menu::puestoMenu($parametros['pedido']); 
                $ord->estado = "ingreso";
                // var_dump($ord);
                $ord->crearOrden();
                
                $mensaje = json_encode(array("mensaje" => "Orden creada con exito"));
                
            }
            else{
                $mensaje = json_encode(array("mensaje" => "El pedido no esta en el menu o la comanda no existe"));
            }

        }else 
        {
            $mensaje = json_encode(array("mensaje" => "Datos invalidos"));
        }
        
        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
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
            Orden::obtenerTodos($puesto, $response);      
        }

        ;
    
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {   
    }

    public function ModificarUno($request, $response, $args)
    {
        parse_str(file_get_contents("php://input"), $parametros);

        if(isset($parametros['codigo_comanda']) && isset($parametros['pedido']))
        {
            $menuValidate = Menu::verificarMenu($parametros['pedido']);

            if($menuValidate != 'error' && !empty($parametros['pedido']))
            {
                Orden::ModificarOrdenPedido($parametros['codigo_comanda'], $parametros['pedido']);
                $mensaje = json_encode(array("mensaje" => "Orden Pedido actualizada con exito"));
            }else
            {
                $mensaje = json_encode(array("mensaje" => "No hay una orden con ese pedido o el pedido no esta en el menu"));
            }
        }
        else
        {
            $mensaje = json_encode(array("mensaje" => "Datos invalidos"));
        }
        
        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstado($request, $response, $args)
    {
        parse_str(file_get_contents("php://input"), $parametros);

        if(isset($parametros['codigo_comanda']) && isset($parametros['estado']) && isset($parametros['demora']))
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
                Orden::ModificarOrdenEstado($parametros['codigo_comanda'], $parametros['estado'], $puesto, $parametros['demora']);
                $numeroMesaComanda = Orden::obtenerMesaPorCodigoComanda($parametros['codigo_comanda']);

                if($parametros['estado'] == "lista")
                {
                    $demoraRestar = Orden::obtenerDemoraPorCodigoComanda($parametros['codigo_comanda']);
                    Orden::restarDemoraPorIdMesa($demoraRestar, $numeroMesaComanda);

                }else
                {
                    Orden::ActualizarComanda($parametros['demora'], $numeroMesaComanda);
                }
                

                $mensaje = json_encode(array("mensaje" => "Orden Estado actualizada con exito"));        
            }
        }
        else
        {
            $mensaje = json_encode(array("mensaje" => "Datos invalidos"));
        }

        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    { 
    }


}

?>