<?php
// backend/categorias/deletar.php

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
        if (isset($_POST['status']) && $_POST['status'] == 1) {
            $stmt = $conexao->prepare("UPDATE categorias SET status = 0 WHERE id = ?");
        } else {
            $stmt = $conexao->prepare("UPDATE categorias SET status = 1 WHERE id = ?");
        }

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Categoria atualizado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar categoria!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        if ($erro->getCode() == 1451) { // erro de restrição de chave estrangeira
            registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro: Esta categoria não pode ser atualizada pois está associada a outros registros.", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "Erro: Esta categoria não pode ser atualizada pois está associada a outros registros.";
        } else {
            registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao atualizar categoria!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
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
