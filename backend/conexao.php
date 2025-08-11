<?php

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'dashcaixa';
} else {
    $host = 'sql313.infinityfree.com';
    $username = 'if0_39683322';
    $password = 'UikYkRLYHWEjOp';
    $dbname = 'if0_39683322_dashcaixa';
}

$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Erro na conexão!" . $conexao->connect_error);
}