<?php


require_once '../models/encuestas.php';
require_once '../middlewares/UsuariosMiddleware.php';
require_once './interfaces/IApiUsable.php';


class EncuestaController extends Encuestas implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        
        $parametros = $request->getParsedBody();
        if (!isset($parametros['numero_mesa']) || empty($parametros["numero_mesa"])
        || !isset($parametros['nombre']) || empty($parametros["nombre"])
        || !isset($parametros['puntuacion']) || empty($parametros["puntuacion"])
        || !isset($parametros['codigo_pedido']) || empty($parametros["codigo_pedido"]))
        {
            
            $response->getBody()->write(json_encode(["error" => "completar campos"]));
            return $response->withHeader('Content-Type', 'application/json');
            
        }
        else
        {
            
            registrarOperacion("Cliente", "Na", "CargarEncuesta", $response);

            if(validarMesaPagada($parametros['numero_mesa']))
            {
                $fecha = new DateTime();
                $nuevaEncuesta = new Encuestas();
                $nuevaEncuesta->numero_mesa = $parametros['numero_mesa'];
                $nuevaEncuesta->nombre = $parametros['nombre'];
                $nuevaEncuesta->puntuacion = $parametros['puntuacion'];
                $nuevaEncuesta->codigo_pedido = $parametros['codigo_pedido'];
                $nuevaEncuesta->crearEncuesta($response);
            }
            else
            {
                $response->getBody()->write(json_encode(["error" => "Mesa no Pagada"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            
        }

        return $response->withHeader('Content-Type', 'application/json');
        
    }

    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        Encuestas::obtenerTodos($response);
        $mensaje = json_encode(array("mensaje" => "Orden Estado actualizada con exito")); 
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        parse_str(file_get_contents("php://input"), $parametros);

        if (!isset($parametros["puntuacion"]) || empty($parametros["puntuacion"])
        || !isset($parametros["codigo_pedido"]) || empty($parametros["codigo_pedido"]))
        {
            $response->getBody()->write(json_encode(["error" => "Completar puntuacion,codigo_pedido"]));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else 
        {
            Encuestas::modificarEncuesta($parametros['codigo_pedido'], $parametros['puntuacion'], $response);
        }

 
        registrarOperacion($nombre, $puesto, "ModificarComanda", $response);
        return $response->withHeader('Content-Type', 'application/json');
    }

    //Terminar
    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!isset($parametros['codigo_comanda']))
        {
            $response->getBody()->write(json_encode(["error" => "Completa campos"]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else
        {
            registrarOperacion("Matias", "Socio", "BorrarComanda", $response);
            Comandas::borrarComanda($parametros['codigo_comanda'], $response);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function GenerarPdf($request, $response, $args) 
    {
        $db = conectar();
        $consulta = "SELECT * FROM encuestas ORDER BY puntuacion DESC";
        $datos = $db->prepare($consulta);
        $datos->execute();
        $encuestas = $datos->fetchAll(PDO::FETCH_ASSOC);
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        
        foreach ($encuestas as $encuesta) {
            $pdf->MultiCell(0, 10, "ID Encuesta: {$encuesta['id_encuesta']}\n"
                                    . "Numero Mesa: {$encuesta['numero_mesa']}\n"
                                    . "Nombre: {$encuesta['nombre']}\n"
                                    . "Puntuacion: {$encuesta['puntuacion']}\n"
                                    . "Codigo Pedido: {$encuesta['codigo_pedido']}\n"
                                    . "Fecha: {$encuesta['fecha']}\n\n");
        }

        $pdfContent = $pdf->Output('S');

        $response->getBody()->write($pdfContent);
        return $response->withHeader('Content-Type', 'application/pdf')->withHeader('Content-Disposition', 'attachment; filename="encuestas.pdf"');
    }

}
?>
