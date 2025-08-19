<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle De Livros</title>
    <link rel="stylesheet" href="templates/css/css/Style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .livro { margin: 10px 0; }
    </style>
</head>
<body>
    <header class="Cabeçalho">
        <h2>Controle De Livros</h2>
    </header>
        <h3>Adicionar Livros</h3>
    <section>
        
        <!-- Campo oculto para armazenar ID ao editar -->
        <input type="hidden" id="livro-id">

        <label for="input-nome">Nome:</label>
        <input type="text" id="input-nome">

        <label for="input-categoria">Categoria:</label>
        <input type="text" id="input-categoria">

        <button id="botao-salvar" onclick="salvarLivro()">Adicionar</button>
    </section>

    <section>
        <h3>Lista de Livros</h3>
        <ul id="lista-livros"></ul>
    </section>

   <script>
        <script src="js/app.js"></script>
   </script>
