
<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once 'AutenticacionJWT.php';

class UsuariosMiddleware{


    public function VerificaAccesoSocio(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('authorization'); 
        $response = new Response();

        if(empty($header)){
            $response->getBody()->write(json_encode(array("error" => "No se ingreso el token")));
            $response = $response->withStatus(401);
        }
        else{
            $token = trim(explode("Bearer", $header)[1]);
           // var_dump('Lo que hay en token (verifica socio func: ',$token);
            if(AutenticacionJWT::VerificarToken($token)){
                $data = AutenticacionJWT::ObtenerData($token);
                if($data->tipo_usuario == "socio")
                    $response = $handler->handle($request);
                else {
                    $response->getBody()->write(json_encode(array("error" => "No tiene acceso, no es socio")));
                    $response = $response->withStatus(401);
                }
            }

        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificaAccesoMeseroYSocio(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('authorization'); 
        $response = new Response();

        if(empty($header)){
            $response->getBody()->write(json_encode(array("error" => "No se ingreso el token")));
            $response = $response->withStatus(401);
        }
        else{
            $token = trim(explode("Bearer", $header)[1]);
            
            if(AutenticacionJWT::VerificarToken($token)){
                $data = AutenticacionJWT::ObtenerData($token);
                if($data->tipo_usuario == "mesero" || $data->tipo_usuario == "socio")
                    $response = $handler->handle($request);
                else {
                    $response->getBody()->write(json_encode(array("error" => "No tiene acceso, no es mozo")));
                    $response = $response->withStatus(401);
                }
            }

            
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

}


?>