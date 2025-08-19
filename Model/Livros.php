<?php

namespace Model;

use PDO;
use Model\Connection;

class Livro {
    private $conn;

    public $id;
    public $name;
    public $category;

    public function __construct() {
        $this->conn = Connection::getConnection();
    }

    // READ - todos os livros
    public function getLivros(): array {
        $sql = "SELECT * FROM livros";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - livro por ID
    public function getLivroById(int $id): ?array {
        $sql = "SELECT * FROM livros WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // CREATE
    public function createLivro(): bool {
        $sql = "INSERT INTO livros (id, name, category) VALUES (:id, :name, :category)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":category", $this->category, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // UPDATE
    public function updateLivro(): bool {
        $sql = "UPDATE livros SET name = :name, category = :category WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":category", $this->category, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE
    public function deleteLivro(): bool {
        $sql = "DELETE FROM livros WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // PATCH - atualização parcial
    public function updateLivroParcial(): bool {
        $campos = [];
        $params = [];

        if ($this->name !== null) {
            $campos[] = "name = :name";
            $params[':name'] = $this->name;
        }

        if ($this->category !== null) {
            $campos[] = "category = :category";
            $params[':category'] = $this->category;
        }

        if (empty($campos)) {
            return false; // Nenhum campo fornecido para atualizar
        }

        $sql = "UPDATE livros SET " . implode(', ', $campos) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $params[':id'] = $this->id;

        return $stmt->execute($params);
    }
}

?>
