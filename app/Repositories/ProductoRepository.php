<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\ProductoRepositoryInterface;
use App\Models\Producto;
use Exception;

class ProductoRepository implements ProductoRepositoryInterface
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }    

    public function getAll() {
        $query = "SELECT * FROM " .Producto::getTable();
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [];

        foreach ($productos as $producto) {
            $producto["precio"] = Producto::convertirADolar($producto["precio"]);
            array_push($resultado, $producto);
        }

        return $resultado;
    }

    public function getById(int $id)
    {
        try {
            $query = "SELECT * FROM " .Producto::getTable() ." WHERE id = " .$id;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (is_array($producto)) {
                $producto["precio"] = Producto::convertirADolar($producto["precio"]);
            }
            
            return $producto;
        } catch (Exception $e) {
            return null;
        }    
    }

    public function create(array $data)
    {
        $query = "INSERT INTO " .Producto::getTable() ." (nombre, descripcion, precio) VALUES (:nombre, :descripcion, :precio)";
        $stmt = $this->db->prepare($query);
        $nombre = htmlspecialchars(strip_tags($data['nombre']));
        $descripcion = htmlspecialchars(strip_tags($data['descripcion']));
        $precio = filter_var($data['precio'], FILTER_VALIDATE_FLOAT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function update(int $id, array $data)
    {
        $query = "UPDATE " .Producto::getTable() ." SET nombre = :nombre, descripcion = :descripcion, precio = :precio WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $nombre = htmlspecialchars(strip_tags($data['nombre']));
        $descripcion = htmlspecialchars(strip_tags($data['descripcion']));
        $precio = filter_var($data['precio'], FILTER_VALIDATE_FLOAT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_INT);
        $stmt->execute();
        $filas = $stmt->rowCount();
        
        return $filas;
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM " .Producto::getTable() ." WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $filas = $stmt->rowCount();
        
        return $filas;
    }
}