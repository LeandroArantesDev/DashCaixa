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
    $stmt = $conexao->prepare("SELECT id, cliente_id, valor, data_vencimento, url_pagamento FROM mensalidades WHERE status IN (1,2)");
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();

    while ($row = $resultado->fetch_assoc()):
        ?>
        <div class="bg-white p-3 rounded-lg border-2 border-[var(--cinza-borda)]">
            <h3>Fatura Pendente</h3>
            <p>Vencimento: <?php echo htmlspecialchars($row['data_vencimento']); ?></p>
            <p>Valor: R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></p>

            <?php if (isset($row['url_pagamento']) && $row['url_pagamento'] != NULL): ?>
                <a class="flex items-center justify-center p-2 w-full bg-sky-500 rounded-lg text-white cursor-pointer"
                   href="<?= htmlspecialchars($row['url_pagamento']) ?>">Pagar com Pix</a>
            <?php else: ?>
                <form action="../../backend/mensalidade/gerar_pagamento.php" method="POST">
                    <input type="hidden" name="fatura_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="valor" value="<?php echo $row['valor']; ?>">
                    <button class="p-2 w-full bg-sky-500 rounded-lg text-white cursor-pointer" type="submit">Pagar com
                        pix
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    <script>
        const div_pendente = document.getElementById("div-pendente");
        const div_pago = document.getElementById("div-pago");

        function simularPago() {
            if (div_pago.style.display === "none") {
                div_pendente.style.display = "none";
                div_pago.style.display = "block";
            } else {
                div_pendente.style.display = "block";
                div_pago.style.display = "none";
            }
        }
    </script>
    <?php include("../../includes/fim.php"); ?>
