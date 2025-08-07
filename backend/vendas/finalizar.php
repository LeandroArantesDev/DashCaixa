<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = strip_tags(trim($_POST["usuario_id"]));
    $itens = json_decode($_POST['itens'], true);
    $cliente_id = strip_tags(trim($_SESSION["cliente_id"]));

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
            //Verificar se tem estoque suficiente para registrar as vendas
            $sucesso_estoque = true;
            foreach ($itens as $item) {
                // Pegar no banco de dados o estoque atual de cada item
                $stmt_estoque = $conexao->prepare("SELECT estoque FROM produtos WHERE id = ?");
                $stmt_estoque->bind_param("i", $item["id"]);

                if (!$stmt_estoque->execute()) {
                    $sucesso_estoque = false;
                    break;
                }

                $stmt_estoque->bind_result($produto_estoque);
                $stmt_estoque->fetch();

                $stmt_estoque->close();

                // Calcular estoque novo
                $novo_estoque = 0;
                $novo_estoque = $produto_estoque - $item["quantidade"];

                if ($novo_estoque < 0) {
                    $item_sem_estoque = $item["nome"];
                    $sucesso_estoque = false;
                    break;
                }
            }

            // Se o estoque for suficiente ele faz os registros e retira o estoque de cada item
            if ($sucesso_estoque == true) {
                // Inserir a venda
                $stmt = $conexao->prepare("INSERT INTO vendas (usuario_id, total, cliente_id) VALUES (?,?,?)");
                $stmt->bind_param("isi", $usuario_id, $total, $cliente_id);

                if ($stmt->execute()) {
                    // Pegando o ID da venda cadastrada
                    $venda_id = $conexao->insert_id;
                    $stmt->close();

                    // Inserir os itens da venda e reduzir o estoque de cada item
                    $sucesso_itens = true;
                    foreach ($itens as $item) {
                        $stmt_item = $conexao->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?,?,?,?)");
                        $stmt_item->bind_param("iiss", $venda_id, $item["id"], $item["quantidade"], $item["preco"]);

                        if (!$stmt_item->execute()) {
                            $sucesso_itens = false;
                            break;
                        }
                        $stmt_item->close();

                        $stmt_estoque = $conexao->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
                        $stmt_estoque->bind_param("ii", $item["quantidade"], $item["id"]);

                        if (!$stmt_estoque->execute()) {
                            $sucesso_itens = false;
                            break;
                        }

                        $stmt_estoque->close();
                    }

                    if ($sucesso_itens) {
                        $_SESSION['resposta'] = "Venda finalizada com sucesso!";
                        $_SESSION['modal_venda'] = $venda_id;
                    } else {
                        $_SESSION['resposta'] = "Venda não foi finalizada com sucesso!";
                    }
                } else {
                    $_SESSION['resposta'] = "Venda não foi finalizada com sucesso!";
                }
            } else {
                registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), $item_sem_estoque . " com estoque baixo!", "01", pegarIpUsuario(), pegarNavegadorUsuario());
                $_SESSION['resposta'] = $item_sem_estoque . " com estoque baixo!";
                header("Location: ../../pages/vendas");
                exit;
            }
        } catch (Exception $erro) {
            registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Erro ao finalizar venda!", $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
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
