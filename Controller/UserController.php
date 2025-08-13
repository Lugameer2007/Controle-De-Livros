<?php

namespace Controller;

use Model\Livro;
use Exception;

class UserController {
    private const TOKEN = 'Bearer';
    private string $envToken;

    public function __construct() {
        $this->envToken = getenv('TOKEN') ?: 'DEFAULT_TOKEN';
    }

    private function authorize(): bool {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        return $authHeader === self::TOKEN . ' ' . $this->envToken;
    }

    public function processRequest(): void {
        if (!$this->authorize()) {
            header('Content-Type: application/json', true, 401);
            echo json_encode(['mensagem' => 'Não autorizado']);
            return;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
                if ($id) {
                    $this->getLivroById($id);
                } else {
                    $this->getLivros();
                }
                break;

            case 'POST':
                $this->createLivro();
                break;

            case 'PUT':
            case 'PATCH':
                $this->updateLivro();
                break;

            case 'DELETE':
                $this->deleteLivro();
                break;

            default:
                header('Content-Type: application/json', true, 405);
                echo json_encode(['mensagem' => 'Método não permitido']);
        }
    }

    public function getLivros(): void {
        try {
            $livro = new Livro();
            $livros = $livro->getLivros();

            if ($livros) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($livros);
            } else {
                header('Content-Type: application/json', true, 404);
                echo json_encode(["mensagem" => "Livros não encontrados"]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(["mensagem" => "Erro interno ao buscar livros"]);
        }
    }

    public function getLivroById($id): void {
        try {
            $livro = new Livro();
            $livro->id = $id;
            $result = $livro->getLivroById();

            if ($result) {
                header('Content-Type: application/json', true, 200);
                echo json_encode($result);
            } else {
                header('Content-Type: application/json', true, 404);
                echo json_encode(["mensagem" => "Livro não foi encontrado"]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(["mensagem" => "Erro interno ao buscar o livro"]);
        }
    }

    public function createLivro(): void {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id, $data->name, $data->category)) {
            $id = filter_var($data->id, FILTER_VALIDATE_INT);
            $name = filter_var($data->name, FILTER_SANITIZE_STRING);
            $category = filter_var($data->category, FILTER_SANITIZE_STRING);

            if ($id && $name && $category) {
                try {
                    $livro = new Livro();
                    $livro->id = $id;
                    $livro->name = $name;
                    $livro->category = $category;

                    if ($livro->createLivro()) {
                        header('Content-Type: application/json', true, 201);
                        echo json_encode(["mensagem" => "Livro adicionado com sucesso!"]);
                    } else {
                        header('Content-Type: application/json', true, 500);
                        echo json_encode(["mensagem" => "Erro ao criar o livro!"]);
                    }
                } catch (Exception $e) {
                    header('Content-Type: application/json', true, 500);
                    echo json_encode(["mensagem" => "Erro interno ao criar o livro"]);
                }
            } else {
                header('Content-Type: application/json', true, 400);
                echo json_encode(["mensagem" => "Dados inválidos ou mal formatados!"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["mensagem" => "Entrada inválida!"]);
        }
    }

    public function updateLivro(): void {
        $id = isset($_GET["id"]) ? filter_var($_GET["id"], FILTER_VALIDATE_INT) : null;

        if ($id) {
            $data = json_decode(file_get_contents("php://input"));

            if (isset($data->name, $data->category)) {
                $name = filter_var($data->name, FILTER_SANITIZE_STRING);
                $category = filter_var($data->category, FILTER_SANITIZE_STRING);

                if ($name && $category) {
                    try {
                        $livro = new Livro();
                        $livro->id = $id;
                        $livro->name = $name;
                        $livro->category = $category;

                        if ($livro->updateLivro()) {
                            header('Content-Type: application/json', true, 200);
                            echo json_encode(["mensagem" => "Livro atualizado com sucesso!"]);
                        } else {
                            header('Content-Type: application/json', true, 500);
                            echo json_encode(["mensagem" => "Falha ao atualizar o livro!"]);
                        }
                    } catch (Exception $e) {
                        header('Content-Type: application/json', true, 500);
                        echo json_encode(["mensagem" => "Erro interno ao atualizar o livro"]);
                    }
                } else {
                    header('Content-Type: application/json', true, 400);
                    echo json_encode(["mensagem" => "Dados inválidos"]);
                }
            } else {
                header('Content-Type: application/json', true, 400);
                echo json_encode(["mensagem" => "Dados inválidos"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["mensagem" => "ID inválido"]);
        }
    }

    public function deleteLivro(): void {
        $id = isset($_GET["id"]) ? filter_var($_GET["id"], FILTER_VALIDATE_INT) : null;

        if ($id) {
            try {
                $livro = new Livro();
                $livro->id = $id;

                if ($livro->deleteLivro()) {
                    header('Content-Type: application/json', true, 200);
                    echo json_encode(["mensagem" => "Livro excluído com sucesso!"]);
                } else {
                    header('Content-Type: application/json', true, 500);
                    echo json_encode(["mensagem" => "Falha ao excluir o livro!"]);
                }
            } catch (Exception $e) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["mensagem" => "Erro interno ao excluir o livro"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["mensagem" => "ID inválido"]);
        }
    }
}
