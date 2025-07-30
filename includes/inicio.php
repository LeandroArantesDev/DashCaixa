<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/DashCaixa/');
    } else {
        define('BASE_URL', '/');
    }
}
date_default_timezone_set('America/Sao_Paulo');

if (!isset($n_valida) || $n_valida == false) {
    include("valida.php");
}

include(__DIR__ . "/../backend/funcoes/geral.php");
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
            // script do submenu
            function toggleSubmenu() {
            const submenu = document.getElementById('mensalidade-submenu');
            submenu.classList.toggle('hidden');
        }

            // Fecha o submenu ao clicar fora
            document.addEventListener('click', function(event) {
            const btn = document.getElementById('mensalidade-btn');
            const submenu = document.getElementById('mensalidade-submenu');

            if (!btn.contains(event.target) && !submenu.contains(event.target)) {
            submenu.classList.add('hidden');
        }
        });
    </script>
</head>

<body>
    <?php include("header.php") ?>
    <main <?= ((isset($form_index) && $form_index == true) ? "class='main-full-height'" : '') ?>>
        <?php include("menu.php") ?>