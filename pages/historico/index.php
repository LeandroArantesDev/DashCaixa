<?php
$titulo = "Histórico de Vendas";
$css = "historico";
include("../../includes/inicio.php");
include("../../backend/funcoes/dashboard-historico.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Histórico de Vendas</h1>
            <p>Acompanhe todas as vendas realizadas</p>
        </div>
        <a class="exportar" href="#"><i class="bi bi-download"></i> Exportar</a>
    </div>
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div id="vendas" class="card">
            <div class="txt-card">
                <p>Total de Vendas</p>
                <span><?= buscar_vendas_diarias() ?></span>
            </div>
            <i class="bi bi-cart"></i>
        </div>
        <div id="faturamento" class="card">
            <div class="txt-card">
                <p>Faturamento</p>
                <span><?= formatarPreco(buscar_faturamento_diario()) ?></span>
            </div>
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div id="media" class="card">
            <div class="txt-card">
                <p>Media de Vendas</p>
                <span><?= formatarPreco(media_preco_vendas()) ?></span>
            </div>
            <i class="bi bi-calculator-fill"></i>
        </div>
    </div>
    <div class="tabela-form">
        <form class="grid grid-cols-3">
            <select class="input-filtro" name="atendente" id="atendente">
                <option value="0" disabled <?= ((isset($_GET['atendente'])) ? '' : 'selected') ?>>Buscar por
                    atendente
                </option>
                <?php
                $stmt = $conexao->prepare("SELECT id, nome FROM usuarios WHERE cliente_id = ? AND status IN (0, 1)");
                $stmt->bind_param("i", $_SESSION['cliente_id']);
                $stmt->execute();
                $resultado = $stmt->get_result();

                while ($row = $resultado->fetch_assoc()):
                ?>
                    <option value="<?= htmlspecialchars($row['id']) ?>"
                        <?= ((isset($_GET['atendente']) && $_GET['atendente'] == $row['id']) ? 'selected' : '') ?>>
                        <?= htmlspecialchars($row['nome']) ?>
                    </option>
                <?php endwhile ?>
            </select>
            <input class="input-filtro" type="date" name="data" id="data"
                value="<?= ((isset($_GET['data']) ? $_GET['data'] : date('Y-m-d'))) ?>">
            <div class="flex items-center justify-center gap-3">
                <a class="flex items-center justify-center gap-2 w-1/2 border border-gray-300/80 h-full rounded-lg"
                    href="<?= BASE_URL . "pages/historico" ?>">
                    <i class="bi bi-trash3"></i> Limpar Filtros
                </a>
                <button class="w-1/2" type="submit"><i class="bi bi-funnel"></i> Aplicar Filtros</button>
            </div>
        </form>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Atendente</th>
                        <th>Total</th>
                        <th>Data e Hora</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $atendente = $_GET['atendente'] ?? '';
                    $data = $_GET['data'] ?? '';

                    // buscando vendas ja feitas
                    if (!empty($atendente) && !empty($data)) {
                        $stmt = $conexao->prepare("SELECT id, usuario_id, total, data_venda FROM vendas WHERE cliente_id = ? AND usuario_id = ? AND DATE(data_venda) = ? ORDER BY data_venda DESC");
                        $stmt->bind_param("iss", $_SESSION['cliente_id'], $atendente, $data);
                    } elseif (!empty($atendente) && empty($data)) {
                        $stmt = $conexao->prepare("SELECT id, usuario_id, total, data_venda FROM vendas WHERE cliente_id = ? AND usuario_id = ? ORDER BY data_venda DESC");
                        $stmt->bind_param("is", $_SESSION['cliente_id'], $atendente);
                    } elseif (empty($atendente) && !empty($data)) {
                        $stmt = $conexao->prepare("SELECT id, usuario_id, total, data_venda FROM vendas WHERE cliente_id = ? AND DATE(data_venda) = ? ORDER BY data_venda DESC");
                        $stmt->bind_param("is", $_SESSION['cliente_id'], $data);
                    } else {
                        $stmt = $conexao->prepare("SELECT id, usuario_id, total, data_venda FROM vendas WHERE cliente_id = ? ORDER BY data_venda DESC");
                        $stmt->bind_param("i", $_SESSION['cliente_id']);
                    }
                    $stmt->execute();

                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                    ?>
                            <tr>
                                <td class="celula-tabela"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="celula-tabela">
                                    <?php
                                    $stmt = $conexao->prepare("SELECT nome FROM usuarios WHERE id = ?");
                                    $stmt->bind_param("s", $row['usuario_id']);
                                    $stmt->execute();
                                    $stmt->bind_result($atendente);
                                    $stmt->fetch();
                                    $stmt->close();
                                    ?>
                                    <?= htmlspecialchars($atendente) ?>
                                </td>
                                <td class="celula-tabela"><?= formatarPreco(htmlspecialchars($row['total'])) ?></td>
                                <td class="celula-tabela">
                                    <?php
                                    $dataDoBanco = $row['data_venda'];

                                    $dataObj = new DateTime($dataDoBanco);

                                    echo htmlspecialchars($dataObj->format('d/m/Y H:i'));
                                    ?>
                                </td>
                                <td id="td-acoes" class="celula-tabela" colspan="2">
                                    <form id="btn-deleta" method="POST" action="../../backend/historico/deletar.php"
                                        target="_self">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars(gerarCSRF()) ?>">
                                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button class="botao-informativo">
                                            <i class="bi bi-trash3"></i>
                                            <span class="tooltip">Deletar</span>
                                        </button>
                                    </form>
                                    <form id="btn-deleta" method="POST" action="#" target="_self">
                                        <button><i class="bi bi-printer text-sky-500"></i></button>
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