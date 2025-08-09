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
        header("Location: ../../pages/categorias");
        exit;
    }

    try {
        $stmt = $conexao->prepare("UPDATE categorias SET status = 2 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Categoria deletada com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao deletar a categoria!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        if ($erro->getCode() == 1451) { // erro de restrição de chave estrangeira
            $_SESSION['resposta'] = "Erro: Esta categoria não pode ser deletada pois está associada a outros registros (ex: produtos).";
        } else {
            registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Erro ao deletar categoria!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "error" . $erro->getCode();
        }
    }

    header("Location: ../../pages/categorias");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/categorias");
    exit;
}
