<?php
// backend/produtos/editar.php

session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST['id'] ?? 0);

    // Recebe os outros dados do formulário
    $nome = strip_tags(trim($_POST["nome"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/categorias");
        exit;
    }

    // Validação para garantir que o ID é válidos
    if ($id === 0) {
        $_SESSION['resposta'] = "Erro: Dados inválidos fornecidos.";
        header("Location: ../../pages/categorias");
        exit;
    }

    try {
        $stmt = $conexao->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Categoria atualizada com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar a categoria!";
        }
        $stmt->close();

    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao editar categoria!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../pages/categorias");
    exit;

} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/categorias");
    exit;
}