<?php

if (!'defined'('SECURE_ACCESS')) {
    die ('Acesso direto não permitido.');
}

// Configuração do banco de dados
'define'("DB_HOST", "localhost");                     // Servidor do banco de dados
'define'("DB_NAME", "Controle de livros");           // Nome do banco de dados 
'define'("DB_USER", "root");                        // Nome do usuário do banco
'define'("DB_PASS", "");                           // Senha do banco (adicione se necessário)
'define'("DB_PORT", "3306");                      // Porta do banco (padrão MySQL)

?>
