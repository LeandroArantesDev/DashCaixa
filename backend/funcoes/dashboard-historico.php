<?php
include(__DIR__ . '/../conexao.php');

function buscar_vendas_diarias()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) AS total FROM `vendas` WHERE DATE(data_venda) = CURRENT_DATE()");
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
    $stmt = $conexao->prepare("SELECT SUM(total) AS vendas FROM `vendas` WHERE DATE(data_venda) = CURRENT_DATE()");
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

    $sql = "SELECT DATE(data_venda) AS dia, SUM(total) AS faturamento FROM vendas WHERE data_venda >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(data_venda) ORDER BY dia DESC";
    $stmt = $conexao->prepare($sql);
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
    $stmt = $conexao->prepare("SELECT COUNT(id) AS total FROM `produtos`");
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
    $stmt = $conexao->prepare("SELECT COUNT(id) AS total FROM `categorias`");
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

    $stmt = $conexao->prepare("SELECT nome, estoque FROM produtos WHERE estoque < 5");
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

    $stmt = $conexao->prepare("SELECT AVG(total) as media FROM vendas WHERE DATE(data_venda) = CURRENT_DATE()");
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();

        return $linha['media'] ?? 0.0;
    }

    return 0.0;
}
