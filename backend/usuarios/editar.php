<?php
// backend/usuarios/editar.php

session_start();
include("../conexao.php");
include("../funcoes/geral.php");
include("../auth/funcoes.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST['id'] ?? 0);

    // Recebe os outros dados do formulário
    $nome = strip_tags(trim($_POST["nome"]));
    $email = strip_tags(trim($_POST["email"]));
    $tipo = strip_tags(trim($_POST["tipo"]));
    $senha = strip_tags(trim($_POST["senha"]));
    $confirmarsenha = strip_tags(trim($_POST["confirmarsenha"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/usuarios");
        exit;
    }

    // Validação básica para garantir que o ID e a categoria são válidos
    if ($id === 0) {
        $_SESSION['resposta'] = "Erro: Dados inválidos fornecidos.";
        header("Location: ../../pages/usuarios");
        exit;
    }

    // Verificar o email
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Email inválido!";
        header("Location: ../../pages/usuarios");
        exit;
    }

    //Validadar senha
    if (validarSenha($senha) == false) {
        $_SESSION['resposta'] = "Pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial";
        header("Location: ../../pages/usuarios");
        exit;
    }

    // Verificar se tudo chegou corretamente
    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($confirmarsenha)) {

        // Verificar se as senhas são iguais e criptografa-la
        if ($senha === $confirmarsenha) {
            $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
        } else {
            $_SESSION['resposta'] = "As senhas não estão iguais!";
            header("Location: ../../pages/usuarios");
            exit;
        }

        try {
            $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nome, $email, $tipo, $senha_hash, $id);

            if ($stmt->execute()) {
                $_SESSION['resposta'] = "Usuario atualizado com sucesso!";
            } else {
                $_SESSION['resposta'] = "Ocorreu um erro ao atualizar o Usuario!";
            }
            $stmt->close();
        } catch (Exception $erro) {
            registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao editar usuario!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "error" . $erro->getCode();
        }

        header("Location: ../../pages/usuarios");
        exit;
    } else {
        $_SESSION['resposta'] = "Parâmetros inválidos";
        header("Location: ../../pages/usuarios");
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/usuarios");
    exit;
}
