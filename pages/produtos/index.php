<?php
$titulo = "Produtos";
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Gestão de Produtos</h1>
            <p>Gerencie seu catálogo de produtos</p>
        </div>
        <a href="#"><i class="bi bi-plus-lg"></i> Novo Produto</a>
    </div>
    <div class="tabela-form">
        <form class="grid grid-cols-3">
            <input class="input-filtro col-span-2" type="text" name="busca" id="busca" placeholder="Buscar por nome...">
            <button type="submit"><i class="bi bi-search"></i> Buscar</button>
        </form>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $busca = $_GET['busca'] ?? '';

                    $busca_like = "%" . $busca . "%";

                    // buscando todos produtos
                    $stmt = $conexao->prepare("SELECT nome, categoria_id, preco, estoque FROM produtos WHERE nome LIKE ?");
                    $stmt->bind_param("s", $busca_like);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0):
                        while ($row = $resultado->fetch_assoc()):
                    ?>
                            <tr class="border-t border-[var(--cinza-borda)]">
                                <td class="celula-tabela flex justify-center items-center gap-2">
                                    <i class="bi bi-box-seam bg-blue-200 rounded-lg text-blue-600 text-lg flex justify-center items-center w-10 h-10"></i>
                                    <p>
                                    <?= htmlspecialchars($row['nome']) ?>
                                    </p>

                                </td>
                                <td class="celula-tabela">
                                    <?php
                                    // buscando o nome da categoria
                                    $stmt = $conexao->prepare("SELECT nome FROM categorias WHERE id = ?");
                                    $stmt->bind_param("i", $row['categoria_id']);
                                    $stmt->execute();
                                    $stmt->bind_result($categoria);
                                    $stmt->fetch();
                                    $stmt->close();
                                    ?>
                                    <span class="bg-zinc-100 rounded-full px-2 py-0.5">
                                        <?= htmlspecialchars($categoria ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="celula-tabela"><?= htmlspecialchars(formatarPreco($row['preco'])) ?></td>
                                <td class="celula-tabela <?= ($row['estoque'] < 5 ? 'text-red-500 font-bold' : '') ?>">
                                    <?= htmlspecialchars($row['estoque']) ?></td>
                                <td id="td-acoes" class="celula-tabela" colspan="2">
                                    <form id="btn-edita" action="#">
                                        <button><i class="bi bi-pencil-square"></i></button>
                                    </form>
                                    <form id="btn-deleta" action="#">
                                        <button><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    <?php else: ?>
                        <?php $_SESSION['resposta'] = "Sem registros!" ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("../../includes/fim.php") ?>