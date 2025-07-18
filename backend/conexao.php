<?php

$conexao = mysqli_connect("localhost", "root", "", "dash_caixa");

if ($conexao->connect_error) {
    die("Erro na conexão!" . $conexao->connect_error);
}