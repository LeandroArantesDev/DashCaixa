<?php
// backend/produtos/deletar.php

session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST['id'] ?? 0);

    //validação do csrf
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/produtos");
        exit;
    }

    try {
        $stmt = $conexao->prepare("UPDATE produtos SET status = 2 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Produto deletado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao deletar o produto!";
        }
        $stmt->close();

    } catch (Exception $erro) {
        if ($erro->getCode() == 1451) { // erro de restrição de chave estrangeira
            $_SESSION['resposta'] = "Erro: Este produto não pode ser deletado pois está associado a outros registros (ex: vendas).";
        } else {
            registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao deletar produto!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "error" . $erro->getCode();
        }
    }

    header("Location: ../../pages/produtos");
    exit;

} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/produtos");
    exit;
}