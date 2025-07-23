<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/DashCaixa/');
    } else {
        define('BASE_URL', '/');
    }
}

include(__DIR__ . '/../conexao.php');

function validarNome($nome)
{
    // Remove espaços no início/fim e valida com regex
    $nome = trim($nome);

    // Regex: letras (com acento), espaço, mínimo 3 e máximo 50 caracteres
    if (!preg_match('/^[\p{L} ]{3,50}$/u', $nome)) {
        return false;
    }

    return true;
}

function validarEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}


function validarSenha($senha)
{
    // Pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha)) {
        return false;
    }

    return true;
}
