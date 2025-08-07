<?php
// função do menu
function link_ativo(string $pagina): string
{
   if (strpos($_SERVER['REQUEST_URI'], 'pages/' . $pagina) !== false || strpos($_SERVER['REQUEST_URI'], 'adm/' . $pagina) !== false) {
      return 'atual';
   }
   return '';
}

if (isset($_SESSION['id'])):
?>
   <aside class="min-w-70 border-r border-borda/80 p-4 bg-fundo-interface">
      <nav class="flex flex-col gap-4">
         <?php if ($_SESSION['tipo'] == 1): ?>

            <!-- caso o usuario for adm -->

            <?php if ($_SESSION['mensalidade'] == 0 || $_SESSION['mensalidade'] == 1): ?>

               <!-- caso ele esteja com a mensalidade paga ou pendente -->

               <a class="link-menu <?php echo link_ativo('dashboard'); ?>" href="<?= BASE_URL ?>pages/dashboard"
                  target="_self"><i class="bi bi-columns-gap"></i> Dashboard</a>
               <a class="link-menu <?php echo link_ativo('vendas'); ?>" href="<?= BASE_URL ?>pages/vendas"
                  target="_self"><i
                     class="bi bi-cart"></i> Vendas</a>
               <a class="link-menu <?php echo link_ativo('produtos'); ?>" href="<?= BASE_URL ?>pages/produtos"
                  target="_self"><i class="bi bi-box-seam"></i> Produtos</a>
               <a class="link-menu <?php echo link_ativo('categorias'); ?>" href="<?= BASE_URL ?>pages/categorias"
                  target="_self"><i class="bi bi-tags"></i> Categorias</a>
               <a class="link-menu <?php echo link_ativo('usuarios'); ?>" href="<?= BASE_URL ?>pages/usuarios"
                  target="_self"><i class="bi bi-people"></i> Usuarios</a>
               <a class="link-menu <?php echo link_ativo('historico'); ?>" href="<?= BASE_URL ?>pages/historico"
                  target="_self"><i class="bi bi-clock-history"></i> Histórico de Vendas</a>
               <a class="link-menu <?php echo link_ativo('roadmap'); ?>" href="<?= BASE_URL ?>pages/roadmap"
                  target="_self"><i class="bi bi-signpost"></i> Roadmap</a>
               <div class="relative inline-block">
                  <!-- Botão principal que abre o submenu -->
                  <button onclick="toggleSubmenu()"
                     id="mensalidade-btn"
                     class="link-menu justify-between w-full cursor-pointer <?php echo (link_ativo('mensalidade') || link_ativo('historico-mensalidades')) ? 'atual' : ''; ?>">
                     <p class="flex gap-4 items-center">
                        <i class="bi bi-wallet"></i>
                        <span>Mensalidade</span>
                     </p>
                     <i id="mensalidade-seta" class="bi bi-chevron-right transition-transform duration-300"></i>
                  </button>

                  <!-- Submenu -->
                  <div id="mensalidade-submenu"
                     class="hidden absolute left-0 w-full rounded-md z-50">
                     <a class="link-submenu"
                        href="<?= BASE_URL ?>pages/mensalidade"
                        target="_self">
                        <i class="bi bi-cash-stack"></i> Status
                     </a>
                     <a class="link-submenu"
                        href="#"
                        target="_self">
                        <i class="bi bi-list-nested"></i> Histórico
                     </a>
                  </div>

               </div>
            <?php else: ?>

               <!-- caso ele esteja com a mensalidade vencida -->

               <a class="link-menu <?php echo link_ativo('mensalidade'); ?>"
                  href="<?= BASE_URL ?>pages/mensalidade"
                  target="_self">
                  <i class="bi bi-wallet"></i> Mensalidade
               </a>
            <?php endif ?>
         <?php elseif ($_SESSION['tipo'] == 0): ?>

            <!-- caso o usuario seja caixa -->

            <a class="link-menu <?php echo link_ativo('vendas'); ?>" href="<?= BASE_URL ?>pages/vendas"
               target="_self"><i
                  class="bi bi-cart"></i> Vendas</a>
            <a class="link-menu <?php echo link_ativo('produtos'); ?>" href="<?= BASE_URL ?>pages/produtos"
               target="_self"><i class="bi bi-box-seam"></i> Produtos</a>
            <a class="link-menu <?php echo link_ativo('categorias'); ?>" href="<?= BASE_URL ?>pages/categorias"
               target="_self"><i class="bi bi-tags"></i> Categorias</a>
            <a class="link-menu <?php echo link_ativo('historico'); ?>" href="<?= BASE_URL ?>pages/historico"
               target="_self"><i class="bi bi-clock-history"></i> Histórico de Vendas</a>
         <?php elseif ($_SESSION['tipo'] == 2): ?>

            <!-- caso o usuario seja fundador -->

            <a class="link-menu <?php echo link_ativo('dashboard'); ?>" href="<?= BASE_URL ?>adm/dashboard"
               target="_self"><i class="bi bi-columns-gap"></i> Dashboard</a>
            <a class="link-menu <?php echo link_ativo('clientes'); ?>" href="<?= BASE_URL ?>adm/clientes"
               target="_self"><i class="bi bi-people"></i> Clientes</a>
            <a class="link-menu <?php echo link_ativo('erros'); ?>" href="<?= BASE_URL ?>adm/erros"
               target="_self"><i class="bi bi-exclamation-diamond"></i> Erros</a>
            <a class="link-menu <?php echo link_ativo('roadmap'); ?>" href="<?= BASE_URL ?>adm/roadmap"
               target="_self"><i class="bi bi-signpost"></i> Roadmap</a>
         <?php endif ?>
      </nav>
   </aside>
<?php endif ?>