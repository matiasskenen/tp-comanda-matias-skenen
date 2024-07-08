<?php


require_once '../models/empleados.php';
require_once '../middlewares/UsuariosMiddleware.php';
require_once './interfaces/IApiUsable.php';

class EmpleadosController extends Empleados implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        Empleados::obtenerOperacionesPorTipo('login', $response);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function cantidadOp($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        if(!(isset($parametros['operacion'])))
        {
            $response->getBody()->write(json_encode(array("error" => "No se ingreso codigo_comanda")));
            $response = $response->withStatus();
        }
        else
        {
            Empleados::cantidadOperaciones('login', $response);
            return $response->withHeader('Content-Type', 'application/json');
        }

    }
    
    public function OpSector($request, $response, $args)
    {
        Empleados::OpSectorempleado($response);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno($request, $response, $args)
    {
    }

    public function BorrarUno($request, $response, $args)
    {
    }

}
?>
