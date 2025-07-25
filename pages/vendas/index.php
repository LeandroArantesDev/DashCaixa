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
        <div class="flex gap-3 items-center justify-between h-[75dvh] w-full">
            <div class="tabela-form w-4/6 bg-white h-full">
                <form action="#" class="grid grid-cols-3 border-b border-[var(--cinza-borda)]">
                    <input class="input-filtro col-span-2" type="text" name="busca" id="busca"
                           placeholder="Buscar produto por nome...">
                    <button type="submit"><i class="bi bi-search"></i> Buscar</button>
                </form>
                <div class="max-h-[75dvh] overflow-y-auto">
                    <div class="h-max flex flex-col gap-3 p-3">
                        <?php
                        // buscando todos os produtos disponiveis
                        $stmt = $conexao->prepare("SELECT nome, categoria_id, preco, estoque FROM produtos WHERE status IN (0, 1)");
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $stmt->close();

                        if ($resultado->num_rows > 0) :
                            while ($row = $resultado->fetch_assoc()) :
                                ?>
                                <div class="produto-card flex items-center gap-4 p-4 border border-[var(--cinza-borda)] rounded-lg hover:shadow-md transition-shadow">
                                    <div class="icone-container flex-shrink-0 h-12 w-12 flex items-center justify-center bg-sky-100 rounded-lg">
                                        <i class="bi bi-box-seam text-2xl text-sky-600"></i>
                                    </div>
                                    <div class="info-container flex-grow">
                                        <h3 class="font-semibold text-gray-900">
                                            <?= htmlspecialchars($row['nome']) ?> •
                                            <span><?= htmlspecialchars($row['estoque']) ?></span>
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
                                        <p class="preco-tag text-lg font-bold text-sky-600 mt-1"><?= formatarPreco(htmlspecialchars($row['preco'])) ?></p>
                                    </div>
                                    <button id="btn-add-produto" class="add-button h-10 w-10 flex items-center justify-center bg-sky-500 text-white rounded-md hover:bg-sky-600 transition-colors cursor-pointer"><i class="bi bi-plus-lg"></i></button>
                                </div>
                            <?php endwhile ?>
                        <?php else: ?>
                            <h2>Sem produtos cadastrados!</h2>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="w-2/6 bg-white">
                <form action="../../backend/vendas/registrar_venda.php" method="POST">
                    <h2><i class="bi bi-cart"></i> Venda Atual</h2>
                    <div>
                        <!-- produtos adicionados ficaram aqui dentro -->
                    </div>
                    <div>
                        <p>Total: <span>R$ 0,00</span></p>
                        <button type="submit">Finalizar e Imprimir Ficha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include("../../includes/fim.php") ?>