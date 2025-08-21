<?php
// backend/produtos/editar.php

session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST['id'] ?? 0);

    // Recebe os outros dados do formulário
    $nome = strip_tags(trim($_POST["nome"]));
    $preco = strip_tags(trim($_POST["preco"]));
    $estoque = strip_tags(trim($_POST["estoque"]));
    $categoria_id = (int)($_POST["categoria_id"] ?? 0);
    $img = strip_tags(trim($_POST["img"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/produtos");
        exit;
    }

    // Validação básica para garantir que o ID e a categoria são válidos
    if ($id === 0 || $categoria_id === 0) {
        $_SESSION['resposta'] = "Erro: Dados inválidos fornecidos.";
        header("Location: ../../pages/produtos");
        exit;
    }

    try {
        $stmt = $conexao->prepare("UPDATE produtos SET nome = ?, preco = ?, estoque = ?, categoria_id = ?, img = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $nome, $preco, $estoque, $categoria_id, $img, $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Produto atualizado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar o produto!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Erro ao editar produto!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../pages/produtos");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/produtos");
    exit;
}
