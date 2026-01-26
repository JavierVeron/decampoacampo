<?php

namespace App\Controllers;

use App\Classes\Logger;
use Exception;
use App\Repositories\ProductoRepository;

class ProductoController {
    private $producto;

    public function __construct($db) {
        $this->producto = new ProductoRepository($db);
    }

    public function getAll() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $productos = $this->producto->getAll($page, $limit);
        http_response_code(200);
        echo json_encode($productos);
    }

    public function getById($id) {
        try {
            if (!is_numeric($id)) {
                throw new Exception("El parámetro 'id' debe ser un valor Numérico!");
            }
            
            $producto = $this->producto->getById($id);
            http_response_code($producto ? 200 : 422);
            echo json_encode($producto ? $producto : "No se encontró el Producto #$id!");
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode($e->getMessage());
        }
    }

    public function create() {
        try {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);

            if (!isset($data["nombre"])) {
                throw new Exception("Falta completar el parámetro 'nombre'!");
            } else if (!is_string($data["nombre"])) {
                throw new Exception("El parámetro 'nombre' debe ser un valor String!");
            }

            if (!isset($data["descripcion"])) {
                throw new Exception("Falta completar el parámetro 'descripcion'!");
            } else if (!is_string($data["descripcion"])) {
                throw new Exception("El parámetro 'descripcion' debe ser un valor String!");
            }

            if (!isset($data["precio"])) {
                throw new Exception("Falta completar el parámetro 'precio'!");
            } else if (!is_numeric($data["precio"])) {
                throw new Exception("El parámetro 'precio' debe ser un valor Numérico!");
            }

            $producto = $this->producto->create($data);
            http_response_code($producto ? 201 : 422);
            echo json_encode($producto ? "El producto se creó correctamente!" : "No se creó el Producto!");
            Logger::info("Se creó un nuevo Producto! => [" .implode(', ', $data) ."]");
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode($e->getMessage());
            Logger::error("Error en la creación del Producto => [" .implode(', ', $data) ."]");
        }
    }

    public function update($id) {
        try {
            if (!is_numeric($id)) {
                throw new Exception("El parámetro 'id' debe ser un valor Numérico!");
            }

            $json = file_get_contents("php://input");
            $data = json_decode($json, true);

            if (!isset($data["nombre"])) {
                throw new Exception("Falta completar el parámetro 'nombre'!");
            } else if (!is_string($data["nombre"])) {
                throw new Exception("El parámetro 'nombre' debe ser un valor String!");
            }

            if (!isset($data["descripcion"])) {
                throw new Exception("Falta completar el parámetro 'descripcion'!");
            } else if (!is_string($data["descripcion"])) {
                throw new Exception("El parámetro 'descripcion' debe ser un valor String!");
            }

            if (!isset($data["precio"])) {
                throw new Exception("Falta completar el parámetro 'precio'!");
            } else if (!is_numeric($data["precio"])) {
                throw new Exception("El parámetro 'precio' debe ser un valor Numérico!");
            }

            $resultado = $this->producto->update($id, $data);
            http_response_code($resultado ? 200 : 422);
            echo json_encode($resultado ? "El producto #$id se actualizó correctamente!" : "No se actualizó el Producto #$id!");
            Logger::info("Se editó el Producto #$id => [" .implode(', ', $data) ."]");
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode($e->getMessage());
            Logger::error("Error en la edición del Producto #$id => [" .implode(', ', $data) ."]");
        }
    }

    public function delete($id) {
        try {
            if (!is_numeric($id)) {
                throw new Exception("El parámetro 'id' debe ser un valor Numérico!");
            }

            $resultado = $this->producto->delete($id);
            http_response_code($resultado > 0 ? 200 : 422);
            echo json_encode($resultado > 0 ? "El producto #$id se eliminó correctamente!" : "No se eliminó el Producto #$id!");
            Logger::info("Se eliminó el Producto #$id");
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode($e->getMessage());
            Logger::error("Error en la eliminación del Producto #$id");
        }
    }
}