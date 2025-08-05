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

    $dataAtual = date('Y-m-d'); // Formato DATE (ex: 2025-08-05)

    try {
        if ($status == 0) {
            $status = $status + 1;
            $stmt = $conexao->prepare("UPDATE roadmap SET status = ?, criado_em = ? WHERE id = ?");
            $stmt->bind_param("isi", $status, $dataAtual, $id);
        } else {
            $status = $status + 1;
            $stmt = $conexao->prepare("UPDATE roadmap SET status = ?, concluido_em = ? WHERE id = ?");
            $stmt->bind_param("isi", $status, $dataAtual, $id);
        }

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Funcionalidade avançada com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao avançar funcionalidade!";
        }
        $stmt->close();
    } catch (Exception $erro) {
        registrarErro($_SESSION["id"], pegarRotaUsuario(), "Ocorreu um erro ao avançar funcionalidade!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = "error" . $erro->getCode();
    }

    header("Location: ../../adm/roadmap");;
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../adm/roadmap");
    exit;
}
