<?php
session_start();
include("funcoes/geral.php");
echo ($_SESSION["csrf"] . " | ");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="produtos/cadastrar.php" method="post">
        <input type="text" name="csrf" value="<?= gerarCSRF() ?>">
        <input type="text" name="nome" id="nome" placeholder="nome">
        <input type="text" name="preco" id="preco" placeholder="preço">
        <input type="text" name="estoque" id="estoque" placeholder="estoque">
        <input type="text" name="categoria_id" id="categoria_id" placeholder="categoria id">
        <button type="submit">Enviar</button>
    </form>
</body>

</html>