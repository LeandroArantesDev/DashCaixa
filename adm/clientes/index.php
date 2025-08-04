<?php
$titulo = "Clientes";
include("../../includes/inicio.php");
?>
    <div class="conteudo">
        <div class="titulo">
            <div class="txt-titulo">
                <h1>Gestão de Clientes</h1>
                <p>Gerencie os clientes do sistema</p>
            </div>
            <button onclick="modalCadastrar()"><i class="bi bi-plus-lg"></i> Novo Cliente</button>
        </div>
        <div class="tabela-form">
            <form class="grid grid-cols-3">
                <input class="input-filtro col-span-2" type="text" name="busca" id="busca"
                       placeholder="Buscar por nome ou email...">
                <button type="submit"><i class="bi bi-search"></i> Buscar</button>
            </form>
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Mensalidade</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $busca = $_GET['busca'] ?? '';

                    $busca_like = "%" . $busca . "%";

                    // buscando todos clientes
                    $stmt = $conexao->prepare("SELECT id, nome, email, status, status_mensalidade FROM clientes WHERE (nome LIKE ? OR email LIKE ?) AND id != ?");
                    $stmt->bind_param("ssi", $busca_like, $busca_like, $_SESSION['cliente_id']);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="celula-tabela flex justify-center items-center">
                                    <div class="flex gap-2 items-center w-1/2">
                                        <i class="bi bi-person flex items-center justify-center w-8 h-8 p-1 bg-blue-600/20 text-blue-600 rounded-full"></i>
                                        <p>
                                            <?= htmlspecialchars($row['nome']) ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="celula-tabela">
                                    <?php
                                    $status_mensalidade = $row['status_mensalidade'];
                                    if ($status_mensalidade == 1 || $status_mensalidade == 2) {
                                        $mensalidade = "Pendente";
                                    } else {
                                        $mensalidade = "Pago";
                                    }
                                    echo htmlspecialchars($mensalidade);
                                    ?>
                                </td>
                                <td id="td-acoes" class="celula-tabela" colspan="2">
                                    <button id="btn-edita" class="botao-informativo"
                                            onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                        <i class="bi bi-pencil-square icon"></i>
                                        <span class="tooltip">Editar</span>
                                    </button>
                                    <form id="btn-deleta" action="../../backend/usuarios/deletar.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                        <button class="botao-informativo">
                                            <i class="bi bi-trash3"></i>
                                            <span class="tooltip">Deletar</span>
                                        </button>
                                    </form>
                                    <form id="btn-status" action="../../backend/usuarios/status.php" method="POST">
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
                                            <span class="tooltip">Ocultar</span>
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