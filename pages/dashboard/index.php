<?php
include("../../backend/dashboard/funcoes.php");
$titulo = "Dashboard";
$css = "index";
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <h1 class="iz">Dashboard</h1>
    <h2>Visão geral do sistema</h2>
    <div class="bg-red-500">
        <article>
        </article>
        <p>Vendas do dia: <?= htmlspecialchars(buscar_vendas_diarias()) ?></p>
    </div>

    <p>Faturamento do dia: <?= htmlspecialchars(buscar_faturamento_diario()) ?></p>
    <p>Faturamento total: <?= htmlspecialchars(buscar_faturamento_total()) ?></p>
    <p>Produtos cadastrados: <?= htmlspecialchars(buscar_produto_total()) ?></p>
    <p>Categorias cadastradas: <?= htmlspecialchars(buscar_categoria_total()) ?></p>
    <hr>
    <p>Alerta de estoque baixo</p>
    <?php
    $alertas = buscar_alerta_estoque_baixo();
    foreach ($alertas as $produto):
    ?>
        <p>Produto: <?= htmlspecialchars($produto['nome']) ?> - Estoque: <?= htmlentities($produto['estoque']) ?></p>
    <?php endforeach; ?>
    <hr>
    <p>Faturamento Semanal</p>
    <?php
    $faturamento_semanal = buscar_faturamento_semanal();
    foreach ($faturamento_semanal as $item):
    ?>
        <p>Dia: <?= htmlspecialchars($item['dia']) ?> - Faturamento:
            <?= htmlspecialchars(formatarPreco($item['faturamento'])) ?></p>
    <?php endforeach; ?>
    <hr>

    <?php
    $_SESSION["resposta"] = "Olá";
    ?>

    <script>
        window.alert(<?= $_SESSION["resposta"] ?>);
    </script>
</div>
<?php include("../../includes/fim.php") ?>