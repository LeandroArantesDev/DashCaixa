<div id="modal">
    <form action="#" method="POST">
        <!-- inputs escondidos -->
        <input type="hidden" value="0" name="id">
        <!-- CSRF -->
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF()?>">
        <h2>Adicionar Novo Produto</h2>
        <div class="input-group-modal">
            <label for="nome">Nome do Produto</label>
            <input type="text" value="" name="nome" id="nome" placeholder="Digite o nome do Produto">
        </div>
        <div class="input-group-modal">
            <label for="categoria">Categoria</label>
            <select name="categoria_id" id="categoria_id">
                <option value="0" disabled selected>Escolha uma categoria</option>
                <?php
                // Buscando todas as categorias
                $stmt = $conexao->prepare("SELECT id, nome FROM categorias");
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
            <label for="preco">Preço de Venda</label>
            <input type="number" name="preco" id="preco" placeholder="0,00">
        </div>
        <div class="input-group-modal">
            <label for="estoque">Estoque</label>
            <input type="number" name="estoque" id="estoque" placeholder="0">
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
    }
</script>