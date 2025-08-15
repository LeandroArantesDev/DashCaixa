<?php
session_start();

// redireciona o usuario caso ele esteja logado
if (isset($_SESSION['id']) && $_SESSION['id'] == 1) {
    header("Location: pages/dashboard");
} else if (isset($_SESSION['id']) && $_SESSION['id'] != 1) {
    header("Location: pages/vendas");
}

$titulo = "Login";
$n_valida = true;
$main_formulario = true;
include("includes/inicio.php");
?>
    <div class="px-[4%] lg:px-0 flex items-center justify-center w-full h-full bg-sky-100">
        <form class="flex items-center space-y-4 justify-center flex-col rounded-xl p-7 bg-fundo-interface shadow-xl w-full lg:w-auto lg:min-w-lg"
              action="backend/auth/login.php" method="POST">
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
            <img src="assets/img/logo.png" alt="Logo DashCaixa" class="h-25">
            <h1 class="text-4xl font-bold">Dash Caixa</h1>
            <div class="input-group">
                <label for="email">Email</label>
                <div class="relative">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email" placeholder="Seu email"
                           class="w-full pl-10 pr-4 py-3 border border-borda rounded-lg focus:ring-2 focus:ring-principal focus:border-transparent transition-colors"
                           required>
                </div>
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <div class="relative">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="senha" id="senha" placeholder="Sua senha"
                           class="w-full pl-10 pr-4 py-3 border border-borda rounded-lg focus:ring-2 focus:ring-principal focus:border-transparent transition-colors"
                           required>
                    <button type="button" class="input-icon-eye" id="toggleSenha">
                        <i class="bi bi-eye" id="iconSenha"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="button-entrar mt-4">Entrar</button>
            <a class="text-principal hover:text-principal-hover transition-colors" href="#">Esqueceu sua senha?</a>
        </form>
    </div>
    <script>
        document.getElementById('toggleSenha').addEventListener('click', function () {
            const input = document.getElementById('senha');
            const icon = document.getElementById('iconSenha');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
<?php include("includes/fim.php") ?>