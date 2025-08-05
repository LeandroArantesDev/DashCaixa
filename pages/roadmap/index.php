<?php
// validando se o usuario é adm ou fundador para acessar
include("../../includes/valida_adm.php");

$titulo = "Usuarios";
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Roadmap de Desenvolvimento</h1>
            <p>Acompanhe o progresso do nosso projeto!</p>
        </div>
    </div>
    <div>
        <div>
            <h2>Planejado</h2>
            <ol>
                <?php
                $stmt_planejado = $conexao->prepare("SELECT titulo, descricao FROM roadmap WHERE status = 0");
                $stmt_planejado->execute();
                $resultado = $stmt_planejado->get_result();
                $stmt_planejado->close();
                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                ?>
                        <li>
                            <h3><?= $row["titulo"] ?></h3>
                            <p><?= $row["descricao"] ?></p>
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
                $stmt_planejado = $conexao->prepare("SELECT titulo, descricao FROM roadmap WHERE status = 1");
                $stmt_planejado->execute();
                $resultado = $stmt_planejado->get_result();
                $stmt_planejado->close();
                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                ?>
                        <li>
                            <h3><?= $row["titulo"] ?></h3>
                            <p><?= $row["descricao"] ?></p>
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
                $stmt_planejado = $conexao->prepare("SELECT titulo, descricao FROM roadmap WHERE status = 2");
                $stmt_planejado->execute();
                $resultado = $stmt_planejado->get_result();
                $stmt_planejado->close();
                if ($resultado->num_rows > 0):
                    while ($row = $resultado->fetch_assoc()):
                ?>
                        <li>
                            <h3><?= $row["titulo"] ?></h3>
                            <p><?= $row["descricao"] ?></p>
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
<?php include("../../includes/fim.php") ?>