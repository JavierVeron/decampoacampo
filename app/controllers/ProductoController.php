<?php

namespace App\Controllers;

use App\Repositories\ProductoRepository;

class ProductoController {
    private $producto;

    public function __construct($db) {
        $this->producto = new ProductoRepository($db);
    }

    public function getAll() {
        $productos = $this->producto->getAll();
        http_response_code(200);
        echo json_encode($productos);
    }

    public function getById($id) {
        $producto = $this->producto->getById($id);
        http_response_code($producto ? 200 : 422);
        echo json_encode($producto ? $producto : "No se encontró el Producto #$id!");
    }

    public function create() {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $producto = $this->producto->create($data);
        http_response_code($producto ? 201 : 422);
        echo json_encode($producto ? "El producto se creó correctamente!" : "No se pudo crear el Producto!");
    }

    public function update($id) {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $resultado = $this->producto->update($id, $data);
        http_response_code($resultado > 0 ? 200 : 422);
        echo json_encode($resultado > 0 ? "El producto #$id se actualizó correctamente!" : "No se pudo actualizar el Producto #$id!");
    }

    public function delete($id) {
        $resultado = $this->producto->delete($id);
        http_response_code($resultado > 0 ? 200 : 422);
        echo json_encode($resultado > 0 ? "El producto #$id se eliminó correctamente!" : "No se pudo eliminar el Producto #$id!");
    }
}