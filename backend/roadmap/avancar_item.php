<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = strip_tags(trim($_POST["id"]));
    $status = strip_tags(trim($_POST["status"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../adm/roadmap");
        exit;
    }

    $status = $status + 1;
    if ($status > 2) {;
        $status = 0;
    }

    try {
        $stmt = $conexao->prepare("UPDATE roadmap SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Item avançado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao avançar item!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Ocorreu um erro ao avançar item!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../adm/roadmap");;
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../adm/roadmap");
    exit;
}
