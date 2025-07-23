<div id="modal">
    <form action="#" method="POST">
        <!-- inputs escondidos -->
        <input type="hidden" value="0" name="id">
        <!-- CSRF -->
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF()?>">
        <h2>Adicionar Novo Usuario</h2>
        <div class="input-group-modal">
            <label for="nome">Nome do Usuario</label>
            <input type="text" name="nome" id="nome" placeholder="Digite o nome do Usuario">
        </div>
        <div class="input-group-modal">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Digite o email do Usuario">
        </div>
        <div class="input-group-modal">
            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo">
                <option value="0">Caixa</option>
                <option value="1">Administrador</option>
            </select>
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
        form.action = "<?= BASE_URL . 'backend/usuarios/cadastrar.php' ?>";

        // titulo do modal
        const titulo = modal.querySelector("h2");
        titulo.textContent = "Adicionar Novo Usuario";

        modal.style.visibility = "visible";
    }

    async function modalEditar(id) {
        // ação do formulário
        form.action = "<?= BASE_URL . 'backend/usuarios/editar.php' ?>";

        // mensagem de "Carregando..." enquanto busca os dados
        const titulo = modal.querySelector("h2");
        titulo.textContent = "Carregando dados do usuario...";
        modal.style.visibility = "visible";

        try {
            const response = await fetch(`<?= BASE_URL . 'backend/usuarios/buscar_usuario.php?id=' ?>${id}`);

            const data = await response.json();

            if (data.erro) {
                titulo.textContent = data.erro;
                return;
            }

            const idInput = form.querySelector("input[name='id']");
            const nomeInput = document.getElementById("nome");
            const emailInput = document.getElementById("email");
            const tipoInput = document.getElementById("tipo");

            // preenche os valores
            idInput.value = id;
            nomeInput.value = data.nome;
            emailInput.value = data.email;
            tipoInput.value = data.tipo;

            // título do modal
            titulo.textContent = `Editar Usuario: ${data.nome}`;

        } catch (error) {
            titulo.textContent = "Erro ao buscar os dados.";
            console.error("Erro no Fetch:", error);
        }
    }

    function esconderModal() {
        modal.style.visibility = "hidden";
    }
</script>