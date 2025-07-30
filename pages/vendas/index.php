<?php
$titulo = "Vendas";
include("../../includes/inicio.php")
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Vendas</h1>
            <p>Realize vendas e gere fichas</p>
        </div>
    </div>
    <div class="flex gap-3 items-center justify-between h-[86%]">
        <div class="tabela-form w-4/6 bg-white h-full">
            <div class="flex w-full border-b border-[var(--cinza-borda)] gap-4 p-4 bg-white">
                <input class="input-filtro col-span-2 w-full p-2 border border-[var(--cinza-borda)] rounded-lg"
                    type="text" name="busca" id="busca" placeholder="Buscar produto por nome...">
            </div>
            <div class="h-full flex-1 overflow-y-auto py-2">
                <div class="h-max flex flex-col gap-3 p-3 pb-20">
                    <?php
                    // buscando todos os produtos disponiveis
                    $stmt = $conexao->prepare("SELECT id, nome, categoria_id, preco, estoque FROM produtos WHERE cliente_id = ? AND status IN (0, 1)");
                    $stmt->bind_param("i", $_SESSION['cliente_id']);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0) :
                        while ($row = $resultado->fetch_assoc()) :
                    ?>
                            <div
                                class="produto-card flex items-center gap-4 p-4 border border-[var(--cinza-borda)] rounded-lg hover:shadow-md transition-shadow <?= ($row["estoque"] < 1) ? "filter grayscale" : "" ?>">
                                <div
                                    class="icone-container flex-shrink-0 h-12 w-12 flex items-center justify-center bg-sky-100 rounded-lg">
                                    <i class="bi bi-box-seam text-2xl text-sky-600"></i>
                                </div>
                                <div class="info-container flex-grow">
                                    <h3 class="font-semibold text-gray-900">
                                        <?= htmlspecialchars($row['nome']) ?> •
                                        <?php if ($row["estoque"] > 0): ?>
                                            <?= ($row["estoque"] < 5) ? "<span class='text-red-300'>" . htmlspecialchars($row['estoque']) . " Estoque baixo!</span>" : "<span>" . htmlspecialchars($row['estoque']) . "</span>" ?>
                                        <?php else: ?>
                                            <span class='text-red-300'>Sem estoque!</span>
                                        <?php endif; ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?php
                                        // buscando o nome da categoria do produto
                                        $stmt = $conexao->prepare("SELECT nome FROM categorias WHERE id = ?");
                                        $stmt->bind_param("i", $row['categoria_id']);
                                        $stmt->execute();
                                        $stmt->bind_result($nome_categoria);
                                        $stmt->fetch();
                                        $stmt->close();
                                        echo $nome_categoria;
                                        ?>
                                    </p>
                                    <p class="preco-tag text-lg font-bold text-sky-600 mt-1">
                                        <?= formatarPreco(htmlspecialchars($row['preco'])) ?></p>
                                </div>
                                <button id="btn-add-produto" type="button" <?= ($row["estoque"] < 1) ? "disabled" : "" ?>
                                    class="add-button h-10 w-10 flex items-center justify-center bg-sky-500 text-white rounded-md hover:bg-sky-600 transition-colors cursor-pointer"
                                    data-id="<?= htmlspecialchars($row["id"]) ?>"
                                    data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                    data-preco="<?= htmlspecialchars($row['preco']) ?>"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        <?php endwhile ?>
                    <?php else: ?>
                        <a class="flex items-center justify-center gap-2 w-full border border-[var(--cinza-borda)] p-2 rounded-lg" href="../produtos">Cadastrar produtos <i class="bi bi-plus-lg bg-blue-200 rounded-lg text-sky-600 text-lg flex justify-center items-center w-8 h-8"></i></a>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="w-2/6 h-full bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col"
            id="carrinho-lateral">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex space-x-3">
                    <i class="bi bi-cart text-xl text-sky-600"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Carrinho</h2>
                </div>
                <div>
                    <button type="button" id="limpar-carrinho" title="Limpar carrinho" class="hidden cursor-pointer text-red-700 hover:text-red-800 transition-colors">
                        <i class="bi bi-trash3 text-xl"></i>
                    </button>
                </div>
            </div>
            <ul id="lista-carrinho" class="text-center text-gray-500 py-4 overflow-x-auto flex-1 px-6 space-y-2">
                <li class="text-center text-gray-500 items-center">
                    <i class="bi bi-cart text-6xl mx-auto text-gray-300"></i>
                    <p class="text-lg font-medium mt-5">Nenhum item adicionado</p>
                    <p>Busque e adicione produtos à ficha</p>
                </li>
            </ul>
            <form action="../../backend/vendas/finalizar.php" method="POST" id="form-imprimir">
                <input type="hidden" name="itens" id="input-itens">
                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                <input type="hidden" name="usuario_id" id="usuario_id" value="<?= $_SESSION["id"] ?>">
                <!-- Aba Total  -->
                <div class="p-6 border-t border-gray-200">
                    <div class="bg-blue-50 rounded-lg py-2 px-4 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">TOTAL:</span>
                            <span id="valor-total" class="text-xl font-bold text-sky-600">R$ 0,00</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <span id="itens-total">0 itens</span>
                        </div>
                    </div>
                    <button type="submit" id="btn-finalizar"
                        class="w-full bg-sky-600 text-white py-2 rounded-lg font-semibold text-xl hover:bg-sky-700 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg cursor-pointer"
                        disabled>Finalizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include("modal.php");
if (isset($_SESSION['modal_venda'])):
?>
    <script>
        abrirModal(<?= $_SESSION['modal_venda'] ?>);
    </script>
<?php
    unset($_SESSION['modal_venda']);
endif;
?>
<script src="../../assets/js/carrinho.js"></script>
<?php include("../../includes/fim.php") ?>