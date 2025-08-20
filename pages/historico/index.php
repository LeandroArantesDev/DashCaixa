<?php
$titulo = "Histórico de Vendas";
include("../../includes/inicio.php");
include("../../backend/funcoes/dashboard-historico.php");
?>
<div class="conteudo">
    <!-- Iframe para imprimir a ficha -->
    <iframe id="iframe-ficha" src="" class="hidden"></iframe>
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Histórico de Vendas</h1>
            <p>Acompanhe todas as vendas realizadas</p>
        </div>
        <!-- <a class="exportar" href="#"><i class="bi bi-download"></i> Exportar</a> -->
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
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
        <form class="grid grid-cols-2 lg:grid-cols-3">
            <select class="input-filtro col-span-2 lg:col-span-1" name="atendente" id="atendente">
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
            <input class="input-filtro " type="date" name="data" id="data"
                value="<?= ((isset($_GET['data']) ? $_GET['data'] : date('Y-m-d'))) ?>">
            <div class="flex items-center justify-center gap-3 ">
                <a class="flex items-center justify-center gap-2 w-1/2 border border-borda/80 hover:bg-gray-300 h-full rounded-lg"
                    href="<?= BASE_URL . "pages/historico" ?>">
                    <i class="bi bi-trash3"></i>
                    <span class="hidden lg:block">
                        Limpar Filtros
                    </span>
                </a>
                <button class="w-1/2 flex justify-center items-center gap-2" type="submit"><i class="bi bi-funnel"></i>
                    <span class="hidden lg:block">
                        Aplicar Filtros
                    </span>
                </button>
            </div>
        </form>
        <div class="table-container overflow-x-auto">
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
                                <td class="celula-tabela px-8 lg:px-0"><?= htmlspecialchars($row['id']) ?></td>
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
                                <td class="celula-tabela"><?= htmlspecialchars(formatarDataHorario($row['data_venda'])) ?></td>
                                <td id="td-acoes" class="celula-tabela" colspan="2">
                                    <form id="btn-deleta" method="POST" action="../../backend/historico/deletar.php"
                                        target="_self" onclick="return confirm('Tem certeza que deseja deletar esta venda?')">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars(gerarCSRF()) ?>">
                                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button class="botao-informativo">
                                            <i class="bi bi-trash3"></i>
                                            <span class="tooltip">Deletar</span>
                                        </button>
                                    </form>
                                    <button id="btn-imprimir" class="botao-informativo"
                                        onclick="this.disabled = true; abrirModal(<?= $row['id'] ?>);setTimeout(() => { this.disabled = false; }, 3000);"><i
                                            class="bi bi-printer text-principal"></i><span
                                            class="tooltip">Imprimir</span></button>
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
<script>
    function abrirModal(id) {
        const iframe = document.getElementById("iframe-ficha");

        // Define o src do iframe com o ID da venda
        iframe.src = `../../backend/ficha/ficha.php?id=${id}`;

        // Remove listeners antigos
        iframe.onload = null;
        iframe.onerror = null;

        // Adiciona listener para quando carregar
        iframe.onload = function() {
            console.log('Iframe carregado com sucesso');

            try {
                // Aguarda um pouco e então imprime
                setTimeout(() => {
                    iframe.contentWindow.print();
                }, 1000);
            } catch (error) {
                console.error('Erro ao imprimir do iframe:', error);
                // Fallback: abre em nova janela
                window.open(iframe.src, '_blank');
            }
        };

        // Adiciona listener para erros
        iframe.onerror = function() {
            console.error('Erro ao carregar o iframe');
            alert('Erro ao carregar a ficha. Tente novamente.');
        };
    }
</script>
<?php include("../../includes/fim.php") ?>