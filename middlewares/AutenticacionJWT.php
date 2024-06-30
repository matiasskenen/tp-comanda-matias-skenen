<?php
use Firebase\JWT\JWT;

class AutenticacionJWT{

    private static $claveSecreta = '1234';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos){

        $ahora = time();
        $payload = array(
                    'iat' => $ahora,
                    'exp' => $ahora + (60000),
                    'data' => $datos, 
                    'app' => "La Comanda - JC"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token){
       // var_dump($token);
        if(empty($token) || $token==""){
            throw new Exception("El token esta vacio");
        }
        else {
            try {
                $decodificado = JWT::decode($token, self::$claveSecreta, 
                                            self::$tipoEncriptacion);
    
            } catch (Exception $e) {
                throw $e;
            }
        }
        return $decodificado;
    }

    public static function ObtenerPayload($token){
        if(empty($token) || $token==""){
            throw new Exception("El token esta vacio");
        }
        return JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion);
    }
    
    public static function ObtenerData($token)
    {
        return JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion)->data;
    }


}
?>