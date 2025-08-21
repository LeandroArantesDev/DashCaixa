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
            <button onclick="modalCadastrar()">
                <i class="bi bi-plus-lg"></i>
                <span class="hidden lg:block">Novo Produto</span>
            </button>
        </div>
        <div class="tabela-form">
            <form class="grid grid-cols-3">
                <input class="input-filtro col-span-2" type="text" name="busca" id="busca"
                       placeholder="Buscar por nome...">
                <button type="submit" class="flex justify-center items-center lg:gap-2"><i class="bi bi-search"></i>
                    <span class="hidden lg:block">
                    Buscar
                </span>
                </button>
            </form>
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
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
                    $stmt = $conexao->prepare("SELECT id, nome, categoria_id, img, preco, estoque, status FROM produtos WHERE cliente_id = ? AND nome LIKE ? AND status IN (0, 1) ORDER BY categoria_id ASC, nome ASC");
                    $stmt->bind_param("is", $_SESSION['cliente_id'], $busca_like);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0):
                        while ($row = $resultado->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="celula-tabela">
                                    <div class="flex items-center justify-center w-full">
                                        <?php
                                        // verificando se tem imagem ou não
                                        if ($row['img'] != NULL):
                                            ?>
                                            <img class="h-13 w-13 rounded-lg object-cover object-center"
                                                 src="<?= htmlspecialchars($row['img']) ?>"
                                                 alt="Imagem do produto <?= htmlspecialchars($row['nome']) ?>">
                                        <?php else: ?>
                                            <img class="h-13 w-13 rounded-lg object-cover object-center"
                                                 src="../../assets/img/placeholder_produto.jpg"
                                                 alt="Produto <?= htmlspecialchars($row['nome']) ?> sem imagem!">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['nome']) ?></td>
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
                                    <span class="bg-zinc-200 rounded-lg px-2 py-0.5 flex justify-center items-center">
                                        <?= htmlspecialchars($categoria ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="celula-tabela"><?= htmlspecialchars(formatarPreco($row['preco'])) ?></td>
                                <td class="celula-tabela <?= ($row['estoque'] < 5 ? 'text-red-500 font-bold' : '') ?>">
                                    <?= htmlspecialchars($row['estoque']) ?>
                                </td>
                                <td id="td-acoes" class="celula-tabela" colspan="2">
                                    <button id="btn-edita" class="botao-informativo"
                                            onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                        <i class="bi bi-pencil-square"></i>
                                        <span class="tooltip">Editar</span>
                                    </button>
                                    <form id="btn-deleta" action="../../backend/produtos/deletar.php" method="POST"
                                          onclick="return confirm('Tem certeza que deseja deletar este produto?')">
                                        <!-- inputs escondidos -->
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                        <button class="botao-informativo">
                                            <i class="bi bi-trash3"></i>
                                            <span class="tooltip">Deletar</span>
                                        </button>
                                    </form>
                                    <form id="btn-status" action="../../backend/produtos/status.php" method="POST">
                                        <!-- inputs escondidos -->
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                        <input type="hidden" name="status" value="<?= $row['status'] ?>">
                                        <button class="botao-informativo">
                                            <?php if ($row['status'] == 1) : ?>
                                                <i class="bi bi-eye-slash"></i>
                                                <span class="tooltip">Exibir</span>
                                            <?php elseif ($row['status'] == 0) : ?>
                                                <i class="bi bi-eye"></i>
                                                <span class="tooltip">Ocultar</span>
                                            <?php endif ?>
                                        </button>
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
<?php include("modal.php") ?>
<?php include("../../includes/fim.php") ?>