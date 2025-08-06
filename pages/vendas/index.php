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
        <div class="tabela-form w-4/6 bg-fundo-interface h-full">
            <div class="flex w-full border-b border-borda gap-4 p-4 bg-fundo-interface">
                <input class="input-filtro col-span-2 w-full p-2 border border-borda rounded-lg"
                    type="text" name="busca" id="busca" placeholder="Buscar produto por nome...">
            </div>
            <div class="h-full flex-1 overflow-y-auto py-2">
                <div class="h-max flex flex-col gap-3 p-3 pb-20">
                    <?php
                    // buscando todos os produtos disponiveis
                    $stmt = $conexao->prepare("SELECT id, nome, categoria_id, preco, estoque
FROM produtos
WHERE cliente_id = ?
  AND status = 0
  AND categoria_id IN (
      SELECT id
      FROM categorias
      WHERE status = 0
  )
ORDER BY nome ASC
");
                    $stmt->bind_param("i", $_SESSION['cliente_id']);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0) :
                        while ($row = $resultado->fetch_assoc()) :
                    ?>
                            <div
                                class="produto-card flex items-center gap-4 p-4 border border-borda rounded-lg hover:shadow-sm transition-all duration-200 hover:scale-102 <?= ($row["estoque"] < 1) ? "filter grayscale" : "" ?>">
                                <div
                                    class="icone-container flex-shrink-0 h-12 w-12 flex items-center justify-center bg-sky-100 rounded-lg">
                                    <i class="bi bi-box-seam text-2xl text-principal"></i>
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
                                    <p class="preco-tag text-lg font-bold text-principal mt-1">
                                        <?= formatarPreco(htmlspecialchars($row['preco'])) ?></p>
                                </div>
                                <button id="btn-add-produto" type="button" <?= ($row["estoque"] < 1) ? "disabled" : "" ?>
                                    class="add-button h-10 w-10 flex items-center justify-center bg-principal text-white rounded-lg hover:bg-principal-hover transition-all duration-200 hover:scale-110 cursor-pointer"
                                    data-id="<?= htmlspecialchars($row["id"]) ?>"
                                    data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                    data-preco="<?= htmlspecialchars($row['preco']) ?>"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        <?php endwhile ?>
                    <?php else: ?>
                        <a class="flex items-center justify-center gap-2 w-full border border-borda p-2 rounded-lg"
                            href="../produtos">Cadastrar produtos <i
                                class="bi bi-plus-lg bg-blue-200 rounded-lg text-principal text-lg flex justify-center items-center w-8 h-8"></i></a>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="w-2/6 h-full bg-fundo-interface rounded-2xl shadow-sm border border-borda flex flex-col"
            id="carrinho-lateral">
            <div class="p-6 border-b border-borda flex items-center justify-between">
                <div class="flex space-x-3">
                    <i class="bi bi-cart text-xl text-principal"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Carrinho</h2>
                </div>
                <div>
                    <button type="button" id="limpar-carrinho" title="Limpar carrinho"
                        class="hidden cursor-pointer text-red-700 hover:text-red-800 hover:scale-115">
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
                <div class="p-6 border-t border-borda">
                    <div class="bg-blue-50 rounded-lg py-2 px-4 mb-2">
                        <!-- valor total -->
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">TOTAL:</span>
                            <span id="valor-total" class="text-xl font-bold text-principal">R$ 0,00</span>
                        </div>
                        <!-- troco -->
                        <div id="valor-troco" class="flex justify-between items-center" style="display:none">
                            <span class="text-xs font-semibold text-gray-900">TROCO:</span>
                            <span id="valor-troco-valor" class="text-sm font-bold text-green-600">R$ 0,00</span>
                        </div>
                        <!-- quantidade de itens -->
                        <div class="mt-2 text-sm text-gray-600">
                            <span id="itens-total">0 itens</span>
                        </div>
                    </div>

                    <!-- chamar calcular troco -->
                    <button id="btn-chamar-calcular-troco" style="display:none"
                        class="cursor-pointer w-full flex items-center justify-center space-x-2 py-2 rounded-lg font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200 mb-2">
                        <i class="bi bi-calculator"></i>
                        <span>Calcular Troco</span>
                    </button>
                    <!-- Calcular o troco -->
                    <div id="btn-calcular-troco" style="display:none"
                        class="w-full flex items-center justify-center space-x-1 py-2 rounded-lg font-medium transition-colors bg-gray-100 text-gray-700 mb-2">
                        <i class="bi bi-currency-dollar text-lg text-green-600"></i>
                        <span>
                            Valor Recebido:
                        </span>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">
                                R$
                            </span>
                            <input type="number" step="0.01" min="0" id="input-valor-recebido" placeholder="0,00"
                                class="w-30 pl-8 border border-borda rounded-lg focus:ring-2 focus:ring-principal focus:border-transparent text-lg font-medium">
                        </div>
                    </div>
                    <!-- finalizar a venda -->
                    <button type="submit" id="btn-finalizar"
                        class="w-full bg-principal text-white py-2 rounded-lg font-semibold text-xl hover:bg-principal-hover focus:ring-2 focus:ring-principal focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm cursor-pointer"
                        disabled>
                        <span>Finalizar</span>
                    </button>
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