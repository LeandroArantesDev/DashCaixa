<?php
// impedindo usuarios não fundadores de acessar nossas paginas
//include("../../includes/valida_fundador.php");

$titulo = "Dashboard";
include("../../backend/funcoes/dashboard-fundadores.php");
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Roadmap de Desenvolvimento</h1>
            <p>Acompanhe o progresso do nosso projeto!</p>
        </div>
        <button onclick="modalCadastrar()"><i class="bi bi-plus-lg"></i> Nova funcionalidade</button>
    </div>
    <div>
        <div>
            <h2>Planejado</h2>
            <button onclick="modalCadastrar()"><i class="bi bi-plus-lg"></i></button>
            <ol>
                <?php
                $stmt_planejado = $conexao->prepare("SELECT id, titulo, descricao, status FROM roadmap WHERE status = 0");
                $stmt_planejado->execute();
                $resultado = $stmt_planejado->get_result();
                $stmt_planejado->close();
                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                ?>
                        <li>
                            <h3><?= $row["titulo"] ?></h3>
                            <p><?= $row["descricao"] ?></p>
                            <button onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="../../backend/roadmap/deletar.php" method="post">
                                <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                <button type="submit"><i class="bi bi-trash3"></i></button>
                            </form>
                            <form action="../../backend/roadmap/avancar_item.php" method="post">
                                <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                <input type="hidden" name="status" value="<?= $row["status"] ?>">
                                <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                <button type="submit">Iniciar</button>
                            </form>
                        </li>
                <?php endwhile;
                endif;
                $resultado = null;
                $row = null;
                ?>
            </ol>
        </div>
        <hr>
        <div>
            <h2>Em andamento</h2>
            <ol>
                <?php
                $stmt_planejado = $conexao->prepare("SELECT id, titulo, descricao, status, criado_em FROM roadmap WHERE status = 1");
                $stmt_planejado->execute();
                $resultado = $stmt_planejado->get_result();
                $stmt_planejado->close();
                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                ?>
                        <li>
                            <h3><?= $row["titulo"] ?></h3>
                            <p><?= $row["descricao"] ?></p>
                            <?php if ($row["criado_em"] != null): ?>
                                <p>Inicio: <?= $row["criado_em"] ?></p>
                            <?php endif; ?>
                            <button onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="../../backend/roadmap/deletar.php" method="post">
                                <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                <button type="submit"><i class="bi bi-trash3"></i></button>
                            </form>
                            <form action="../../backend/roadmap/avancar_item.php" method="post">
                                <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                <input type="hidden" name="status" value="<?= $row["status"] ?>">
                                <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                <button type="submit">Concluir</button>
                            </form>
                        </li>
                <?php endwhile;
                endif;
                $resultado = null;
                $row = null;
                ?>
            </ol>
        </div>
        <hr>
        <div>
            <h2>Concluído</h2>
            <ol>
                <?php
                $stmt_planejado = $conexao->prepare("SELECT id, titulo, descricao, status, criado_em, concluido_em FROM roadmap WHERE status = 2");
                $stmt_planejado->execute();
                $resultado = $stmt_planejado->get_result();
                $stmt_planejado->close();
                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                ?>
                        <li>
                            <h3><?= $row["titulo"] ?></h3>
                            <p><?= $row["descricao"] ?></p>
                            <?php if ($row["criado_em"] != null): ?>
                                <p>Inicio: <?= $row["criado_em"] ?></p>
                            <?php endif; ?>
                            <?php if ($row["concluido_em"] != null): ?>
                                <p>Conclusão: <?= $row["concluido_em"] ?></p>
                            <?php endif; ?>
                            <button onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="../../backend/roadmap/deletar.php" method="post">
                                <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                <button type="submit"><i class="bi bi-trash3"></i></button>
                            </form>
                        </li>
                <?php endwhile;
                endif;
                $resultado = null;
                $row = null;
                ?>
            </ol>
        </div>
    </div>
</div>
<?php include("modal.php") ?>
<?php include("../../includes/fim.php") ?>