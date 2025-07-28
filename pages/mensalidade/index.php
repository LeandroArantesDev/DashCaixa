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
    <!--TESTES-->
    <?php
    // buscando faturas pendentes ou vencidas do cliente
    $stmt = $conexao->prepare("
    SELECT id, cliente_id, valor, data_vencimento, url_pagamento
FROM mensalidades
WHERE status IN (1, 2)
  AND (
    DATEDIFF(data_vencimento, CURRENT_DATE) = 5 OR
    data_vencimento = CURRENT_DATE OR
    data_vencimento < CURRENT_DATE
  );

    ");
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row_pendente = $resultado->fetch_assoc();
    $stmt->close();

    if ($resultado->num_rows > 0):
        ?>
        <div class="bg-white p-3 rounded-lg border-2 border-[var(--cinza-borda)]">
            <h3>Fatura Pendente</h3>
            <p>Vencimento: <?php echo htmlspecialchars($row_pendente['data_vencimento']); ?></p>
            <p>Valor: R$ <?php echo number_format($row_pendente['valor'], 2, ',', '.'); ?></p>

            <?php if (isset($row_pendente['url_pagamento']) && $row_pendente['url_pagamento'] != NULL): ?>
                <a class="flex items-center justify-center p-2 w-full bg-sky-500 rounded-lg text-white cursor-pointer"
                   href="<?= htmlspecialchars($row_pendente['url_pagamento']) ?>">Pagar com Pix</a>
            <?php else: ?>
                <form action="../../backend/mensalidade/gerar_pagamento.php" method="POST">
                    <input type="hidden" name="fatura_id" value="<?php echo $row_pendente['id']; ?>">
                    <input type="hidden" name="valor" value="<?php echo $row_pendente['valor']; ?>">
                    <button class="p-2 w-full bg-sky-500 rounded-lg text-white cursor-pointer" type="submit">Pagar com
                        pix
                    </button>
                </form>
            <?php endif; ?>
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
        <div>
            <div class="bg-white p-3 rounded-lg border-2 border-[var(--cinza-borda)]">
                <h3>Fatura Paga</h3>
                <?php if ($row_pago): ?>
                    <p>Vencimento: <?php echo htmlspecialchars($row_pago['data_pagamento']); ?></p>
                    <p>Valor: R$ <?php echo number_format($row_pago['valor'], 2, ',', '.'); ?></p>
                <?php else: ?>
                    <p>Nenhum pagamento encontrado.</p>
                    <p>Valor: R$ 0,00</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php include("../../includes/fim.php"); ?>
