<?php
require_once("../conexao.php");
require_once("../funcoes/geral.php");
session_start();

$itens = json_decode($_SESSION['itens'], true);

if (empty($itens)) {
    die("Nenhum item foi selecionado.");
}

$total = 0;
$data_venda = date('d/m/Y H:i:s');

// Calcula o total
foreach ($itens as $item) {
    $total += $item['preco'] * $item['quantidade'];
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
        <p><strong>Data da Venda:</strong> <?= $data_venda ?></p>
        <p><strong>Número de Itens:</strong> <?= count($itens) ?></p>
    </div>

    <table class="tabela-itens">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome']) ?></td>
                    <td><?= htmlspecialchars($item['quantidade']) ?></td>
                    <td><?= formatarPreco($item['preco']) ?></td>
                    <td><?= formatarPreco($item['preco'] * $item['quantidade']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="total">
        <p>TOTAL GERAL: <?= formatarPreco($total) ?></p>
    </div>
</body>

</html>