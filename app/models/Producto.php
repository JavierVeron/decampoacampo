<?php

namespace App\Models;

class Producto {
    static private $table = "productos";
    static private $outputFields = ["id", "nombre", "descripcion", "precio", "precio_usd"];

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;

    static function getTable()
    {
        return self::$table;
    }

    static function outputFields()
    {
        return self::$outputFields;
    }

    static public function convertirPesosADolar($precio) {
        return round($precio / $_ENV["PRECIO_USD"]);
    }
}