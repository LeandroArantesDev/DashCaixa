<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = strip_tags(trim($_POST["usuario_id"]));
    $itens = json_decode($_POST['itens'], true);

    if (empty($itens)) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/vendas");
        exit;
    }

    $total = 0;
    $data_venda = date('d/m/Y H:i:s');

    // Calcula o total
    foreach ($itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/vendas");
        exit;
    }

    // Verificar se tudo chegou corretamente
    if (!empty($usuario_id) && !empty($itens)) {
        try {
            // Inserir a venda
            $stmt = $conexao->prepare("INSERT INTO vendas (usuario_id, total) VALUES (?,?)");
            $stmt->bind_param("is", $usuario_id, $total);

            if ($stmt->execute()) {
                // Pegando o ID da venda cadastrada
                $venda_id = $conexao->insert_id;
                $stmt->close();

                // Inserir os itens da venda
                $sucesso_itens = true;
                foreach ($itens as $item) {
                    $stmt_item = $conexao->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco) VALUES (?,?,?,?)");
                    $stmt_item->bind_param("iiss", $venda_id, $item["id"], $item["quantidade"], $item["preco"]);

                    if (!$stmt_item->execute()) {
                        $sucesso_itens = false;
                        break;
                    }
                    $stmt_item->close();
                }

                if ($sucesso_itens) {
                    $_SESSION['resposta'] = "Venda realizada com sucesso!";
                } else {
                    $_SESSION['resposta'] = "Erro ao cadastrar itens da venda!";
                }
            } else {
                $_SESSION['resposta'] = "Erro ao cadastrar venda!";
            }
        } catch (Exception $erro) {
            registrarErro($_SESSION["id"], pegarRotaUsuario(), "Erro ao finalizar venda!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "error" . $erro->getCode();
        }

        header("Location: ../../pages/vendas");
        exit;
    } else {
        $_SESSION['resposta'] = "Parâmetros inválidos";
        header("Location: ../../pages/vendas");
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: ../../pages/vendas");
    exit;
}
