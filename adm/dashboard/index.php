<?php
// impedindo usuarios não fundadores de acessar nossas paginas
include("../../includes/valida_fundador.php");

$titulo = "Dashboard";
include("../../backend/funcoes/dashboard-fundadores.php");
include("../../includes/inicio.php");
?>

<div class="conteudo">
    <div class="titulo">
        <div class="txt-titulo">
            <h1>Dashboard</h1>
            <p>Visão geral do sistema e nossos clientes</p>
        </div>
    </div>

    <!-- 🔷 CARDS -->
    <div class="grid grid-cols-4 gap-4 mb-5">
        <div class="card-topo">
            <div class="txt-card">
                Clientes Ativos<br>
                <span><?= totalClientesAtivos() ?></span>
            </div>
            <i class="bi bi-person-lines-fill"></i>
        </div>

        <div class="card-topo">
            <div class="txt-card">
                Receita Mensal<br>
                <span><?= formatarPreco(receitaMensal()) ?></span>
            </div>
            <i class="bi bi-cash-stack"></i>
        </div>

        <div class="card-topo">
            <div class="txt-card">
                Adimplência<br>
                <span><?= taxaAdimplencia() ?>%</span>
            </div>
            <i class="bi bi-check-circle"></i>
        </div>

        <div class="card-topo">
            <div class="txt-card">
                Usuários Ativos<br>
                <span><?= totalUsuarios() ?></span>
            </div>
            <i class="bi bi-people-fill"></i>
        </div>
    </div>

    <!-- 📊 GRÁFICOS -->
    <div class="flex gap-4 mb-5">
        <!-- Receita últimos 6 meses -->
        <div class="flex-1 border p-5 rounded-xl bg-fundo-conteudo">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="bi bi-bar-chart-line"></i> Receita Últimos 6 Meses
            </h2>

            <?php
            $dados = receitaUltimos6Meses();
            $max = !empty($dados) ? max(array_column($dados, 'receita')) : 0;

            foreach ($dados as $item):
                $largura = ($max > 0) ? ($item['receita'] / $max) * 100 : 0;
                ?>
                <div class="mb-2">
                    <span class="block text-sm font-medium text-gray-500"><?= $item['mes'] ?></span>
                    <div class="h-5 relative bg-gray-200 rounded-full">
                        <div class="h-5 bg-principal rounded-full text-white flex items-center justify-end pr-2"
                             style="width: <?= $largura ?>%;">
                            <?= formatarPreco($item['receita']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Crescimento de clientes -->
        <div class="flex-1 border p-5 rounded-xl bg-fundo-conteudo">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="bi bi-people"></i> Novos Clientes
            </h2>

            <?php
            $clientes = crescimentoClientes();
            $max = !empty($clientes) ? max(array_column($clientes, 'total')) : 0;

            foreach ($clientes as $item):
                $largura = ($max > 0) ? ($item['total'] / $max) * 100 : 0;
                ?>
                <div class="mb-2">
                    <span class="block text-sm font-medium text-gray-500"><?= $item['mes'] ?></span>
                    <div class="h-5 relative bg-gray-200 rounded-full">
                        <div class="h-5 bg-principal rounded-full text-white flex items-center justify-end pr-2"
                             style="width: <?= $largura ?>%;">
                            <?= $item['total'] ?> clientes
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ⚠️ ERROS -->
    <div class="grid grid-cols-2 gap-4">
        <div class="p-5 rounded-xl bg-yellow-200/20 border border-yellow-300">
            <h2 class="flex gap-2 text-xl text-amber-700">
                <i class="bi bi-exclamation-triangle"></i> Clientes com mais erros </h2>

            <?php $clientesComErros = clientesComMaisErros(); // Renomeei a variável para o plural ?>

            <?php if ($clientesComErros): ?>
                <?php foreach ($clientesComErros as $cliente): // Inicia o loop para cada cliente ?>
                    <p class="mt-2 text-lg font-bold">
                        <?= htmlspecialchars($cliente['nome']) ?> <span class="text-red-500">
                    (<?= $cliente['total'] ?> erros) </span>
                    </p>
                <?php endforeach; // Termina o loop ?>
            <?php else: ?>
                <p class="mt-2 text-gray-600">Nenhum erro registrado.</p>
            <?php endif; ?>
        </div>

        <div class="p-5 rounded-xl bg-red-200/20 border border-red-300">
            <h2 class="flex gap-2 text-xl text-red-700">
                <i class="bi bi-bug"></i> Rotas com mais erros </h2>

            <?php $rotasErro = rotasComMaisErros(); ?>

            <?php if ($rotasErro): ?>
                <?php foreach ($rotasErro as $rota): ?>
                    <p class="mt-2 text-lg font-bold">
                        <?= htmlspecialchars($rota['rota']) ?> <span class="text-red-500">
                    (<?= $rota['total'] ?> erros) </span>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="mt-2 text-gray-600">Nenhum erro registrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("../../includes/fim.php"); ?>
