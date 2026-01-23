<?php

namespace App\Models;

class Producto {
    static private $tabla = "productos";

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;

    static function getTable()
    {
        return self::$tabla;
    }

    static public function convertirADolar($precio) {
        return round($precio / $_ENV["PRECIO_USD"]);
    }
}