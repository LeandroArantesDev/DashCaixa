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
        <a href="#"><i class="bi bi-plus-lg"></i> Novo Usuário</a>
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
                    $stmt = $conexao->prepare("SELECT nome, email, tipo, ultimo_acesso FROM usuarios WHERE nome LIKE ? OR email LIKE ?");
                    $stmt->bind_param("ss", $busca_like, $busca_like);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                    ?>
                            <tr>
                                <td class="celula-tabela">
                                    <?php if ($row['tipo'] == 1): ?>
                                        <i class="bi bi-shield"></i>
                                    <?php else: ?>
                                        <i class="bi bi-person"></i>
                                    <?php endif ?>
                                    <?= htmlspecialchars($row['nome']) ?>
                                </td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="celula-tabela">
                                    <?= htmlspecialchars(($row['tipo']) == 1) ? 'Administrador' : 'Caixa' ?>
                                </td>
                                <td class="celula-tabela">
                                    <?= htmlspecialchars((empty($row['ultimo_acesso'])) ? 'N/A' : $row['ultimo_acesso']) ?></td>
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