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

    public static function verificarMenu($valor){
        switch($valor)
        {
            case 'hamburguesa':
            break;
            case 'empanadas':
            break;
            default:
                return 'error';

        }
    }

    public static function puestoMenu($valor){
        switch($valor)
        {
            case 'Hamburguesa':
            default:
                return 'error';

        }
    }

}

?>