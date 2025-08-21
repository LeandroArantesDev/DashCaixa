<?php
// impedindo usuarios não fundadores de acessar nossas paginas
include("../../includes/valida_fundador.php");

$titulo = "Clientes";
include("../../includes/inicio.php");
?>
    <div class="conteudo">
        <div class="titulo">
            <div class="txt-titulo">
                <h1>Erros</h1>
                <p>Todos erros reportados pelo sistema</p>
            </div>
        </div>
        <div class="tabela-form">
            <form class="grid grid-cols-3">
                <input class="input-filtro col-span-2" type="text" name="busca" id="busca"
                       placeholder="Buscar por codigo ou mensagem...">
                <button type="submit"><i class="bi bi-search"></i> Buscar</button>
            </form>
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Usúario</th>
                        <th>Rota</th>
                        <th>Mensagem</th>
                        <th>IP</th>
                        <th>Navegador</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $busca = $_GET['busca'] ?? '';

                    $busca_like = "%" . $busca . "%";

                    // buscando todos erros
                    $stmt = $conexao->prepare("SELECT id, cliente_id, usuario_id, rota, mensagem, codigo, ip, navegador, criado_em FROM erros WHERE (codigo LIKE ? OR mensagem LIKE ?)");
                    $stmt->bind_param("ss", $busca_like, $busca_like);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $stmt->close();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="celula-tabela"><?= htmlspecialchars($row['codigo']) ?></td>
                                <td class="celula-tabela">
                                    <?php
                                    // buscando o nome do usuario
                                    $stmt = $conexao->prepare("SELECT nome FROM usuarios WHERE id = ?");
                                    $stmt->bind_param("i", $row['usuario_id']);
                                    $stmt->execute();
                                    $stmt->bind_result($nome_usuario);
                                    $stmt->fetch();
                                    $stmt->close();
                                    echo $nome_usuario;
                                    ?>
                                </td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['rota']) ?></td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['mensagem']) ?></td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['ip']) ?></td>
                                <td class="celula-tabela"><?= htmlspecialchars($row['navegador']) ?></td>
                                <td class="celula-tabela"><?= htmlspecialchars(formatarData($row['criado_em'])) ?></td>
                                <td id="td-acoes" class="celula-tabela">
                                    <form id="btn-deleta" action="../../backend/usuarios/deletar.php" method="POST"
                                          onclick="return confirm('Tem certeza que deseja deletar este usuário?')">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                        <button class="botao-informativo">
                                            <i class="bi bi-trash3"></i>
                                            <span class="tooltip">Deletar</span>
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
<?php include("../../includes/fim.php") ?>