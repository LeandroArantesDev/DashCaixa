<?php
// Assume-se que o arquivo de conexão define a variável global $conexao
// include(__DIR__ . '/../conexao.php');

function totalClientesAtivos()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) FROM clientes WHERE status = 0");
    $stmt->execute();
    $total = 0;
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt = null;
    return $total ?? 0;
}

function receitaMensal()
{
    global $conexao;
    $mesAtual = date('Y-m');
    $stmt = $conexao->prepare("SELECT SUM(valor) FROM mensalidades WHERE DATE_FORMAT(data_pagamento, '%Y-%m') = ?");
    $stmt->bind_param("s", $mesAtual);
    $stmt->execute();
    $receita = 0;
    $stmt->bind_result($receita);
    $stmt->fetch();
    $stmt = null;
    return $receita ?? 0;
}

function taxaAdimplencia()
{
    global $conexao;

    // Total de mensalidades
    $stmt_total = $conexao->prepare("SELECT COUNT(id) FROM mensalidades");
    $stmt_total->execute();
    $total = 0;
    $stmt_total->bind_result($total);
    $stmt_total->fetch();
    $stmt_total = null;

    // Mensalidades pagas
    $stmt_pagas = $conexao->prepare("SELECT COUNT(id) FROM mensalidades WHERE status = 0");
    $stmt_pagas->execute();
    $pagas = 0;
    $stmt_pagas->bind_result($pagas);
    $stmt_pagas->fetch();
    $stmt_pagas = null;

    if ($total > 0) {
        return round(($pagas / $total) * 100, 2);
    }
    return 0;
}

function totalUsuariosAtivosPorTipo($tipo)
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) FROM usuarios WHERE status = 0 AND tipo = ?");
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $total = 0;
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt = null;
    return $total ?? 0;
}

function receitaUltimos6Meses()
{
    global $conexao;
    $sql = "
        SELECT DATE_FORMAT(data_pagamento, '%Y-%m') AS mes, SUM(valor) AS receita
        FROM mensalidades
        WHERE data_pagamento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dados = [];
    while ($row = $resultado->fetch_assoc()) {
        $dados[] = $row;
    }
    return $dados;
}

function crescimentoClientes()
{
    global $conexao;
    $sql = "
        SELECT DATE_FORMAT(criado_em, '%Y-%m') AS mes, COUNT(id) AS total
        FROM clientes
        WHERE criado_em >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes ASC
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dados = [];
    while ($row = $resultado->fetch_assoc()) {
        $dados[] = $row;
    }
    return $dados;
}

function resumoFinanceiroMes()
{
    global $conexao;
    $mesAtual = date('Y-m');
    $sql = "
        SELECT 
            SUM(valor) AS previsto,
            SUM(CASE WHEN status = 0 THEN valor ELSE 0 END) AS recebido,
            SUM(CASE WHEN status != 0 THEN valor ELSE 0 END) AS pendente
        FROM mensalidades
        WHERE DATE_FORMAT(data_vencimento, '%Y-%m') = ?
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $mesAtual);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

function statusPagamentos()
{
    global $conexao;
    $sql = "
        SELECT 
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS pagos,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pendentes,
            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS vencidos
        FROM mensalidades
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}
function clientesComMaisErros()
{
    global $conexao;
    $sql = "
        SELECT clientes.nome, COUNT(erros.id) AS total
        FROM erros
        JOIN clientes ON erros.cliente_id = clientes.id
        GROUP BY clientes.id, clientes.nome
        ORDER BY total DESC
        LIMIT 3
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $dados = [];

    while ($row = $resultado->fetch_assoc()) {
        $dados[] = $row;
    }

    return $dados;
}
function rotasComMaisErros()
{
    global $conexao;
    $sql = "
        SELECT rota, COUNT(id) AS total
        FROM erros
        GROUP BY rota
        ORDER BY total DESC
        LIMIT 3
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $dados = [];

    while ($row = $resultado->fetch_assoc()) {
        $dados[] = $row;
    }

    return $dados;
}

function totalUsuarios()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT COUNT(id) FROM usuarios WHERE status = 0");
    $stmt->execute();
    $total = 0;
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt = null;
    return $total ?? 0;
}

?>