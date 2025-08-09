<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $venda_id = strip_tags(trim($_POST["item_id"]));

    // Validação do CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: ../../pages/historico");
        exit;
    }

    // Validação do ID da venda
    if (empty($venda_id) || !is_numeric($venda_id)) {
        $_SESSION['resposta'] = "ID da venda inválido!";
        header("Location: ../../pages/historico");
        exit;
    }

    try {
        // Iniciar transação para garantir integridade dos dados
        $conexao->autocommit(false);

        // Verificar se a venda existe
        $stmt_verificar = $conexao->prepare("SELECT id FROM vendas WHERE id = ?");
        $stmt_verificar->bind_param("i", $venda_id);

        if (!$stmt_verificar->execute()) {
            throw new Exception("Erro ao verificar venda no banco!", 1);
        }

        $stmt_verificar->bind_result($venda_existe);
        $stmt_verificar->fetch();
        $stmt_verificar->close();

        if (!$venda_existe) {
            $conexao->rollback();
            $_SESSION['resposta'] = "Venda não encontrada!";
            header("Location: ../../pages/historico");
            exit;
        }

        // Buscar no banco de dados os itens vendidos
        $stmt_itens = $conexao->prepare("SELECT produto_id, quantidade FROM itens_venda WHERE venda_id = ?");
        $stmt_itens->bind_param("i", $venda_id);

        if (!$stmt_itens->execute()) {
            throw new Exception("Erro ao buscar itens da venda!", 2);
        }

        $stmt_itens->bind_result($produto_id, $quantidade);

        // Inicializa o array
        $itens_venda = [];

        // Busca os dados
        while ($stmt_itens->fetch()) {
            $itens_venda[] = [
                'produto_id' => $produto_id,
                'quantidade' => $quantidade,
            ];
        }
        $stmt_itens->close();

        // Verificar se encontrou itens
        if (empty($itens_venda)) {
            $conexao->rollback();
            registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Nenhum item encontrado para a venda ID: " . $venda_id, 3, pegarIpUsuario(), pegarNavegadorUsuario());
            $_SESSION['resposta'] = "Nenhum item encontrado para esta venda!";
            header("Location: ../../pages/historico");
            exit;
        }

        // Preparar statement para devolver estoque (reutilizar)
        $stmt_estoque = $conexao->prepare("UPDATE produtos SET estoque = estoque + ? WHERE id = ?");

        // Devolver estoque para os produtos que foram vendidos
        foreach ($itens_venda as $item) {
            $stmt_estoque->bind_param("ii", $item["quantidade"], $item["produto_id"]);

            if (!$stmt_estoque->execute()) {
                throw new Exception("Erro ao devolver estoque do produto ID: " . $item["produto_id"], 4);
            }
        }
        $stmt_estoque->close();

        // Deletar primeiro os itens da venda (devido à constraint de FK)
        $stmt_delete_itens = $conexao->prepare("DELETE FROM itens_venda WHERE venda_id = ?");
        $stmt_delete_itens->bind_param("i", $venda_id);

        if (!$stmt_delete_itens->execute()) {
            throw new Exception("Erro ao deletar itens da venda!", 5);
        }
        $stmt_delete_itens->close();

        // Depois deletar a venda
        $stmt_venda = $conexao->prepare("DELETE FROM vendas WHERE id = ?");
        $stmt_venda->bind_param("i", $venda_id);

        if (!$stmt_venda->execute()) {
            throw new Exception("Erro ao deletar a venda!", 6);
        }
        $stmt_venda->close();

        // Se chegou até aqui, confirma todas as operações
        $conexao->commit();

        // Log de sucesso para auditoria
        registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Venda ID: " . $venda_id . " deletada com sucesso!", 0, pegarIpUsuario(), pegarNavegadorUsuario());

        $_SESSION['resposta'] = "Venda deletada com sucesso!";
    } catch (Exception $erro) {
        // Em caso de erro, desfaz todas as operações
        $conexao->rollback();

        // Tratamento específico para diferentes tipos de erro
        switch ($erro->getCode()) {
            case 1451: // Constraint de chave estrangeira
                $mensagem = "Erro: Esta venda não pode ser deletada pois possui dependências no sistema.";
                break;
            case 1062: // Entrada duplicada
                $mensagem = "Erro: Conflito de dados ao processar a exclusão.";
                break;
            default:
                $mensagem = "Erro ao deletar venda: " . $erro->getMessage();
                break;
        }

        registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Erro ao deletar venda ID: " . $venda_id . " - " . $erro->getMessage(), $erro->getCode(), pegarIpUsuario(), pegarNavegadorUsuario());
        $_SESSION['resposta'] = $mensagem;
    } finally {
        // Garantir que autocommit seja restaurado
        $conexao->autocommit(true);
    }

    header("Location: ../../pages/historico");
    exit;
} else {
    $_SESSION['resposta'] = "Método de solicitação inválido!";
    header("Location: ../../pages/historico");
    exit;
}
