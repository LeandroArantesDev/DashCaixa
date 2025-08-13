<?php

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '076f731191cb.ngrok-free.app') {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'dash_caixa';
} else {
    $host = 'localhost';
    $username = 'u262084135_admin';
    $password = ':Y6pwzR^7u';
    $dbname = 'u262084135_dash_caixa';
}

$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Erro na conexão!" . $conexao->connect_error);
}