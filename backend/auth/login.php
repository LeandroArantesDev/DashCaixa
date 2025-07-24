<?php
session_start();
include("funcoes.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(strip_tags($_POST['email']));
    $senha = trim(strip_tags($_POST["senha"]));

    // Verificar o email
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Email inválido!";
        header("Location: " . BASE_URL);
        exit;
    }

    //Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Método invalido!";
        header("Location: " . BASE_URL);
        exit;
    }

    //Validadar senha
    if (validarSenha($senha) == false) {
        $_SESSION['resposta'] = "Senha incorreta!";
        header("Location: " . BASE_URL);
        exit;
    }

    if (!empty($email) && !empty($senha)) {
        try {
            // Faz a verificação no banco de dados
            $stmt = $conexao->prepare("SELECT id, nome, email, senha, tipo, status FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($id, $nome, $email, $senha_db, $tipo, $status);
            $stmt->fetch();
            $stmt->close();

            // Verifica se usuário está ativo no sistema
            if ($status == 0) {
                // verifica que o email e senha existe e batem no banco de dados ele loga o usuário;
                if (!empty($nome) && !empty($senha) && password_verify($senha, $senha_db)) {
                    // adicionar o ultimo acesso do usuario
                    $stmt = $conexao->prepare("UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->close();

                    // atualiza as variaveis sessions
                    $_SESSION["id"] = $id;
                    $_SESSION["nome"] = $nome;
                    $_SESSION["email"] = $email;
                    $_SESSION["tipo"] = $tipo;

                    $_SESSION['resposta'] = "Bem Vindo! " . $_SESSION['nome'];

                    if ($tipo == 1) {
                        header("Location: " . BASE_URL . "pages/dashboard");
                        exit;
                    } else {
                        header("Location: " . BASE_URL . "pages/vendas");
                        exit;
                    }
                } else {
                    $_SESSION['resposta'] = "E-mail ou senha incorretos!";
                    header("Location: " . BASE_URL);
                    exit;
                }
            } else {
                $_SESSION['resposta'] = "Acesso negado!";
                header("Location: " . BASE_URL);
                exit;
            }
        } catch (Exception $erro) {
            registrarErro(null, pegarRotaUsuario(), "Erro na hora do login", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            // Caso houver erro ele retorna
            switch ($erro->getCode()) {
                // erro de quantidade de paramêtros erro
                case 1136:
                    $_SESSION['resposta'] = "Quantidade de dados inseridos inválida!";
                    header("Location: " . BASE_URL);
                    exit;
                default:
                    $_SESSION['resposta'] = "error" . $erro->getCode();
                    header("Location: " . BASE_URL);
                    exit;
            }
        }
    } else {
        $_SESSION['resposta'] = "Preencha todas as informações!";
    }
} else {
    $_SESSION['resposta'] = "Variável POST ínvalida!";
}
header("Location: " . BASE_URL);
exit;
