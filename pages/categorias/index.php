<?php
$titulo = "Categorias";
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Gestão de Categorias</h1>
            <p>Organize seus produtos por categorias</p>
        </div>
        <button onclick="modalCadastrar()">
            <i class="bi bi-plus-lg"></i> Nova Categoria
        </button>
    </div>
    <div class="tabela-form">
        <form class="grid grid-cols-3 border-b border-[var(--cinza-borda)]">
            <input class="input-filtro col-span-2" type="text" name="busca" id="busca" placeholder="Buscar por nome...">
            <button type="submit"><i class="bi bi-search"></i> Buscar</button>
        </form>
        <div class="grid grid-cols-3 p-3 gap-3">
            <?php
            // puxando todas as categorias
            $stmt = $conexao->prepare("SELECT id, nome, status FROM categorias WHERE cliente_id = ? AND status IN (0, 1) ORDER BY nome ASC");
            $stmt->bind_param("i", $_SESSION['cliente_id']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            if ($resultado->num_rows > 0) :
                while ($row = $resultado->fetch_assoc()) :
            ?>
                    <div class="card-categoria">
                        <!-- card das categorias -->
                        <div class="topo">
                            <div class="txt-categoria">
                                <i class="bi bi-tag"></i>
                                <h2><?= htmlspecialchars($row['nome']) ?></h2>
                            </div>
                            <div class="flex gap-2">
                                <button id="btn-edita" class="botao-informativo"
                                    onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                    <i class="bi bi-pencil-square"></i>
                                    <span class="tooltip">Editar</span>
                                </button>
                                <form id="btn-deleta" action="../../backend/categorias/deletar.php" method="POST">
                                    <!-- inputs escondidos -->
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                    <button class="botao-informativo">
                                        <i class="bi bi-trash3"></i>
                                        <span class="tooltip">Deletar</span>
                                    </button>
                                </form>
                                <form id="btn-status" action="../../backend/categorias/status.php" method="POST">
                                    <!-- inputs escondidos -->
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                    <input type="hidden" name="status" value="<?= $row['status'] ?>">
                                    <button class="botao-informativo">
                                        <?php if ($row['status'] == 1) : ?>
                                            <i class="bi bi-eye-slash"></i>
                                        <?php elseif ($row['status'] == 0) : ?>
                                            <i class="bi bi-eye"></i>
                                        <?php endif ?>
                                        <span class="tooltip">Alterar visibilidade</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p>
                            <!-- quantidade de produtos cadastrados -->
                            <i class="bi bi-box-seam"></i>
                            <?php
                            // buscando quantos produtos a categoria possui
                            $stmt = $conexao->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
                            $stmt->bind_param("i", $row['id']);
                            $stmt->execute();
                            $stmt->bind_result($produtos_count);
                            $stmt->fetch();
                            $stmt->close();

                            if ($produtos_count > 0) {
                                echo $produtos_count . " produtos cadastrados";
                            } else {
                                echo "Nenhum produto encontrado!";
                            }
                            ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <h2>Sem categorias cadastradas!</h2>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<?php include("modal.php") ?>
<?php include("../../includes/fim.php") ?>