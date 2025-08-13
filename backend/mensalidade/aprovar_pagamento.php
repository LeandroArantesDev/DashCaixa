<?php
// Configurações
$payment_id = "1340348847"; // Substitua pelo ID real
$access_token = 'TEST-6022564160361452-081112-3ab0d9536a1f271c03093ea88dc04e3f-578403532';

// url webhook
$url_webhook = 'https://d12b7e571089.ngrok-free.app/DashCaixa/';

// 1. Consultar o pagamento
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.mercadopago.com/v1/payments/$payment_id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $access_token"
    ]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    die("Erro ao consultar pagamento: HTTP $http_code");
}

$payment = json_decode($response, true);

// 2. Verificar se é um PIX pendente
if ($payment['payment_method_id'] !== 'pix' || $payment['status'] !== 'pending') {
    die("Pagamento não é PIX ou já foi processado");
}

// 3. Simular aprovação via webhook (método recomendado pelo Mercado Pago)
$webhook_url = $url_webhook . '/backend/mensalidade/processar_pagamento.php'; // Substitua pela sua URL real

$payload = [
    'action' => 'payment.updated',
    'data' => ['id' => $payment_id],
    'type' => 'payment',
    'date_created' => date('Y-m-d\TH:i:s.v\Z')
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $webhook_url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Signature: ' . hash_hmac('sha256', json_encode($payload), 'seu_segredo_webhook') // Opcional
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
curl_close($ch);

echo "<h2>Webhook disparado com sucesso!</h2>";
echo "<p>O pagamento PIX #$payment_id será processado em breve.</p>";
echo "<p>Verifique o arquivo <code>webhook_log.txt</code> para confirmar.</p>";