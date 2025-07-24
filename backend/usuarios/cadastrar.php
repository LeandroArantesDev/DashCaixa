<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
    try {
        $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, tipo) VALUE (?,?,?)");
        $stmt->bind_param("sss", $nome, $email, $tipo);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Usuario cadastrado com sucesso!";
            header("Location: ../../pages/usuarios");
            $stmt->close();
            exit;
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro!";
            header("Location: ../../pages/usuarios");
            $stmt->close();
            exit;
        }
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao cadastrar usuario!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        switch ($erro->getCode()) {
            default:
                $_SESSION['resposta'] = "error" . $erro->getCode();
                header("Location: ../../pages/usuarios");
                exit;
        }
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
}

header("Location: ../../pages/usuarios");
$stmt = null;
exit;
