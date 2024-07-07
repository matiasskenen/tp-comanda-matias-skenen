<?php

class Menu{
    public $id;
    public $producto;

    public function __construct($id, $producto){
        $this->id = $id;
        $this->producto = $producto;
    }

    public static function nuevoMenu($id, $producto){
        $nuevoMenu = new Menu($id, $producto);
        return $nuevoMenu;
    }

    public function GetId(){
        return $this->id;
    }

    public function GetProducto(){
        return $this->producto;
    }

    public static function verificarMenu($valor)
    {
        switch($valor)
        {
            case '1':
                return "milanesaacaballo";
            break;
            case '2':
                return "hamburguesas";
            break;
            case '3':
                return "corona";
            break;
            case '4':
                return "daikiri";
            break;
            default:
                return false;
            break;
        }
    }

    public static function valorMenu($valor)
    {
        switch($valor)
        {
            case '1':
                return 500;
            break;
            case '2':
                return 300;
            break;
            case '3':
                return 300;
            break;
            case '4':
                return 300;
            break;
            default:
                return false;
            break;

        }
    }

    public static function puestoMenu($valor)
    {
        switch($valor)
        {
            case 'milanesaacaballo':
                return "cocinero";
            break;
            case "hamburguesas":
                return "cocinero";
            break;
            case "daikiri":
                return "bartender";
            break;
            case "corona":
                return "cervezero";
            break;
            default:
                return 'error';
        }
    }

}

?>