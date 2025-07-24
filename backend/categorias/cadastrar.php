<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = strip_tags(trim($_POST["nome"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/categorias");
        exit;
    }
    try {
        $stmt = $conexao->prepare("INSERT INTO categorias (nome) VALUE (?)");
        $stmt->bind_param("s", $nome);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Categoria cadastrada com sucesso!";
            header("Location: ../../pages/categorias");
            $stmt->close();
            exit;
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro!";
            header("Location: ../../pages/categorias");
            $stmt->close();
            exit;
        }
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao cadastrar categoria!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        switch ($erro->getCode()) {
            default:
                $_SESSION['resposta'] = "error" . $erro->getCode();
                header("Location: ../../pages/categorias");
                exit;
        }
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
}

header("Location: ../../pages/categorias");
$stmt = null;
exit;
