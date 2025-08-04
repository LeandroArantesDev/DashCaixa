<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = strip_tags(trim($_POST["id"]));

    //validação do csrf
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../adm/roadmap");
        exit;
    }

    try {
        $stmt = $conexao->prepare("DELETE FROM roadmap WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Item deletado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao editar item!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Ocorreu um erro ao editar item!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../adm/roadmap");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../adm/roadmap");
    exit;
}
