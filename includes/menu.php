<?php if (isset($_SESSION['id'])): ?>
    <aside>
        <nav>
            <?php
            if ($_SESSION['tipo'] == 1):
            ?>
                <a href="<?= BASE_URL ?>pages/dashboard" target="_self">Dashboard</a>
                <a href="<?= BASE_URL ?>pages/vendas" target="_self">Vendas</a>
                <a href="<?= BASE_URL ?>pages/produtos" target="_self">Produtos</a>
                <a href="<?= BASE_URL ?>pages/categorias" target="_self">Categorias</a>
                <a href="<?= BASE_URL ?>pages/usuarios" target="_self">Usuarios</a>
                <a href="<?= BASE_URL ?>pages/historico" target="_self">Histórico de Vendas</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>pages/vendas" target="_self">Vendas</a>
                <a href="<?= BASE_URL ?>pages/produtos" target="_self">Produtos</a>
                <a href="<?= BASE_URL ?>pages/categorias" target="_self">Categorias</a>
                <a href="<?= BASE_URL ?>pages/historico" target="_self">Histórico de Vendas</a>
            <?php endif ?>
        </nav>
    </aside>
<?php endif ?>