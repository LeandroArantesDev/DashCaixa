<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
include("../auth/funcoes.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = strip_tags(trim($_POST["nome"]));
    $email = strip_tags(trim($_POST["email"]));
    $tipo = strip_tags(trim($_POST["tipo"]));
    $senha = strip_tags(trim($_POST["senha"]));
    $confirmarsenha = strip_tags(trim($_POST["confirmarsenha"]));
    $cliente_id = strip_tags(trim($_SESSION["cliente_id"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
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
            $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, tipo, senha, cliente_id) VALUE (?,?,?,?,?)");
            $stmt->bind_param("ssisi", $nome, $email, $tipo, $senha_hash, $cliente_id);

            if ($stmt->execute()) {
                $_SESSION['resposta'] = "Usuario cadastrado com sucesso!";
                header("Location: ../../pages/usuarios");
            } else {
                $_SESSION['resposta'] = "Ocorreu um erro!";
                header("Location: ../../pages/usuarios");
            }
        } catch (Exception $erro) {
            registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao cadastrar usuario!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
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
