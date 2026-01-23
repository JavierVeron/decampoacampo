<?php

class Database {
    private static $instance = null;
    private $connection;

    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;

    private function __construct() {
        $this->loadEnv("../.env");
        $this->host = $_ENV["DB_HOST"];
        $this->db_name = $_ENV["DB_NAME"];
        $this->username = $_ENV["DB_USER"];
        $this->password = $_ENV["DB_PASSWORD"];
        $this->charset = "utf8mb4";
        
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->db_name;charset=$this->charset";
            $this->connection = new PDO($dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    private function loadEnv($path) {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            $_ENV[$name] = $value;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function __clone() { }

    public function __wakeup() {
        throw new Exception("No se puede deserializar una instancia de Singleton.");
    }
}