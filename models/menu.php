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
                return "hamburguesa";
            break;
            case '2':
                return "empanadas";
            break;
            case '3':
                return "coca-cola";
            break;
            case '4':
                return "vino";
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
            case 'hamburguesa':
                return 500;
            break;
            case 'empanadas':
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
            case 'hamburguesa':
                return "cocinero";
            break;
            case "empanadas":
                return "cocinero";
            break;
            case "coca-cola":
                return "mesero";
            break;
            case "vino":
                return "cervezero";
            break;
            default:
                return 'error';
        }
    }

}

?>