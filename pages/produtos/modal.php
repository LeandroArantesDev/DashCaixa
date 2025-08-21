<div id="modal">
    <form action="#" method="POST">
        <!-- inputs escondidos -->
        <input type="hidden" value="0" name="id">
        <!-- CSRF -->
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
        <h2>Adicionar Novo Produto</h2>
        <div class="input-group-modal">
            <label for="nome">Nome do Produto</label>
            <input type="text" value="" name="nome" id="nome" placeholder="Digite o nome do Produto" required
                   pattern="^[A-Za-zÀ-ÿ0-9\s\-_()]{2,100}$"
                   title="Nome deve conter apenas letras, números, espaços, hífens e parênteses (2-100 caracteres)"
                   minlength="2" maxlength="100">
        </div>
        <div class="input-group-modal">
            <label for="categoria">Categoria</label>
            <select name="categoria_id" id="categoria_id" required title="Selecione uma categoria">
                <option value="0" disabled selected>Escolha uma categoria</option>
                <?php
                // Buscando todas as categorias
                $stmt = $conexao->prepare("SELECT id, nome FROM categorias WHERE cliente_id = ? AND status IN (0, 1)");
                $stmt->bind_param("i", $_SESSION['cliente_id']);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $stmt->close();

                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['nome']) ?></option>
                    <?php endwhile ?>
                <?php else: ?>
                    <option value="0" disabled>Nenhuma categoria cadastrada!</option>
                <?php endif ?>
            </select>
        </div>
        <div class="input-group-modal">
            <label for="nome">Imagem do Produto</label>
            <input type="text" value="" name="img" id="img" placeholder="Cole o link da imagem"
                   minlength="2">
        </div>
        <div class="input-group-modal">
            <label for="preco">Preço de Venda</label>
            <input type="text" name="preco_formatado" id="preco" placeholder="R$ 0,00" inputmode="numeric" required
                   pattern="^R\$\s\d{1,6}(,\d{2})?$" title="Digite um preço válido (ex: R$ 10,50)" maxlength="12">
            <input type="hidden" name="preco" id="preco_real">
        </div>
        <div class="input-group-modal">
            <label for="estoque">Estoque</label>
            <input type="number" name="estoque" id="estoque" placeholder="0" required min="0" max="99999"
                   pattern="^[0-9]{1,5}$" title="Digite uma quantidade válida de estoque (0-99999)" step="1">
        </div>
        <div class="div-btn">
            <button type="button" onclick="esconderModal()">Cancelar</button>
            <button type="submit">Enviar</button>
        </div>
    </form>
    <div id="overlay-modal" onclick="esconderModal()"></div>
</div>
<script src="<?= BASE_URL ?>assets/js/formatarValores.js"></script>
<script>
    const modal = document.getElementById("modal");
    const form = document.querySelector("#modal form");

    // Aplicar formatação no input de preço quando a página carregar
    document.addEventListener('DOMContentLoaded', function () {
        aplicarFormatacaoPreco('preco', 'preco_real');
    });

    function modalCadastrar() {
        // action do formulario
        form.action = "<?= BASE_URL . 'backend/produtos/cadastrar.php' ?>";

        // titulo do modal
        const titulo = modal.querySelector("h2");
        titulo.textContent = "Adicionar Novo Produto";

        modal.style.visibility = "visible";
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
            const imgInput = document.getElementById("img");
            const precoInput = document.getElementById("preco");
            const precoRealInput = document.getElementById("preco_real");
            const estoqueInput = document.getElementById("estoque");

            // preenche os valores
            idInput.value = id;
            nomeInput.value = data.nome;
            categoriaInput.value = data.categoria_id;
            imgInput.value = data.img;

            // Formatar o preço para exibição
            precoInput.value = formatarPrecoExibicao(data.preco);
            precoRealInput.value = parseFloat(data.preco).toFixed(2);

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
        const precoRealInput = document.getElementById("preco_real");
        const estoqueInput = document.getElementById("estoque");

        idInput.value = '';
        nomeInput.value = '';
        categoriaInput.value = 0;
        precoInput.value = 'R$ 0,00';
        precoRealInput.value = '0.00';
        estoqueInput.value = '';
    }
</script>