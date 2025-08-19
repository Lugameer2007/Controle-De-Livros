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

    private function getIdFromUri(): ?int { 
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 
        $segments = explode('/', trim($uri, '/')); 
        $id = end($segments); 
        return is_numeric($id) ? (int) $id : null; 
    } 

    private function respondBadRequest(string $mensagem): void { 
        header('Content-Type: application/json', true, 400); 
        echo json_encode(["mensagem" => $mensagem]); 
    } 

    public function processRequest(): void { 
        if (!$this->authorize()) { 
            header('Content-Type: application/json', true, 401); 
            echo json_encode(['mensagem' => 'Não autorizado']); 
            return; 
        } 
        
        $method = $_SERVER['REQUEST_METHOD']; 
        $id = $this->getIdFromUri(); 
        
        switch ($method) { 
            case 'GET': 
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
                if ($id) { 
                    $this->updateLivro($id); 
                } else { 
                    $this->respondBadRequest("ID obrigatório para atualizar."); 
                } 
                break; 

            case 'DELETE': 
                if ($id) { 
                    $this->deleteLivro($id); 
                } else { 
                    $this->respondBadRequest("ID obrigatório para excluir."); 
                } 
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
                $this->respondBadRequest("Dados inválidos ou mal formatados!"); 
            } 
        } else { 
            $this->respondBadRequest("Entrada inválida!"); 
        } 
    } 

    public function updateLivro(int $id): void { 
        $data = json_decode(file_get_contents("php://input")); 
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'PUT') {
            if (!isset($data->name) || !isset($data->category)) {
                $this->respondBadRequest("Campos obrigatórios ausentes para PUT");
                return;
            }
        }

        if ($method === 'PATCH') {
            if (!isset($data->name) && !isset($data->category)) {
                $this->respondBadRequest("Nenhum campo fornecido para PATCH");
                return;
            }
        }

        try {
            $livro = new Livro();
            $livro->id = $id;
            $livro->name = isset($data->name) ? filter_var($data->name, FILTER_SANITIZE_STRING) : null;
            $livro->category = isset($data->category) ? filter_var($data->category, FILTER_SANITIZE_STRING) : null;

            if ($method === 'PUT') {
                $success = $livro->updateLivro();
            } else {
                $success = $livro->updateLivroParcial(); // Você precisa garantir que esse método exista
            }

            if ($success) {
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
    }

    public function deleteLivro(int $id): void {
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
    }
}

?>
