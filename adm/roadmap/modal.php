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
                <option value="2">Concluído</option>
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
            <button type="submit" id="submit">Adicionar</button>
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

    const titulo = modal.querySelector("h2");
    titulo.textContent = "Nova funcionalidade";

    const btnSubmit = document.getElementById("submit");
    btnSubmit.innerText = "Adicionar";

    const editar = document.querySelectorAll(".editar");
    editar.forEach(e => e.style.display = "none");
}

async function modalEditar(id) {
    // ação do formulário
    form.action = "<?= BASE_URL . 'backend/roadmap/editar.php' ?>";

    // mensagem de "Carregando..." enquanto busca os dados
    const titulo = modal.querySelector("h2");
    titulo.textContent = "Carregando dados da funcionalidade...";
    modal.style.visibility = "visible";

    const btnSubmit = document.getElementById("submit");
    btnSubmit.innerText = "Editar";

    try {
        const response = await fetch(`<?= BASE_URL . 'backend/roadmap/buscar_funcionalidade.php?id=' ?>${id}`);
        const data = await response.json();

        if (data.erro) {
            titulo.textContent = data.erro;
            return;
        }

        const idInput = form.querySelector("input[name='id']");
        const tituloInput = document.getElementById("titulo");
        const descricaoInput = document.getElementById("descricao");
        const statusInput = document.getElementById("status");
        const criadoInput = document.getElementById("criado_em");
        const concluidoInput = document.getElementById("concluido_em");

        // preenche os valores
        idInput.value = id;
        tituloInput.value = data.titulo;
        descricaoInput.value = data.descricao;
        statusInput.value = data.status; // Isso seleciona automaticamente a option correta

        // Formatar e preencher as datas
        if (data.criado_em && data.criado_em !== '0000-00-00') {
            criadoInput.value = formatarDataParaInput(data.criado_em);
        }

        if (data.concluido_em && data.concluido_em !== '0000-00-00') {
            concluidoInput.value = formatarDataParaInput(data.concluido_em);
        }

        // título do modal
        titulo.textContent = `Editar funcionalidade`;

    } catch (error) {
        titulo.textContent = "Erro ao buscar os dados.";
        console.error("Erro no Fetch:", error);
    }
}

function esconderModal() {
    modal.style.visibility = "hidden";

    // esvazia os valores
    const idInput = form.querySelector("input[name='id']");
    const tituloInput = document.getElementById("titulo");
    const descricaoInput = document.getElementById("descricao");
    const statusInput = document.getElementById("status");
    const criadoInput = document.getElementById("criado_em");
    const concluidoInput = document.getElementById("concluido_em");

    idInput.value = '';
    tituloInput.value = '';
    descricaoInput.value = '';
    statusInput.value = '0'; // Volta para "A fazer"
    criadoInput.value = '';
    concluidoInput.value = '';
}

// Função auxiliar para formatar datas
function formatarDataParaInput(data) {
    if (!data || data === '0000-00-00' || data === 'null') {
        return '';
    }

    // Se já está no formato correto (YYYY-MM-DD)
    if (/^\d{4}-\d{2}-\d{2}$/.test(data)) {
        return data;
    }

    // Se está no formato DD/MM/YYYY, converter para YYYY-MM-DD
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(data)) {
        const [dia, mes, ano] = data.split('/');
        return `${ano}-${mes}-${dia}`;
    }

    // Tentar converter como timestamp ou outro formato
    const date = new Date(data);
    if (!isNaN(date.getTime())) {
        return date.toISOString().split('T')[0];
    }

    return '';
}
</script>