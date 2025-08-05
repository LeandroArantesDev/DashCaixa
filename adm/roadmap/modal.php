<div id="modal">
    <form action="#" method="POST">
        <!-- inputs escondidos -->
        <input type="hidden" value="0" name="id">
        <!-- CSRF -->
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
        <h2>Nova funcionalidade</h2>
        <div class="input-group-modal">
            <label for="titulo">Titulo</label>
            <input type="text" value="" name="titulo" id="titulo" placeholder="Nome da funcionalidade">
        </div>
        <div class="input-group-modal">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" placeholder="Descreva a funcionalidade"></textarea>
        </div>
        <div class="input-group-modal editar">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="0">A fazer</option>
                <option value="1">Em andamento</option>
                <option value="1">Concluído</option>
            </select>
        </div>
        <div class="input-group-modal editar">
            <label for="criado_em">Data de Início</label>
            <input type="date" name="criado_em" id="criado_em">
        </div>
        <div class="input-group-modal editar">
            <label for="concluido_em">Data de Conclusão</label>
            <input type="date" name="concluido_em" id="concluido_em">
        </div>
        <div class="div-btn">
            <button type="button" onclick="esconderModal()">Cancelar</button>
            <button type="submit">Adicionar</button>
        </div>
    </form>
    <div id="overlay-modal" onclick="esconderModal()"></div>
</div>
<script>
    const modal = document.getElementById("modal");
    const form = document.querySelector("#modal form");

    function modalCadastrar() {
        // action do formulario
        form.action = "<?= BASE_URL . 'backend/roadmap/cadastrar.php' ?>";
        modal.style.visibility = "visible";

        const editar = document.querySelectorAll(".editar");
        editar.forEach(e => e.style.display = "none");
    }

    async function modalEditar(id) {
        // ação do formulário
        form.action = "<?= BASE_URL . 'backend/produtos/editar.php' ?>";

        // mensagem de "Carregando..." enquanto busca os dados
        const titulo = modal.querySelector("h2");
        titulo.textContent = "Carregando dados do produto...";
        modal.style.visibility = "visible";

        try {
            const response = await fetch(`<?= BASE_URL . 'backend/produtos/buscar_produto.php?id=' ?>${id}`);

            const data = await response.json();

            if (data.erro) {
                titulo.textContent = data.erro;
                return;
            }

            const idInput = form.querySelector("input[name='id']");
            const nomeInput = document.getElementById("nome");
            const categoriaInput = document.getElementById("categoria_id");
            const precoInput = document.getElementById("preco");
            const estoqueInput = document.getElementById("estoque");

            // preenche os valores
            idInput.value = id;
            nomeInput.value = data.nome;
            categoriaInput.value = data.categoria_id;
            precoInput.value = data.preco;
            estoqueInput.value = data.estoque;

            // título do modal
            titulo.textContent = `Editar Produto: ${data.nome}`;

        } catch (error) {
            titulo.textContent = "Erro ao buscar os dados.";
            console.error("Erro no Fetch:", error);
        }
    }

    function esconderModal() {
        modal.style.visibility = "hidden";

        // esvazia os valores
        const idInput = form.querySelector("input[name='id']");
        const nomeInput = document.getElementById("nome");
        const categoriaInput = document.getElementById("categoria_id");
        const precoInput = document.getElementById("preco");
        const estoqueInput = document.getElementById("estoque");
        idInput.value = '';
        nomeInput.value = '';
        categoriaInput.value = 0;
        precoInput.value = '';
        estoqueInput.value = '';
    }
</script>