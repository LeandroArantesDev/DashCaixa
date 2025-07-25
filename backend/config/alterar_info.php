<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
include("../auth/funcoes.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = strip_tags(trim($_POST["nome"]));
    $email = strip_tags(trim($_POST["email"]));
    $csrf = trim(strip_tags($_POST["csrf"]));

    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Erro de segurança (token inválido). Tente novamente.";
        header("Location: ../../pages/config");
        exit;
    }

    if (empty($nome) || empty($email)) {
        $_SESSION['resposta'] = "O nome e o email não podem estar vazios.";
        header("Location: ../../pages/config");
        exit;
    }

    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "O formato do email é inválido.";
        header("Location: ../../pages/config");
        exit;
    }

    try {
        $id_usuario = $_SESSION["id"];

        $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $email, $id_usuario);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['resposta'] = "Informações alteradas com sucesso!";
            } else {
                $_SESSION['resposta'] = "Nenhuma informação foi alterada."; // O usuário enviou os mesmos dados
            }
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao tentar alterar as informações.";
        }
    } catch (Exception $erro) {
        // Em caso de erro com o banco de dados, registra o erro e informa o usuário
        registrarErro(
            $_SESSION["id"],
            pegarRotaUsuario(),
            "Erro ao alterar informações do usuário",
            $erro->getCode(),
            pegarIpUsuario(),
            pegarNavegadorUsuario()
        );
        $_SESSION['resposta'] = "Ocorreu um erro no sistema. Tente novamente mais tarde. (Cód: " . $erro->getCode() . ")";
    }

    $stmt->close();
    header("Location: ../../pages/config");
    exit;

} else {
    $_SESSION['resposta'] = "Método de solicitação inválido.";
    header("Location: ../../pages/config");
    exit;
}