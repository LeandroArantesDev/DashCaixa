<?php
date_default_timezone_set('America/Sao_Paulo');
include(__DIR__ . "/../backend/funcoes/geral.php");

if (!isset($n_valida) || $n_valida == false) {
    include("valida.php");
} else {
    session_start();
    include __DIR__ . "/../backend/conexao.php";
}

?>

    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?= BASE_URL ?>assets/css/output.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <title><?= htmlspecialchars($titulo ?? "DashCaixa") ?></title>
        <script defer>
            function toggleSubmenu() {
                const submenu = document.getElementById('mensalidade-submenu');
                const seta = document.getElementById('mensalidade-seta');

                // Alternar visibilidade com animação
                submenu.classList.toggle('hidden');
                submenu.classList.toggle('fade-slide');

                // Alternar rotação da seta
                seta.classList.toggle('rotate-90');
            }

            // Fecha submenu ao clicar fora
            document.addEventListener('click', function (event) {
                const btn = document.getElementById('mensalidade-btn');
                const submenu = document.getElementById('mensalidade-submenu');
                const seta = document.getElementById('mensalidade-seta');

                if (!btn.contains(event.target) && !submenu.contains(event.target)) {
                    submenu.classList.add('hidden');
                    submenu.classList.remove('fade-slide');
                    seta.classList.remove('rotate-90');
                }
            });
        </script>
    </head>

<body>
<?php include("header.php") ?>
<main
    <?php
    // caso for uma tela com um formulario central (login, ou cadastro)
    if (isset($main_formulario) && $main_formulario == true) {
        echo "class='main-formulario'";
    } elseif (isset($main_full) && $main_full == true) { // caso for uma tela que precise apenas de 100dvh
        echo "class='main-full'";
    }
    ?>>
<?php include("menu.php") ?>