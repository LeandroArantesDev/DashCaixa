<?php
session_start();

if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/DashCaixa/');
    } else {
        define('BASE_URL', '/');
    }
}

if ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] == 0) {
    header("Location: " . BASE_URL . "pages/vendas");
    exit;
}
