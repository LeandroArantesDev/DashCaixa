<?php
// backend/produtos/editar.php

session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST['id'] ?? 0);

    // Recebe os outros dados do formulário
    $nome = strip_tags(trim($_POST["nome"]));
    $email = strip_tags(trim($_POST["email"]));
    $tipo = strip_tags(trim($_POST["tipo"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/usuarios");
        exit;
    }

    // Validação básica para garantir que o ID e a categoria são válidos
    if ($id === 0) {
        $_SESSION['resposta'] = "Erro: Dados inválidos fornecidos.";
        header("Location: ../../pages/usuarios");
        exit;
    }

    try {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $email, $tipo, $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Usuario atualizado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar o Usuario!";
        }
        $stmt->close();

    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao editar usuario!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../pages/usuarios");
    exit;

} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/usuarios");
    exit;
}