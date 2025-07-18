<?php
include(__DIR__ . '/../backend/conexao.php');

//Verifica se existe uma sessão ativa e se não houver inicia uma
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION["id"]) and !isset($_SESSION["nome"]) and !isset($_SESSION["email"])) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL);
    exit();
} else {
    $email = $_SESSION["email"];
    $stmt = $conexao->prepare("SELECT nome, email, tipo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $stmt->bind_result($nome, $email, $tipo);
        $stmt->fetch();
        $stmt->close();

        if ((($nome === null) || ($email === null) || ($tipo === null))) {
            session_unset();
            session_destroy();
            header("Location: " . BASE_URL);
            exit();
        } else {
            $_SESSION["nome"] = $nome;
            $_SESSION["email"] = $email;
            $_SESSION["tipo"] = $tipo;
        }
    }
}
