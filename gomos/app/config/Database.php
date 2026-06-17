<?php
namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe de conexão com o banco de dados utilizando o padrão Singleton e PDO.
 */
class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db_name = 'gomos';
    private $username = 'root';
    private $password = '';

    /**
     * Construtor privado para evitar instanciação direta.
     */
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Erro de Conexão: " . $e->getMessage());
        }
    }

    /**
     * Retorna a instância única da conexão com o banco de dados.
     * 
     * @return PDO
     */
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }
}
