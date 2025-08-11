<?php

// Certifique-se de que esta variável contém seu token de TESTE
$accessToken = 'APP_USR-6022564160361452-081112-6db29656652e1d72d2b47ad7b5321594-578403532';

// ID do pagamento que você quer aprovar
$paymentId = '121910136320'; // Substitua pelo ID real do pagamento de teste

$data_to_send = json_encode(['status' => 'approved']);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/' . $paymentId,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => $data_to_send,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        // A linha abaixo é a mais importante para resolver o erro
        'Authorization: Bearer ' . $accessToken
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

echo "Resposta da API: ";
print_r($response);

?>