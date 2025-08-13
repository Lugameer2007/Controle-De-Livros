<?php

namespace Model;

use PDO;
use PDOException;

require_once __DIR__ . '/configuration.php'; // Corrigido o caminho

class Connection {
    public static function getConnection() {
        try {
            return new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log($e->getMessage);
            die("Erro de conexão: " . $e->getMessage());
        }
    }
}
?>