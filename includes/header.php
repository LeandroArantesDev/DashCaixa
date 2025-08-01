<?php if (isset($_SESSION['id'])): ?>
<header class="flex justify-between px-5 h-15 border-b border-gray-300/80">
    <div class="flex items-center justify-center gap-2">
        <img class="h-10 rounded-xl" src="<?= BASE_URL . 'assets/img/logo_fundo.png' ?>" alt="Logo do Dash Caixa">
        <h2 class="font-semibold text-xl">Sistema Dash Caixa</h2>
    </div>
    <div class="flex gap-3 items-center justify-center group">
        <div class="txt-user">
            <div class="text-sm font-semibold text-right"><?= htmlspecialchars($_SESSION['nome']) ?></div>
            <div class="text-xs text-right font-normal text-black/50">
                <?= htmlspecialchars(($_SESSION['tipo'] == 1) ? 'Administrador' : 'Caixa') ?></div>
        </div>
        <i class="bi bi-person flex items-center justify-center text-2xl w-8 h-8 bg-sky-500 text-white rounded-full">
        </i>
        <div id="user-opcoes"
            class="fixed right-5 top-13 bg-white flex flex-col shadow-lg border border-gray-300/80 rounded-xl invisible opacity-0  z-800 group-hover:visible group-hover:opacity-100">
            <a class="link-user border-b border-gray-300/80" href="<?= BASE_URL ?>pages/config">
                <i class="bi bi-gear"></i>
                Meu Perfil
            </a>
            <a class="link-user text-red-500" href="<?= BASE_URL ?>backend/auth/logout.php">
                <i class="bi bi-arrow-bar-left"></i> Sair
            </a>
        </div>
    </div>
</header>
<?php endif ?>