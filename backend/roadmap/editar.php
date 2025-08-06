<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = strip_tags(trim($_POST["id"]));
    $titulo = strip_tags(trim($_POST["titulo"]));
    $descricao = strip_tags(trim($_POST["descricao"]));
    $status = strip_tags(trim($_POST["status"]));
    $criado_em = $_POST["criado_em"];
    $concluido_em = $_POST["concluido_em"];

    if (empty($criado_em)) {
        $criado_em = null;
    }

    if (empty($concluido_em)) {
        $concluido_em = null;
    }

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../adm/roadmap");
        exit;
    }

    try {
        $stmt = $conexao->prepare("UPDATE roadmap SET titulo = ?, descricao = ?, status = ?, criado_em = ?, concluido_em = ? WHERE id = ?");
        $stmt->bind_param("ssissi", $titulo, $descricao, $status, $criado_em, $concluido_em, $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Item atualizado com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar item!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Ocorreu um erro ao atualizar item!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../adm/roadmap");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../adm/roadmap");
    exit;
}
