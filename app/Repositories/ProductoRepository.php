<?php

namespace App\Repositories;

use PDO;
use Exception;
use App\Models\Producto;
use App\Classes\CacheManager;
use App\Classes\Logger;
use App\Interfaces\ProductoRepositoryInterface;
const TTL = 3600;

class ProductoRepository implements ProductoRepositoryInterface
{
    private $db;
    private $cache;

    public function __construct($db) {
        $this->db = $db;
        $this->cache = CacheManager::getInstance();
    }    

    public function getAll(int $page = 1, int $limit = 10) {
        $offset = ($page - 1) * $limit;
        $cacheKey = "productos_p_#{$page}_l_#{$limit}";

        $resultado = $this->cache->remember($cacheKey, TTL, function() use ($limit, $offset) {
            $query = "SELECT * FROM " .Producto::getTable() ." LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arrayProductos = [];
            $total = $this->db->query("SELECT COUNT(*) FROM " .Producto::getTable())->fetchColumn();
    
            foreach ($productos as $producto) {
                $prod = $this->outputFields($producto);
                array_push($arrayProductos, $prod);
            }

            $currentPage = (int)$offset / $limit + 1;
            $totalPages = ceil($total / $limit);

            return [
                "data" => $arrayProductos,
                "pagination" => [
                    "totalItems" => (int)$total,
                    "currentPage" => $currentPage,
                    "prevPage" => $currentPage > 1 ? ($currentPage - 1) : null,
                    "nextPage" => $currentPage < $totalPages ? ($currentPage + 1) : null,
                    "totalPages" => $totalPages,
                    "limit" => (int)$limit
                ]
            ];
        });

        return $resultado;
    }

    public function getById(int $id)
    {
        try {
            $cacheKey = "producto_" . $id;

            $resultado = $this->cache->remember($cacheKey, TTL, function() use ($id) {
                $query = "SELECT * FROM " .Producto::getTable() ." WHERE id = " .$id;
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if (is_array($producto)) {
                    $producto = $this->outputFields($producto);
                }
                
                return $producto;
            });

            return $resultado;
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
        $resultado = $stmt->execute();
        $this->cache->flush();

        return $resultado;
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
        $resultado = $stmt->rowCount();

        if ($resultado) {
            $this->cache->flush();
        }

        Logger::info($resultado);
        
        return $resultado;
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM " .Producto::getTable() ." WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->rowCount();
        
        if ($resultado) {
            $this->cache->flush();
        }
        
        return $resultado;
    }

    public function outputFields(array $producto)
    {
        $prod = [];
        $campos = Producto::outputFields();
    
        foreach ($campos as $campo) {
            if ($campo == "precio_usd") {
                $prod[$campo] = Producto::convertirPesosADolar($producto["precio"]);
            } else {
                $prod[$campo] = $producto[$campo];
            }
        }

        return $prod;
    }
}