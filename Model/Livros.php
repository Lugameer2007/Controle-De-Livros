<?php

namespace Model;

use PDO;
use Model\Connection;

class Livro {
    private $conn;

    public $id;
    public $name;
    public $category;  // corrigido ponto e vírgula

    public function __construct() {
        $this->conn = Connection::getConnection();
    }

    public function getLivros(): array {
        $sql = "SELECT * FROM livros"; // mantendo lowercase para consistência
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLivroById(int $id): ?array {
        $sql = "SELECT * FROM livros WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createLivro(): bool {
        $sql = "INSERT INTO livros (id, name, category) VALUES (:id, :name, :category)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":category", $this->category, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function updateLivro(): bool {
        $sql = "UPDATE livros SET name = :name, category = :category WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":category", $this->category, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteLivro(): bool {
        $sql = "DELETE FROM livros WHERE id = :id"; // corrigido o ponto e vírgula
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>