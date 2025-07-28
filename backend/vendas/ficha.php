<?php
require_once("../conexao.php");
require_once("../funcoes/geral.php");
session_start();

// Verifica se o ID da venda foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    registrarErro($_SESSION["id"], pegarRotaUsuario(), "ID da venda não informado!", 1, pegarIpUsuario(), pegarNavegadorUsuario());
}

$venda_id = intval($_GET['id']);

// Busca os dados da venda
$stmt_venda = $conexao->prepare("SELECT v.id, v.usuario_id, v.total, v.data_venda, u.nome as usuario_nome 
                                 FROM vendas v 
                                 INNER JOIN usuarios u ON v.usuario_id = u.id 
                                 WHERE v.id = ?");
$stmt_venda->bind_param("i", $venda_id);
$stmt_venda->execute();
$resultado_venda = $stmt_venda->get_result();

if ($resultado_venda->num_rows == 0) {
    registrarErro($_SESSION["id"], pegarRotaUsuario(), "Venda não encontrada. ID procurado: " . $venda_id, 1, pegarIpUsuario(), pegarNavegadorUsuario());
}

$venda = $resultado_venda->fetch_assoc();
$stmt_venda->close();

// Busca os itens da venda
$stmt_itens = $conexao->prepare("SELECT iv.quantidade, iv.preco_unitario, p.nome as produto_nome 
                                 FROM itens_venda iv 
                                 INNER JOIN produtos p ON iv.produto_id = p.id 
                                 WHERE iv.venda_id = ?");
$stmt_itens->bind_param("i", $venda_id);
$stmt_itens->execute();
$resultado_itens = $stmt_itens->get_result();

$itens = [];
while ($row = $resultado_itens->fetch_assoc()) {
    $itens[] = [
        'nome' => $row['produto_nome'],
        'quantidade' => $row['quantidade'],
        'preco' => $row['preco_unitario']
    ];
}
$stmt_itens->close();

if (empty($itens)) {
    registrarErro($_SESSION["id"], pegarRotaUsuario(), "Nenhum item encontrado para esta venda. ID: " . $venda_id, 1, pegarIpUsuario(), pegarNavegadorUsuario());
}


$total = $venda['total'];
$data_venda = date('d/m/Y H:i:s', strtotime($venda['data_venda']));
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
        <p><strong>Número da Venda:</strong><?= $venda['id'] ?></p>
        <p><strong>Vendedor:</strong> <?= htmlspecialchars($venda['usuario_nome']) ?></p>
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