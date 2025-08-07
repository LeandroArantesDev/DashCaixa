<?php
$titulo = "Histórico de Vendas";
$css = "historico";
include("../../includes/inicio.php");
include("../../backend/funcoes/dashboard-historico.php");
?>
    <div class="conteudo">
        <!-- Iframe para imprimir a ficha -->
        <iframe id="iframe-ficha" src="" class="hidden"></iframe>
        <div class="titulo">
            <div class="txt-titulo">
                <h1>Histórico de Mensalidades</h1>
                <p>Visualize todas as suas mensalidades</p>
            </div>
        </div>
        <div class="tabela-form">
            <form class="grid grid-cols-3">
                <input class="input-filtro col-span-2" type="date" name="data" id="data"
                       value="<?= ((isset($_GET['data']) ? $_GET['data'] : date('Y-m-d'))) ?>">
                <button class="w-full" type="submit"> Buscar Data</button>
            </form>
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Valor</th>
                        <th>Data Pagamento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = $_GET['data'] ?? '';

                    // buscando todas mensalidades
                    if (!empty($data)) {
                        $stmt = $conexao->prepare("SELECT id, valor, data_pagamento, status FROM mensalidades WHERE cliente_id = ? AND DATE(data_pagamento) = ? ORDER BY data_pagamento DESC");
                        $stmt->bind_param("is", $_SESSION['cliente_id'], $data);
                    } else {
                        $stmt = $conexao->prepare("SELECT id, valor, data_pagamento, status FROM mensalidades WHERE cliente_id = ? ORDER BY data_pagamento DESC");
                        $stmt->bind_param("i", $_SESSION['cliente_id']);
                    }
                    $stmt->execute();

                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows > 0):

                        while ($row = $resultado->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="celula-tabela"><?= formatarPreco(htmlspecialchars($row['valor'])) ?></td>
                                <td class="celula-tabela">
                                    <?php
                                    $dataDoBanco = $row['data_pagamento'];

                                    $dataObj = new DateTime($dataDoBanco);

                                    echo htmlspecialchars($dataObj->format('d/m/Y H:i'));
                                    ?>
                                </td>
                                <td class="celula-tabela">
                                    <?php
                                    // trocando o texto caso for pendente vencida ou paga
                                    if ($row['status'] == 0):?>
                                        <p>Pago</p>
                                    <?php elseif ($row['status'] == 1): ?>
                                        <p>Pendente</p>
                                    <?php elseif ($row['status'] == 2): ?>
                                        <p>Vencida</p>
                                    <?php endif ?>
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
                                    <button id="btn-deleta" class="botao-informativo"
                                            onclick="abrirModal(<?= $row['id'] ?>)"><i
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
            iframe.onload = function () {
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
            iframe.onerror = function () {
                console.error('Erro ao carregar o iframe');
                alert('Erro ao carregar a ficha. Tente novamente.');
            };
        }
    </script>
<?php include("../../includes/fim.php") ?>