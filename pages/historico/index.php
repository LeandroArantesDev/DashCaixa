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
    <div class="w-full border border-gray-300/80 rounded-lg">
        <form class="grid grid-cols-3 gap-4 p-5">
            <input class="input-filtro" type="text" name="atendente" id="atendente" placeholder="Buscar por atendente">
            <input class="input-filtro" type="date" name="data" id="data" value="<?= date('Y-m-d') ?>">
            <button type="submit"><i class="bi bi-funnel"></i> Aplicar Filtros</button>
        </form>
        <div class="w-full overflow-auto p-5">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Atendente</th>
                        <th>Total</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // buscando todas as vendas ja feitas
                    $stmt = $conexao->prepare("SELECT id, usuario_id, total, data_venda FROM vendas ORDER BY data_venda ASC");
                    $stmt->execute();

                    $resultado = $stmt->get_result();

                    while ($row = $resultado->fetch_assoc()):
                    ?>
                    <tr class="text-center">
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['usuario_id']) ?></td>
                        <td><?= htmlspecialchars($row['total']) ?></td>
                        <td><?= htmlspecialchars($row['data_venda']) ?></td>
                        <td class="flex gap-2 items-center justify-center" colspan="2">
                            <form action="#">
                                <button><i class="bi bi-pencil-square"></i></button>
                            </form>
                            <form action="#">
                                <button><i class="bi bi-trash3"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("../../includes/fim.php") ?>