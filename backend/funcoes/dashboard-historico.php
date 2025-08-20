<?php
function buscar_vendas_diarias()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) AS total FROM `vendas` WHERE DATE(data_venda) = CURRENT_DATE() AND cliente_id = ?");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();
    $vendas = 0;
    $stmt->bind_result($vendas);
    $stmt->fetch();
    $stmt = null;

    return $vendas;
}

function buscar_faturamento_diario()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT SUM(total) AS vendas FROM `vendas` WHERE DATE(data_venda) = CURRENT_DATE() AND cliente_id = ?");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();
    $vendas = 0;
    $stmt->bind_result($vendas);
    $stmt->fetch();
    $stmt = null;

    if ($vendas == null) {
        $vendas = 0;
    }

    return ($vendas);
}

function buscar_faturamento_semanal()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT DATE(data_venda) AS dia, SUM(total) AS faturamento FROM vendas WHERE cliente_id = ? AND data_venda >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(data_venda) ORDER BY dia DESC");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $vendas = [];
    while ($row = $resultado->fetch_assoc()) {
        $vendas[] = $row;
    }

    return ($vendas);
}

function buscar_produto_total()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) AS total FROM `produtos` WHERE cliente_id = ? AND status IN (0,1)");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();
    $produtos = 0;
    $stmt->bind_result($produtos);
    $stmt->fetch();
    $stmt = null;

    return ($produtos);
}

function buscar_categoria_total()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) AS total FROM `categorias` WHERE cliente_id = ? AND status IN (0,1)");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();
    $categorias = 0;
    $stmt->bind_result($categorias);
    $stmt->fetch();
    $stmt = null;

    return ($categorias);
}

function buscar_alerta_estoque_baixo()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT nome, estoque FROM produtos WHERE estoque < 5 AND cliente_id = ? AND status IN (0,1)");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $produtos = [];
    while ($row = $resultado->fetch_assoc()) {
        $produtos[] = $row;
    }

    return ($produtos);
}

function media_preco_vendas()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT AVG(total) as media FROM vendas WHERE DATE(data_venda) = CURRENT_DATE() AND cliente_id = ?");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();

        return $linha['media'] ?? 0.0;
    }

    return 0.0;
}
