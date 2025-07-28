<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

$fatura_id = $_POST['fatura_id'];
$valor_fatura = $_POST['valor'];
$cliente_id = $_SESSION['cliente_id'];

// convertendo para centavos o valor
$valor_fatura = $valor_fatura * 100;

// buscando dados do cliente
$stmt = $conexao->prepare("SELECT nome, email, telefone, documento FROM clientes WHERE id = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$stmt->bind_result($nome_cliente, $email_cliente, $telefone_cliente, $doc_cliente);
$stmt->fetch();
$stmt->close();

if (!isset($fatura_id) || !isset($valor_fatura)) {
    $_SESSION['resposta'] = "Erro ao gerar o pagamento";
    header("Location: ../../pages/mensalidade");
    exit();
}

$abacatePayApiKey = 'abc_dev_tscgwnkCY3Ncexhr6BchapNT';

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.abacatepay.com/v1/billing/create",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'frequency' => 'ONE_TIME',
        'methods' => [
            'PIX'
        ],
        'allowCoupons' => null,
        'products' => [
            [
                'name' => 'DashCaixa',
                'quantity' => 1,
                'price' => $valor_fatura,
                'description' => 'Mensalidade do DashCaixa',
                'externalId' => $fatura_id
            ]
        ],
        'returnUrl' => 'http://localhost/DashCaixa/pages/mensalidade/',
        'completionUrl' => 'http://localhost/DashCaixa/backend/processar_pagamento.php?fatura_id=' . $fatura_id,
        'customer' => [
            'name' => $nome_cliente,
            'cellphone' => $telefone_cliente,
            'email' => $email_cliente,
            'taxId' => $doc_cliente,
        ]
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $abacatePayApiKey",
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    // erro na comunicação com a API
    $_SESSION['resposta'] = "Erro de comunicação com o gateway de pagamento: " . $err;
    header("Location: ../../pages/mensalidade");
    exit();
} else {
    // decodifica a resposta JSON para um array
    $data = json_decode($response, true);

    // verifica se a API retornou um erro interno
    if (isset($data['error']) && $data['error'] !== null) {
        $_SESSION['resposta'] = "Erro do gateway: " . json_encode($data['error']);
        header("Location: ../../pages/mensalidade");
        exit();
    }

    // se tudo deu certo, extrai a URL de pagamento e redireciona o cliente
    if (isset($data['data']['url'])) {
        $payment_url = $data['data']['url'];

        // guarda a URL no banco caso o usuario queira pagar outro momento
        $stmt = $conexao->prepare("UPDATE mensalidades SET url_pagamento = ? WHERE id = ?");
        $stmt->bind_param("si", $payment_url, $fatura_id);
        $stmt->execute();
        $stmt->close();

        header("Location: " . $payment_url);
        exit();
    } else {
        // caso a URL não seja encontrada na resposta por algum motivo
        $_SESSION['resposta'] = "Não foi possível obter a URL de pagamento.";
        header("Location: ../../pages/mensalidade");
        exit();
    }
}