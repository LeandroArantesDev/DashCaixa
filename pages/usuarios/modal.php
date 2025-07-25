<div id="modal">
    <form action="#" method="POST">
        <!-- inputs escondidos -->
        <input type="hidden" value="0" name="id">
        <!-- CSRF -->
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
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
        <div class="input-group-modal checkbox">
            <input type="checkbox" name="checkbox" id="checkbox">
            <label for="">Deseja alterar a senha do usuário?</label>
        </div>
        <div class="input-group-modal password">
            <label for="email">Senha</label>
            <input type="password" name="senha" id="senha" placeholder="Digite a senha do Usuario">
        </div>
        <div class="input-group-modal password">
            <label for="email">Senha</label>
            <input type="password" name="confirmarsenha" id="confirmarsenha" placeholder="Digite a senha novamente">
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

    // Mostra os campos de senha (as divs com class="password")
    const passwordGroups = modal.querySelectorAll('.password');
    passwordGroups.forEach(group => group.classList.add("ativo"));

    modal.style.visibility = "visible";
}

async function modalEditar(id) {
    // ação do formulário
    form.action = "<?= BASE_URL . 'backend/usuarios/editar.php' ?>";

    // mensagem de "Carregando..." enquanto busca os dados
    const titulo = modal.querySelector("h2");
    titulo.textContent = "Carregando dados do usuario...";
    modal.style.visibility = "visible";

    // Mostrar checkbox
    const checkboxDiv = modal.querySelector(".checkbox");
    checkboxDiv.classList.add("ativo");

    // Mostrar/ocultar campos de senha com base na checkbox
    const checkbox = modal.querySelector("#checkbox");
    const passwordGroups = modal.querySelectorAll(".password");

    // Resetar o estado da checkbox e campos de senha
    checkbox.checked = false;
    passwordGroups.forEach(group => group.classList.remove("ativo"));

    // Quando mudar o estado da checkbox
    checkbox.onchange = function() {
        if (checkbox.checked) {
            passwordGroups.forEach(group => group.classList.add("ativo"));
        } else {
            passwordGroups.forEach(group => group.classList.remove("ativo"));
        }
    };

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
    // Esconde o modal
    modal.style.visibility = "hidden";

    // preenche os valores
    const idInput = form.querySelector("input[name='id']");
    const nomeInput = document.getElementById("nome");
    const emailInput = document.getElementById("email");
    const tipoInput = document.getElementById("tipo");
    idInput.value = '';
    nomeInput.value = '';
    emailInput.value = '';
    tipoInput.value = 0;

    // Oculta os campos de senha (divs com class="password")
    const passwordGroups = modal.querySelectorAll('.password');
    passwordGroups.forEach(group => group.classList.remove("ativo"));

    // Ocultar o campo checkbox
    const checkbox_div = modal.querySelector(".checkbox");
    checkbox_div.classList.remove("ativo");
}
</script>