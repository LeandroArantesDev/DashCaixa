<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
include("../auth/funcoes.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $senha_atual = trim($_POST["senha-atual"]);
    $nova_senha = trim($_POST["nova-senha"]);
    $confirmar_senha = trim($_POST["confirmar-senha"]);
    $csrf = trim(strip_tags($_POST["csrf"]));

    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Erro de segurança (token inválido). Tente novamente.";
        header("Location: ../../pages/config");
        exit;
    }

    if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
        $_SESSION['resposta'] = "Todos os campos de senha são obrigatórios.";
        header("Location: ../../pages/config");
        exit;
    }

    // verificar se a nova senha e a confirmação são iguais
    if ($nova_senha !== $confirmar_senha) {
        $_SESSION['resposta'] = "A nova senha e a confirmação não coincidem.";
        header("Location: ../../pages/config");
        exit;
    }

    if (validarSenha($nova_senha) == false) {
        $_SESSION['resposta'] = "A nova senha deve ter pelo menos 8 caracteres, uma letra maiúscula, uma minúscula, um número e um caractere especial.";
        header("Location: ../../pages/config");
        exit;
    }

    try {
        $id_usuario = $_SESSION["id"];

        $stmt = $conexao->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $_SESSION['resposta'] = "Usuário não encontrado.";
            header("Location: ../../pages/config");
            exit;
        }

        $usuario = $resultado->fetch_assoc();
        $hash_senha_db = $usuario['senha'];
        $stmt->close();

        // verificar se a senha atual fornecida corresponde à senha no banco
        if (password_verify($senha_atual, $hash_senha_db)) {

            $nova_senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);

            $stmt_update = $conexao->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt_update->bind_param("si", $nova_senha_hash, $id_usuario);

            if ($stmt_update->execute()) {
                $_SESSION['resposta'] = "Senha alterada com sucesso!";
            } else {
                $_SESSION['resposta'] = "Ocorreu um erro ao atualizar a senha.";
            }
            $stmt_update->close();
        } else {
            // Se a senha atual estiver incorreta
            $_SESSION['resposta'] = "A senha atual está incorreta.";
        }
    } catch (Exception $erro) {
        registrarErro(
            $_SESSION["cliente_id"],
            $_SESSION["id"],
            pegarRotaUsuario(),
            "Erro ao alterar senha do usuário",
            $erro->getCode(),
            pegarIpUsuario(),
            pegarNavegadorUsuario()
        );
        // Mensagem de erro genérica para o usuário
        $_SESSION['resposta'] = "Ocorreu um erro no sistema. (Cód: " . $erro->getCode() . ")";
    }

    header("Location: ../../pages/config");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação inválido.";
    header("Location: ../../pages/config");
    exit;
}
