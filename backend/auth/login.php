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
            $stmt = $conexao->prepare("SELECT id, cliente_id, nome, email, senha, tipo, status FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($id, $cliente_id, $nome, $email, $senha_db, $tipo, $status);
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

                    // Verifica e atualiza mensalidade vencida
                    $stmt = $conexao->prepare("SELECT id, data_vencimento FROM mensalidades WHERE cliente_id = ? AND status = 1 ORDER BY data_vencimento ASC LIMIT 1");
                    $stmt->bind_param("i", $cliente_id);
                    $stmt->execute();
                    $stmt->bind_result($id_mensalidade, $data_vencimento);
                    if ($stmt->fetch()) {
                        if ($data_vencimento < date("Y-m-d")) {
                            $stmt->close();
                            $stmt = $conexao->prepare("UPDATE mensalidades SET status = 2 WHERE id = ?");
                            $stmt->bind_param("i", $id_mensalidade);
                            $stmt->execute();
                        }
                    }
                    $stmt->close();

                    // Verifica o status atual da mensalidade do cliente
                    $stmt = $conexao->prepare("SELECT status FROM mensalidades WHERE cliente_id = ? AND status IN (1,2) ORDER BY data_vencimento ASC LIMIT 1");
                    $stmt->bind_param("i", $cliente_id);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $status_mensalidade = 0;

                    if ($linha = $resultado->fetch_assoc()) {
                        $status_mensalidade = $linha['status'];
                    }

                    $stmt->close();

                    // Atualiza o status do cliente
                    $stmt = $conexao->prepare("UPDATE clientes SET status_mensalidade = ? WHERE id = ?");
                    $stmt->bind_param("ii", $status_mensalidade, $cliente_id);
                    $stmt->execute();
                    $stmt->close();

                    $_SESSION['mensalidade'] = $status_mensalidade;

                    // atualiza as variaveis sessions
                    $_SESSION["id"] = $id;
                    $_SESSION['cliente_id'] = $cliente_id;
                    $_SESSION["nome"] = $nome;
                    $_SESSION["email"] = $email;
                    $_SESSION["tipo"] = $tipo;

                    // redirecionando para mensalidade caso esteja com a mensalidade vencida
                    if ($_SESSION['mensalidade'] == 2) {
                        header("Location: ../../pages/mensalidade");
                        exit();
                    }

                    $_SESSION['resposta'] = "Bem Vindo! " . $_SESSION['nome'];

                    if ($tipo == 1) {
                        // caso o usuario for administrador
                        header("Location: " . BASE_URL . "pages/dashboard");
                        exit;
                    } elseif ($tipo = 0) {
                        // caso o usuario for caixa
                        header("Location: " . BASE_URL . "pages/vendas");
                        exit;
                    } elseif ($tipo = 2){
                        // caso o usuario for fundador
                        header("Location: " . BASE_URL . "adm/dashboard");
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
