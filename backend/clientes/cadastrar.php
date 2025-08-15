<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
include("../auth/funcoes.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = strip_tags(trim($_POST["nome"]));
    $email = strip_tags(trim($_POST["email"]));
    $documento = strip_tags(trim($_POST["documento"]));
    $telefone = strip_tags(trim($_POST["telefone"]));
    $mensalidade = strip_tags(trim($_POST["mensalidade"]));
    $senha = strip_tags(trim($_POST["senha"]));
    $confirmarsenha = strip_tags(trim($_POST["confirmarsenha"]));
    $cliente_id = strip_tags(trim($_SESSION["cliente_id"]));

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../adm/clientes");
        exit;
    }

    // Verificar o email
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Email inválido!";
        header("Location: ../../adm/clientes");
        exit;
    }

    //Validadar senha
    if (validarSenha($senha) == false) {
        $_SESSION['resposta'] = "Pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial";
        header("Location: ../../adm/clientes");
        exit;
    }

    // Verificar se tudo chegou corretamente
    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($confirmarsenha)) {

        // Verificar se as senhas são iguais e criptografa-la
        if ($senha === $confirmarsenha) {
            $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
        } else {
            $_SESSION['resposta'] = "As senhas não estão iguais!";
            header("Location: ../../adm/clientes");
            exit;
        }

        try {
            // Iniciar transação
            $conexao->autocommit(false);

            // Cadastrar cliente primeiro
            $stmt_cliente = $conexao->prepare("INSERT INTO clientes (nome, documento, telefone, email, status_mensalidade, status) VALUES (?,?,?,?,?,0)");
            $stmt_cliente->bind_param("ssssi", $nome, $documento, $telefone, $email, $mensalidade);

            if (!$stmt_cliente->execute()) {
                throw new Exception("Erro ao cadastrar cliente!");
            }

            // PEGAR O ID DO CLIENTE RECÉM CADASTRADO
            $cliente_id_novo = $conexao->insert_id;
            $stmt_cliente->close();

            // Cadastrar usuário usando o ID do cliente
            $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, tipo, senha, cliente_id) VALUES (?,?,?,?,?)");
            $tipo_usuario = 1;
            $stmt->bind_param("ssisi", $nome, $email, $tipo_usuario, $senha_hash, $cliente_id_novo);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao cadastrar usuário!");
            }

            // PEGAR O ID DO USUÁRIO RECÉM CADASTRADO
            $usuario_id_novo = $conexao->insert_id;
            $stmt->close();

            // Confirmar todas as operações
            $conexao->commit();

            $_SESSION['resposta'] = "Cadastro realizado com sucesso!";
        } catch (Exception $erro) {
            // Desfazer operações em caso de erro
            $conexao->rollback();

            registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Erro ao cadastrar cliente novo" . $erro->getMessage(), $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "Erro ao realizar cadastro!";
        } finally {
            // Restaurar autocommit
            $conexao->autocommit(true);
        }

        header("Location: ../../adm/clientes");
        exit;
    } else {
        $_SESSION['resposta'] = "Parâmetros inválidos";
        header("Location: ../../adm/clientes");
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../adm/clientes");
    exit;
}
