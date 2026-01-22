<?php

namespace App\Models;

class Producto {
    private $tabla = "productos";

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;

    /* static function getTable()
    {
        return $this->tabla;
    } */

    static public function convertirADolar($precio) {
        return round($precio / $_ENV["PRECIO_USD"]);
    }
}