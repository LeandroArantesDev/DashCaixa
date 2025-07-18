<?php
$senha = $_GET['senha'];

$resultado = password_hash($senha, PASSWORD_DEFAULT);
echo $resultado;
