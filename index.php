<?php
session_start();

// redireciona o usuario caso ele esteja logado
if (isset($_SESSION['id']) && $_SESSION['id'] == 1) {
    header("Location: pages/dashboard");
} else if (isset($_SESSION['id']) && $_SESSION['id'] != 1) {
    header("Location: pages/vendas");
}

$titulo = "Login";
$css = "formulario";
$n_valida = true;
include("backend/auth/funcoes.php");
include("includes/inicio.php");
?>
<div class="flex items-center justify-center w-full h-full bg-sky-100">
    <form class="flex items-center justify-center gap-4 flex-col rounded-xl p-7 bg-white shadow-xl min-w-lg"
        action="backend/auth/login.php" method="POST">
        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
        <img src="assets/img/logo.png" alt="Logo DashCaixa" class="h-25">
        <h1 class="text-4xl font-bold">Dash Caixa</h1>
        <div class="input-group">
            <label for="email">Email</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Seu email" required>
            </div>
        </div>
        <div class="input-group">
            <label for="senha">Senha</label>
            <div class="input-icon">
                <i class="bi bi-lock"></i>
                <input type="password" name="senha" id="senha" placeholder="Sua senha" required>
            </div>
        </div>
        <button type="submit" class="bg-sky-500 text-white w-full p-2 rounded-lg mt-2">Entrar</button>
        <a class="text-sky-500" href="#">Esqueceu sua senha?</a>
    </form>
</div>
<?php include("includes/fim.php") ?>