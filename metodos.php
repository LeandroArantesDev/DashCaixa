<?php

// Seu token de acesso de teste do Mercado Pago
$accessToken = 'APP_USR-1277687938145888-081113-975667558598b729fd39a580d1597908-2622662708';

// URL do endpoint da API
$url = 'https://api.mercadopago.com/v1/payment_methods';

// Inicializa o cURL
$ch = curl_init();

// Configura as opções da requisição
curl_setopt_array($ch, [
    CURLOPT_URL => $url, // Define a URL
    CURLOPT_RETURNTRANSFER => true, // Retorna a resposta como uma string em vez de imprimi-la
    CURLOPT_HTTPHEADER => [ // Define os cabeçalhos da requisição
        'Accept: application/json',
        'Authorization: Bearer ' . $accessToken
    ]
]);

// Executa a requisição
$response = curl_exec($ch);

// Verifica se ocorreu algum erro durante a requisição
if (curl_errno($ch)) {
    echo 'Erro no cURL: ' . curl_error($ch);
}

// Fecha a sessão cURL
curl_close($ch);

// Decodifica a resposta JSON para um array PHP
$payment_methods = json_decode($response, true);

// Exibe o resultado de forma legível
echo "<pre>";
print_r($payment_methods);
echo "</pre>";

?>