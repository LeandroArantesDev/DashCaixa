<?php
require_once("../conexao.php");
require_once("../funcoes/geral.php");
session_start();

// Verifica se o ID da venda foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "ID da venda não informado!", 1, pegarIpUsuario(), pegarNavegadorUsuario());
}

$mensalidade_id = intval($_GET['id']);

// Busca os dados da venda
$stmt_mensalidade = $conexao->prepare("SELECT id, (SELECT nome FROM clientes p WHERE p.id = m.cliente_id) AS cliente, valor, data_vencimento, data_pagamento, status FROM mensalidades m WHERE id = ?");
$stmt_mensalidade->bind_param("i", $mensalidade_id);
$stmt_mensalidade->execute();
$resultado_mensalidade = $stmt_mensalidade->get_result();

if ($resultado_mensalidade->num_rows == 0) {
    registrarErro($_SESSION["cliente_id"], $_SESSION["id"], pegarRotaUsuario(), "Venda não encontrada. ID procurado: " . $venda_id, 1, pegarIpUsuario(), pegarNavegadorUsuario());
}

$mensalidade = $resultado_mensalidade->fetch_assoc();
$stmt_mensalidade->close();

$data_vencimento = date('d/m/Y', strtotime($mensalidade['data_vencimento']));

if ($mensalidade["data_pagamento"] == null) {
    $data_pagamento = "N/A";
} else {
    $data_pagamento = date('d/m/Y', strtotime($mensalidade['data_pagamento']));
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Venda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }

        .cabecalho {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .info-venda {
            margin-bottom: 30px;
        }

        .tabela-itens {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .tabela-itens th,
        .tabela-itens td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .tabela-itens th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .botoes {
            text-align: center;
            margin-top: 30px;
        }

        .botoes button {
            background-color: #007cba;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .botoes button:hover {
            background-color: #005a8b;
        }
    </style>
</head>

<body>
    <div class="cabecalho">
        <h1>FICHA DE VENDA</h1>
        <p>DashCaixa - Sistema de Vendas</p>
    </div>

    <div class="info-venda">
        <p><strong>ID da mensalidade:</strong> <?= $mensalidade['id'] ?></p>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($mensalidade['cliente']) ?></p>
    </div>

    <table class="tabela-itens">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Preço da fatura</th>
                <th>Data de vencimento</th>
                <th>Data de pagamento</th>
                <th>Situação</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars($mensalidade['id']) ?></td>
                <td><?= htmlspecialchars($mensalidade['cliente']) ?></td>
                <td><?= formatarPreco($mensalidade['valor']) ?></td>
                <td><?= htmlspecialchars($data_vencimento) ?></td>
                <td><?= htmlspecialchars($data_pagamento) ?></td>
                <td>
                    <?php
                    // trocando o texto caso for pendente vencida ou paga
                    if ($mensalidade['status'] == 0): ?>
                        <p>Pago</p>
                    <?php elseif ($mensalidade['status'] == 1): ?>
                        <p>Pendente</p>
                    <?php elseif ($mensalidade['status'] == 2): ?>
                        <p>Vencida</p>
                    <?php endif ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="total">
        <p>TOTAL GERAL: <?= formatarPreco($mensalidade['valor']) ?></p>
    </div>
</body>

</html>