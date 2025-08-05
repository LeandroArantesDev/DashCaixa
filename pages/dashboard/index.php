<?php
// validando se o usuario é adm ou fundador para acessar
include("../../includes/valida_adm.php");

$titulo = "Dashboard";
include("../../backend/funcoes/dashboard-historico.php");
include("../../includes/inicio.php");
?>
<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Dashboard</h1>
            <p>Visão geral do sistema</p>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-5">
        <div id="vendas" class="card-topo">
            <div class="txt-card">
                Vendas do dia <br> <span><?= htmlspecialchars(buscar_vendas_diarias()) ?></span>
            </div>
            <i class="bi bi-cart-check"></i>
        </div>
        <div id="faturamento" class="card-topo">
            <div class="txt-card">
                Faturamento do dia <br> <span><?= htmlspecialchars(formatarPreco(buscar_faturamento_diario())) ?></span>
            </div>
            <i class="bi bi-cash-coin"></i>
        </div>
        <div id="produtos" class="card-topo">
            <div class="txt-card">
                Produtos cadastrados <br> <span><?= htmlspecialchars(buscar_produto_total()) ?></span>
            </div>
            <i class="bi bi-boxes"></i>
        </div>
        <div id="categorias" class="card-topo">
            <div class="txt-card">
                Categorias cadastradas <br> <span><?= htmlspecialchars(buscar_categoria_total()) ?></span>
            </div>
            <i class="bi bi-tags"></i>
        </div>
    </div>

    <div class="flex justify-between gap-3">
        <div class="flex flex-col gap-5 border border-yellow-300 bg-yellow-200/20 p-5 rounded-xl min-w-1/3">
            <div class="flex gap-2 text-xl text-amber-700">
                <i
                    class="bi bi-exclamation-triangle flex items-center justify-center w-8 h-8 rounded-md text-yellow-600 bg-yellow-600/20"></i>
                <h2>Alerta de Estoque Baixo</h2>
            </div>
            <?php
            $alertas = buscar_alerta_estoque_baixo();
            foreach ($alertas as $produto):
            ?>
                <p class="flex items-center justify-between w-full bg-white p-3 rounded-xl font-bold">
                    <?= htmlspecialchars($produto['nome']) ?> <span
                        class="text-red-500"><?= htmlentities($produto['estoque']) ?> Unidades</span>
                </p>
            <?php endforeach; ?>
        </div>
        <div class="flex flex-col gap-4 border border-gray-300/80 bg-white p-5 rounded-xl w-full h-full">
            <div class="flex items-center gap-2 text-xl font-semibold text-gray-700">
                <i
                    class="bi bi-graph-up flex items-center justify-center w-10 h-10 rounded-md text-2xl text-sky-500 bg-sky-500/10"></i>
                <h2>Faturamento dos Últimos 7 Dias</h2>
            </div>

            <div class="flex flex-col gap-3">
                <?php
                $faturamento_semanal = buscar_faturamento_semanal();

                $max_faturamento = !empty($faturamento_semanal) ? max(array_column($faturamento_semanal, 'faturamento')) : 0;

                $dias_semana_map = [
                    'Sunday'    => 'Dom',
                    'Monday'    => 'Seg',
                    'Tuesday'   => 'Ter',
                    'Wednesday' => 'Qua',
                    'Thursday'  => 'Qui',
                    'Friday'    => 'Sex',
                    'Saturday'  => 'Sáb'
                ];

                foreach ($faturamento_semanal as $item):
                    $date_obj = new DateTime($item['dia']);
                    $dia_em_ingles = $date_obj->format('l');

                    $dia_abreviado = $dias_semana_map[$dia_em_ingles] ?? $item['dia'];

                    $largura_percentual = ($max_faturamento > 0) ? ($item['faturamento'] / $max_faturamento) * 100 : 0;
                ?>

                    <div class="flex items-center gap-4 text-sm">
                        <span class="w-8 font-medium text-gray-500"><?= htmlspecialchars($dia_abreviado) ?></span>
                        <div class="relative w-full h-5 bg-gray-200/80 rounded-full">
                            <div class="absolute top-0 left-0 h-full bg-blue-600 rounded-full flex items-center justify-end pr-3"
                                style="width: <?= $largura_percentual ?>%;">
                                <span class="font-bold text-white">
                                    <?= htmlspecialchars(formatarPreco($item['faturamento'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php include("../../includes/fim.php") ?>