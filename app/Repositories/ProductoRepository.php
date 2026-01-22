<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\ProductoRepositoryInterface;
use App\Models\Producto;

class ProductoRepository implements ProductoRepositoryInterface
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }    

    public function getAll() {
        $query = "SELECT * FROM productos";
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
        $query = "SELECT * FROM productos WHERE id = " .$id;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        $producto["precio"] = Producto::convertirADolar($producto["precio"]);
        
        return $producto;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO productos (nombre, descripcion, precio) VALUES (:nombre, :descripcion, :precio)";
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
        $query = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio WHERE id = :id";
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
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $filas = $stmt->rowCount();
        
        return $filas;
    }
}