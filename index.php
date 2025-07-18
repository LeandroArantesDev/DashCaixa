<?php
$titulo = "Login";
$css = "formulario";
include("includes/inicio.php")
?>
<div class="flex items-center justify-center w-full h-dvh bg-sky-100">
    <form class="flex items-center justify-center gap-3 flex-col rounded-xl p-5 bg-white shadow-xl min-w-lg"
        action="backend/auth/login.php" method="POST">
        <img src="assets/img/logo.png" alt="Logo DashCaixa" class="h-25">
        <h1 class="text-4xl font-bold">Login</h1>
        <p class="text-center">Sistema de Gestão de Vendas</p>
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
        <button type="submit" class="bg-sky-500 text-white w-full p-2 rounded-lg">Entrar</button>
        <a class="text-sky-500" href="#">Esqueceu sua senha?</a>
    </form>
</div>
<?php include("includes/fim.php") ?>