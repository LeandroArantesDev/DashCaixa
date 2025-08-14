<?php

$hostAtual = $_SERVER['HTTP_HOST'];

if ($hostAtual == 'localhost' || str_contains($hostAtual, 'ngrok-free.app')) {
    // Ambiente local ou ngrok
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'dash_caixa';
} else {
    // Ambiente de produção
    $host = 'localhost';
    $username = 'u262084135_admin';
    $password = ':Y6pwzR^7u';
    $dbname = 'u262084135_dash_caixa';
}


$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Erro na conexão!" . $conexao->connect_error);
}