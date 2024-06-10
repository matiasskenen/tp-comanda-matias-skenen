<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler; 
use Slim\Psr7\Response;


class authmiddllewares
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $params = $request->getParsedBody();
        $credenciales = $params["credenciales"];

        if(isset($credenciales))
        {
            if($credenciales == "admin")
            {        
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No sos admin")));

            }
        }
        else
        {
            // si no hay credenciales
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "No hay credenciales")));
        }


        return $response;
    }

    /*
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $params = $request->getParsedBody();

        if(isset($params["nombre"], $params["clave"]))
        {
            $response = $handler->handle($request);
        }else
        {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }
    */
}

?>