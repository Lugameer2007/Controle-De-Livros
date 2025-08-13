<script>
        const API_URL = 'UserController.php';
        const TOKEN = 'Bearer DEFAULT_TOKEN';

        async function carregarLivros() {
            const response = await fetch(API_URL, {
                headers: { 'Authorization': TOKEN }
            });
            const livros = await response.json();
            const lista = document.getElementById('lista-livros');
            lista.innerHTML = '';

            if (Array.isArray(livros)) {
                livros.forEach(livro => {
                    const li = document.createElement('li');
                    li.className = 'livro';
                    li.innerHTML = `
                        <strong>${livro.name}</strong> - ${livro.category}
                        <button onclick="prepararEdicao(${livro.id}, '${livro.name}', '${livro.category}')">Editar</button>
                        <button onclick="excluirLivro(${livro.id})">Excluir</button>
                    `;
                    lista.appendChild(li);
                });
            } else {
                lista.innerHTML = '<li>Nenhum livro encontrado</li>';
            }
        }

        function prepararEdicao(id, nome, categoria) {
            document.getElementById('livro-id').value = id;
            document.getElementById('input-nome').value = nome;
            document.getElementById('input-categoria').value = categoria;
            document.getElementById('botao-salvar').textContent = 'Atualizar';
        }

        async function salvarLivro() {
            const id = document.getElementById('livro-id').value;
            const nome = document.getElementById('input-nome').value;
            const categoria = document.getElementById('input-categoria').value;

            const livro = {
                name: nome,
                category: categoria
            };

            let method = 'POST';
            let endpoint = API_URL;
            let mensagemSucesso = 'Livro adicionado com sucesso!';

            if (id) {
                method = 'PUT';
                endpoint = `${API_URL}?id=${id}`;
                mensagemSucesso = 'Livro atualizado com sucesso!';
            } else {
                livro.id = Date.now(); // Apenas para novo livro
            }

            const response = await fetch(endpoint, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': TOKEN
                },
                body: JSON.stringify(livro)
            });

            const resultado = await response.json();
            alert(resultado.mensagem || mensagemSucesso);
            limparFormulario();
            carregarLivros();
        }

        async function excluirLivro(id) {
            const confirmacao = confirm("Tem certeza que deseja excluir este livro?");
            if (!confirmacao) return;

            const response = await fetch(`${API_URL}?id=${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': TOKEN
                }
            });

            const resultado = await response.json();
            alert(resultado.mensagem);
            carregarLivros();
        }

        function limparFormulario() {
            document.getElementById('livro-id').value = '';
            document.getElementById('input-nome').value = '';
            document.getElementById('input-categoria').value = '';
            document.getElementById('botao-salvar').textContent = 'Adicionar';
        }

        carregarLivros();
    </script>