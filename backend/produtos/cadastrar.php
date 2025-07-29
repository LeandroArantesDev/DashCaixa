<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = strip_tags(trim($_POST["nome"]));
    $preco = strip_tags(trim($_POST["preco"]));
    $estoque = strip_tags(trim($_POST["estoque"]));
    $categoria_id = strip_tags(trim($_POST["categoria_id"]));
    $cliente_id = strip_tags(trim($_SESSION["cliente_id"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/produtos");
        exit;
    }
    try {
        $stmt = $conexao->prepare("INSERT INTO produtos (nome, preco, estoque, categoria_id, cliente_id) VALUE (?,?,?,?,?)");
        $stmt->bind_param("sssii", $nome, $preco, $estoque, $categoria_id, $cliente_id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Produto cadastrado com sucesso!";
            header("Location: ../../pages/produtos");
            $stmt->close();
            exit;
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro!";
            header("Location: ../../pages/produtos");
            $stmt->close();
            exit;
        }
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao cadastrar produto!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        switch ($erro->getCode()) {
            default:
                $_SESSION['resposta'] = "error" . $erro->getCode();
                header("Location: ../../pages/produtos");
                exit;
        }
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
}

header("Location: ../../pages/produtos");
$stmt = null;
exit;
