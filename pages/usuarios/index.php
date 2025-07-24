<?php
$titulo = "Usuarios";
include("../../includes/inicio.php");
include("../../includes/valida_adm.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Gestão de Usuários</h1>
            <p>Gerencie os usuários do sistema</p>
        </div>
        <button onclick="modalCadastrar()"><i class="bi bi-plus-lg"></i> Novo Usuário</button>
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
                        <th>Usuario</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Último Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $busca = $_GET['busca'] ?? '';

                    $busca_like = "%" . $busca . "%";

                    // buscando todos usuarios
                    $stmt = $conexao->prepare("SELECT id, nome, email, tipo, ultimo_acesso, status FROM usuarios WHERE (nome LIKE ? OR email LIKE ?) AND status IN (0, 1)");
                    $stmt->bind_param("ss", $busca_like, $busca_like);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="celula-tabela flex justify-center items-center">
                            <div class="flex gap-2 items-center w-1/2">
                                <?php if ($row['tipo'] == 1): ?>
                                <i
                                    class="bi bi-shield flex items-center justify-center w-8 h-8 p-1 bg-purple-600/20 text-purple-600 rounded-full"></i>
                                <?php else: ?>
                                <i
                                    class="bi bi-person flex items-center justify-center w-8 h-8 p-1 bg-blue-600/20 text-blue-600 rounded-full"></i>
                                <?php endif ?>
                                <p>
                                    <?= htmlspecialchars($row['nome']) ?>
                                </p>
                            </div>
                        </td>
                        <td class="celula-tabela"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="celula-tabela">
                            <?= htmlspecialchars(($row['tipo']) == 1) ? 'Administrador' : 'Caixa' ?>
                        </td>
                        <td class="celula-tabela">
                            <?php
                                    $dataDoBanco = $row['ultimo_acesso'];

                                    if ($dataDoBanco) {
                                        $dataObj = new DateTime($dataDoBanco);
                                        echo htmlspecialchars($dataObj->format('d/m/Y H:i'));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                        </td>
                        <td id="td-acoes" class="celula-tabela" colspan="2">
                            <button id="btn-edita" onclick="modalEditar(<?= htmlspecialchars($row['id']) ?>)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form id="btn-deleta" action="../../backend/usuarios/deletar.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                <button><i class="bi bi-trash3"></i></button>
                            </form>
                            <form id="btn-status" action="../../backend/usuarios/status.php" method="POST">
                                <!-- inputs escondidos -->
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                <input type="hidden" name="status" value="<?= $row['status'] ?>">
                                <button>
                                    <?php if ($row['status'] == 1) : ?>
                                    <i class="bi bi-eye-slash"></i>
                                    <?php elseif ($row['status'] == 0) : ?>
                                    <i class="bi bi-eye"></i>
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