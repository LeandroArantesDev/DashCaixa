<?php
$titulo = "Login";
$css = "formulario";
include("includes/inicio.php")
?>
<div class="flex items-center justify-center w-full h-dvh bg-sky-100">
    <form class="flex items-center justify-center gap-2 flex-col rounded-xl p-5 bg-white shadow-xl"
        action="backend/auth/login.php" method="POST">
        <img src="assets/img/logo.png" alt="Logo DashCaixa" class="w-45 h-45">
        <h1 class="text-4xl font-bold">Login</h1>
        <p class="text-center">Sistema de Gestão de Vendas</p>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Seu email" required>
        </div>
        <div class="input-group">
            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" placeholder="Sua senha" required>
        </div>
        <button type="submit">Entrar</button>
        <a href="#">Esqueceu sua senha?</a>
    </form>
</div>
<?php include("includes/fim.php") ?>