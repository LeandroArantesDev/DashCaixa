<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = strip_tags(trim($_POST["titulo"]));
    $descricao = strip_tags(trim($_POST["descricao"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../adm/roadmap");
        exit;
    }

    try {
        $stmt = $conexao->prepare("INSERT INTO roadmap (titulo, descricao, status) VALUE (?,?,0)");
        $stmt->bind_param("ss", $titulo, $descricao);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Item cadastrado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao cadastrar item!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Ocorreu um erro ao cadastrar item!", 1, pegarIpUsuario(), pegarNavegadorUsuario());
        switch ($erro->getCode()) {
            default:
                $_SESSION['resposta'] = "error" . $erro->getCode();
        }
    }
    header("Location: ../../adm/roadmap");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
}

header("Location: ../../adm/roadmap");
$stmt = null;
exit;
