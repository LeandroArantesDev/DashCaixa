<?php
// função do menu
function link_ativo(string $pagina): string
{
    if (strpos($_SERVER['REQUEST_URI'], 'pages/' . $pagina) !== false) {
        return 'atual';
    }
    return '';
}

if (isset($_SESSION['id'])):
?>
<aside class="min-w-70 border-r border-gray-300/80 p-4 bg-white">
    <nav class="flex flex-col gap-4">
        <?php if ($_SESSION['tipo'] == 1): ?>
        <a class="link-menu <?php echo link_ativo('dashboard'); ?>" href="<?= BASE_URL ?>pages/dashboard"
            target="_self"><i class="bi bi-columns-gap"></i> Dashboard</a>
        <a class="link-menu <?php echo link_ativo('vendas'); ?>" href="<?= BASE_URL ?>pages/vendas" target="_self"><i
                class="bi bi-cart"></i> Vendas</a>
        <a class="link-menu <?php echo link_ativo('produtos'); ?>" href="<?= BASE_URL ?>pages/produtos"
            target="_self"><i class="bi bi-box-seam"></i> Produtos</a>
        <a class="link-menu <?php echo link_ativo('categorias'); ?>" href="<?= BASE_URL ?>pages/categorias"
            target="_self"><i class="bi bi-tags"></i> Categorias</a>
        <a class="link-menu <?php echo link_ativo('usuarios'); ?>" href="<?= BASE_URL ?>pages/usuarios"
            target="_self"><i class="bi bi-people"></i> Usuarios</a>
        <a class="link-menu <?php echo link_ativo('historico'); ?>" href="<?= BASE_URL ?>pages/historico"
            target="_self"><i class="bi bi-clock-history"></i> Histórico de Vendas</a>
        <?php else: ?>
        <a class="link-menu <?php echo link_ativo('vendas'); ?>" href="<?= BASE_URL ?>pages/vendas" target="_self"><i
                class="bi bi-cart"></i> Vendas</a>
        <a class="link-menu <?php echo link_ativo('produtos'); ?>" href="<?= BASE_URL ?>pages/produtos"
            target="_self"><i class="bi bi-box-seam"></i> Produtos</a>
        <a class="link-menu <?php echo link_ativo('categorias'); ?>" href="<?= BASE_URL ?>pages/categorias"
            target="_self"><i class="bi bi-tags"></i> Categorias</a>
        <a class="link-menu <?php echo link_ativo('historico'); ?>" href="<?= BASE_URL ?>pages/historico"
            target="_self"><i class="bi bi-clock-history"></i> Histórico de Vendas</a>
        <?php endif ?>
    </nav>
</aside>
<?php endif ?>