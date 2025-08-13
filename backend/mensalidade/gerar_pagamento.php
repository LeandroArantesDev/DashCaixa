<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
function generate_uuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}


$mensalidade_id = $_POST['mensalidade_id'];
$valor = $_POST['valor'];
$cliente_id = $_SESSION['cliente_id'];

// buscando dados do cliente
$stmt = $conexao->prepare("SELECT nome, email, telefone, documento FROM clientes WHERE id = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$stmt->bind_result($nome_cliente, $email_cliente, $telefone_cliente, $doc_cliente);
$stmt->fetch();
$stmt->close();

if (!isset($mensalidade_id) || !isset($valor)) {
    $_SESSION['resposta'] = "Erro ao gerar o pagamento";
    header("Location: ../../pages/mensalidade");
    exit();
}

$mercadoPagoAccessToken = 'APP_USR-6022564160361452-081112-6db29656652e1d72d2b47ad7b5321594-578403532';
$mercadoPagoExternalReference = 'ID_UNICO_DA_FATURA_' . $mensalidade_id;

// URL webhook
$url_webhook = 'https://076f731191cb.ngrok-free.app/DashCaixa';

// formatando informações
$telefone_cliente_apenas_numeros = preg_replace('/[^0-9]/', '', $telefone_cliente);
$doc_cliente_apenas_numeros = preg_replace('/[^0-9]/', '', $doc_cliente);
$nome_cliente_partes = explode(' ', $nome_cliente, 2);
$primeiro_nome = $nome_cliente_partes[0];
$ultimo_nome = isset($nome_cliente_partes[1]) ? $nome_cliente_partes[1] : '';

// dados da requisição para a API do Mercado Pago
$payload = json_encode([
    'description' => 'Mensalidade do DashCaixa - ' . $mensalidade_id,
    'transaction_amount' => (float)$valor,
    'payment_method_id' => 'pix',
    'payer' => [
        'first_name' => $primeiro_nome,
        'last_name' => $ultimo_nome,
        'email' => $email_cliente,
        'identification' => [
            'type' => (strlen($doc_cliente_apenas_numeros) === 11) ? 'CPF' : 'CNPJ',
            'number' => $doc_cliente_apenas_numeros
        ]
    ],
    'external_reference' => $mercadoPagoExternalReference,
    'notification_url' => $url_webhook . '/backend/mensalidade/processar_pagamento.php',
]);
$idempotencyKey = generate_uuid();

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.mercadopago.com/v1/payments",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $mercadoPagoAccessToken",
        "Content-Type: application/json",
        "X-Idempotency-Key: " . $idempotencyKey
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
    $data = json_decode($response, true);

    // Verifica se a requisição foi bem-sucedida e se o pagamento foi criado
    if (isset($data['status']) && $data['status'] === 'pending') {
        $payment_url = $data['point_of_interaction']['transaction_data']['ticket_url'];
        $pix_code = $data['point_of_interaction']['transaction_data']['qr_code'];
        $pix_base64_image = $data['point_of_interaction']['transaction_data']['qr_code_base64'];

        $stmt = $conexao->prepare("UPDATE mensalidades SET url_pagamento = ?, idempotency_key = ? WHERE id = ?");
        $stmt->bind_param("ssi", $payment_url, $idempotencyKey, $mensalidade_id);
        $stmt->execute();
        $stmt->close();

        header("Location: " . $payment_url);
        exit();
    } else {
        // Trata erros da API do Mercado Pago
        $error_message = isset($data['message']) ? $data['message'] : "Erro desconhecido ao gerar o pagamento.";
        $_SESSION['resposta'] = "Erro do gateway: " . $error_message . " Detalhes: " . json_encode(isset($data['cause']) ? $data['cause'] : $data);
        header("Location: ../../pages/mensalidade");
        exit();
    }
}
?>