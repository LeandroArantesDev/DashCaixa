<div id="modal">
    <form action="#" method="POST">
        <!-- inputs escondidos -->
        <input type="hidden" value="0" name="id">
        <!-- CSRF -->
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
        <h2>Adicionar Nova Categoria</h2>
        <div class="input-group-modal">
            <label for="nome">Nome da Categoria</label>
            <input type="text" value="" name="nome" id="nome" placeholder="Digite o nome da Categoria" required
                pattern="^[A-Za-zÀ-ÿ0-9\s\-_&()]{2,50}$"
                title="Nome deve conter apenas letras, números, espaços, hífens, underscores, & e parênteses (2-50 caracteres)"
                minlength="2" maxlength="50">
        </div>
        <div class="div-btn">
            <button type="button" onclick="esconderModal()">Cancelar</button>
            <button type="submit">Enviar</button>
        </div>
    </form>
    <div id="overlay-modal" onclick="esconderModal()"></div>
</div>
<script>
    const modal = document.getElementById("modal");
    const form = document.querySelector("#modal form");

    function modalCadastrar() {
        // action do formulario
        form.action = "<?= BASE_URL . 'backend/categorias/cadastrar.php' ?>";

        // titulo do modal
        const titulo = modal.querySelector("h2");
        titulo.textContent = "Adicionar Nova Categoria";

        modal.style.visibility = "visible";
    }

    async function modalEditar(id) {
        // ação do formulário
        form.action = "<?= BASE_URL . 'backend/categorias/editar.php' ?>";

        // mensagem de "Carregando..." enquanto busca os dados
        const titulo = modal.querySelector("h2");
        titulo.textContent = "Carregando dados da categoria...";
        modal.style.visibility = "visible";

        try {
            const response = await fetch(`<?= BASE_URL . 'backend/categorias/buscar_categoria.php?id=' ?>${id}`);

            const data = await response.json();

            if (data.erro) {
                titulo.textContent = data.erro;
                return;
            }

            const idInput = form.querySelector("input[name='id']");
            const nomeInput = document.getElementById("nome");

            // preenche os valores
            idInput.value = id;
            nomeInput.value = data.nome;

            // título do modal
            titulo.textContent = `Editar Categoria: ${data.nome}`;

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
        idInput.value = '';
        nomeInput.value = '';
    }
</script>