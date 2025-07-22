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
        <a class="px-5 py-2 bg-lime-600 text-white rounded-lg" href="#"><i class="bi bi-download"></i> Exportar</a>
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
    <div class="w-full border border-gray-300/80 rounded-lg overflow-hidden">
        <form class="grid grid-cols-3 gap-4 p-5">
            <select class="input-filtro" name="atendente" id="atendente">
                <option value="0" disabled <?= ((isset($_GET['atendente'])) ? '' : 'selected') ?>>Buscar por atendente
                </option>
                <?php
                $stmt = $conexao->prepare("SELECT id, nome FROM usuarios");
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
                        $sql = "SELECT id, usuario_id, total, data_venda FROM vendas WHERE usuario_id = ? AND DATE(data_venda) = ? ORDER BY data_venda DESC";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("ss", $atendente, $data);
                    } elseif (!empty($atendente) && empty($data)) {
                        $sql = "SELECT id, usuario_id, total, data_venda FROM vendas WHERE usuario_id = ? ORDER BY data_venda DESC";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("s", $atendente);
                    } elseif (empty($atendente) && !empty($data)) {
                        $sql = "SELECT id, usuario_id, total, data_venda FROM vendas WHERE DATE(data_venda) = ? ORDER BY data_venda DESC";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("s", $data);
                    } else {
                        $sql = "SELECT id, usuario_id, total, data_venda FROM vendas ORDER BY data_venda DESC";
                        $stmt = $conexao->prepare($sql);
                    }
                    $stmt->execute();

                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                    ?>
                    <tr class="text-center">
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
                            <form id="btn-edita" action="#">
                                <button><i class="bi bi-pencil-square"></i></button>
                            </form>
                            <form id="btn-deleta" action="#">
                                <button><i class="bi bi-trash3"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
                <?php else: ?>
                <?php $_SESSION['resposta'] = "Sem registros!" ?>
                <?php endif ?>
            </table>
        </div>
    </div>
</div>
<?php include("../../includes/fim.php") ?>