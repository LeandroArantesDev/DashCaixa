<?php
$titulo = "Mensalidade";
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Mensalidade</h1>
            <p>Acompanhe o status do seu pagamento mensal</p>
        </div>
    </div>
    <?php
    $stmt = $conexao->prepare("
    SELECT id, cliente_id, valor, data_vencimento, url_pagamento
    FROM mensalidades
    WHERE cliente_id = ? AND status IN (1, 2)
      AND (
        DATEDIFF(data_vencimento, CURRENT_DATE) <= 5
      );
");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row_pendente = $resultado->fetch_assoc();
    $stmt->close();

    if ($resultado->num_rows > 0):
        ?>
        <!-- div da mensalidade pendente -->
        <div class="flex items-center justify-center w-full">
            <div class="flex flex-col justify-center items-center gap-3 bg-white p-5 rounded-lg border border-[var(--cinza-borda)]">
                <i class="bi bi-exclamation-triangle flex items-center justify-center text-3xl rounded-full w-12 h-12 bg-amber-100 text-amber-600"></i>
                <h2>Mensalidade Pendente!</h2>
                <p>Identificamos que a mensalidade ainda não foi paga.</p>
                <div>
                    <h3><i class="bi bi-currency-dollar"></i> Detalhes da Mensalidade</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <p>
                            Dias até vencer:
                            <?php
                            $dataVencimentoStr = $row_pendente['data_vencimento'];

                            $dataVencimento = new DateTime($dataVencimentoStr);
                            $hoje = new DateTime('today');

                            $diferenca = $hoje->diff($dataVencimento);

                            if ($diferenca->invert == 1) {
                                echo '<strong style="color: #ef4444;">Vencida</strong>';
                            } elseif ($diferenca->days == 0) {
                                echo '<strong style="color: #f97316;">Vence hoje</strong>';
                            } else {
                                $dias = $diferenca->days;
                                $textoDias = ($dias == 1) ? 'dia' : 'dias';
                                echo "Faltam " . $dias . " " . $textoDias;
                            }
                            ?>
                        </p>
                        <p>
                            Vencimento:
                            <?php
                            $dataDoBanco = $row_pendente['data_vencimento'];
                            $dataObj = new DateTime($dataDoBanco);
                            echo htmlspecialchars($dataObj->format('d/m/Y'));
                            ?>
                        </p>
                        <p>
                            Valor: R$
                            <?= number_format($row_pendente['valor'], 2, ',', '.'); ?>
                        </p>
                        <p>Status: Pendente</p>
                    </div>
                </div>
                <?php if (isset($row_pendente['url_pagamento']) && $row_pendente['url_pagamento'] != NULL): ?>
                    <a class="flex items-center justify-center p-2 w-full bg-sky-500 rounded-lg text-white cursor-pointer"
                       href="<?= htmlspecialchars($row_pendente['url_pagamento']) ?>">Pagar com Pix</a>
                <?php else: ?>
                    <form action="../../backend/mensalidade/gerar_pagamento.php" method="POST">
                        <input type="hidden" name="fatura_id" value="<?php echo $row_pendente['id']; ?>">
                        <input type="hidden" name="valor" value="<?php echo $row_pendente['valor']; ?>">
                        <button class="p-2 w-full bg-sky-500 rounded-lg text-white cursor-pointer" type="submit">
                            Pagar com pix
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        <?php
        $stmt = $conexao->prepare("SELECT data_pagamento, valor FROM mensalidades WHERE cliente_id = ? AND status = 0 ORDER BY data_pagamento DESC LIMIT 1 ");
        $stmt->bind_param("i", $_SESSION['cliente_id']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row_pago = $resultado->fetch_assoc();
        $stmt->close();
        ?>
        <?php if ($row_pago): ?>
            <!-- div da mensalidade paga -->
            <div class="flex items-center justify-center w-full">
                <div class="flex flex-col justify-center items-center gap-3 bg-white p-5 rounded-lg border border-[var(--cinza-borda)]">
                    <i class="bi bi-check-lg flex items-center justify-center text-4xl rounded-full w-12 h-12 bg-green-200 text-green-600"></i>
                    <h2>Você está em dia!</h2>
                    <p>Agradecemos por manter seu pagamento em dia.</p>
                    <div>
                        <h3><i class="bi bi-check2-circle"></i> Status do Pagamento</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <p>
                                Proximo Vencimento:
                                <?php
                                // buscando a data da proxima fatura
                                $stmt = $conexao->prepare("SELECT data_vencimento FROM mensalidades WHERE cliente_id = ? AND status = 1 ORDER BY data_vencimento DESC LIMIT 1");
                                $stmt->bind_param("i", $_SESSION['cliente_id']);
                                $stmt->execute();
                                $stmt->bind_result($data_vencimento);
                                $stmt->fetch();
                                $stmt->close();

                                $dataDoBanco = $data_vencimento;
                                $dataObj = new DateTime($dataDoBanco);
                                echo htmlspecialchars($dataObj->format('d/m/Y'));
                                ?>
                            </p>
                            <p>
                                Último Pagamento:
                                <?php
                                $dataDoBanco = $row_pago['data_pagamento'];

                                $dataObj = new DateTime($dataDoBanco);

                                echo htmlspecialchars($dataObj->format('d/m/Y'));
                                ?>
                            </p>
                            <p>
                                Valor: R$
                                <?= number_format($row_pago['valor'], 2, ',', '.'); ?>
                            </p>
                            <p>Status: ✓ Em Dia</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- div caso nenhuma mensalidade for encontrada -->
            <p>Nenhuma mensalidade encontrada.</p>
        <?php endif; ?>
    <?php endif; ?>
    <?php include("../../includes/fim.php"); ?>
