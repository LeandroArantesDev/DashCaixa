<?php
include("../backend/conexao.php");
$titulo = 'Cardapio';
$n_valida = true;
$main_full = true;
include("../includes/inicio.php")
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Cardapio</h1>
            <p>Veja todos produtos e adicione ao carrinho</p>
        </div>
        <button onclick="modalCadastrar()">
            <i class="bi bi-cart"></i>
            <span class="hidden lg:block">Carrinho</span>
        </button>
    </div>
    <div class="flex flex-col gap-5">
        <?php
        // puxando todas categorias
        $stmt = $conexao->prepare("SELECT id, nome FROM categorias WHERE status = 0");
        $stmt->execute();
        $resultado_categoria = $stmt->get_result();
        $stmt->close();

        if ($resultado_categoria->num_rows > 0):
            while ($row_categoria = $resultado_categoria->fetch_assoc()):
                ?>
                <div class="border border-borda rounded-lg overflow-hidden">
                    <div class="flex items-center border-b border-borda bg-white p-3">
                        <h2 class="text-2xl mt-2 mb-1 font-bold"><?= htmlspecialchars($row_categoria['nome']) ?></h2>
                    </div>
                    <div class="flex gap-5 p-5 max-w-full overflow-auto">
                        <?php
                        // puxando todos produtos da categoria
                        $stmt = $conexao->prepare("SELECT id, nome, img, preco FROM produtos WHERE categoria_id = ? AND status = 0 ");
                        $stmt->bind_param("i", $row_categoria['id']);
                        $stmt->execute();
                        $resultado_produto = $stmt->get_result();
                        $stmt->close();

                        if ($resultado_produto->num_rows > 0):
                            while ($row_produto = $resultado_produto->fetch_assoc()):
                                ?>
                                <article
                                        class="flex flex-col items-center justify-between border border-borda min-w-55 max-w-55git rounded-xl overflow-hidden hover:scale-102">
                                    <?php
                                    // verificando se tem imagem
                                    if ($row_produto['img'] == NULL || $row_produto['img'] == ""): ?>
                                        <img class="w-full" src="../assets/img/placeholder_produto.jpg"
                                             alt="Imagem do produto <?= $row_produto['nome'] ?>">
                                    <?php else: ?>
                                        <img class="w-full" src="<?= htmlspecialchars($row_produto['img']) ?>"
                                             alt="Imagem do produto <?= $row_produto['nome'] ?>">
                                    <?php endif; ?>
                                    <div class="flex flex-col items-center justify-center w-full p-3">
                                        <h3 class="text-md text-black/60"><?= htmlspecialchars($row_produto['nome']) ?></h3>
                                        <h4 class="text-lg text-principal font-bold"><?= htmlspecialchars(formatarPreco($row_produto['preco'])) ?></h4>
                                        <button class="bg-principal cursor-pointer rounded-lg p-1 text-white w-full hover:bg-principal-hover">
                                            <i class="bi bi-plus"></i> Carrinho
                                        </button>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <h2>Sem produtos cadastrados!</h2>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <h2>Sem categorias cadastradas!</h2>
        <?php endif; ?>
    </div>
</div>
<?php include("../includes/fim.php") ?>
