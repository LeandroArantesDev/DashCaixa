<?php
// Inclua suas conexões e funções
include("../conexao.php");
include("funcoes.php"); // Supondo que marcarFaturaComoPaga() está aqui

// 1. Receber a notificação do Mercado Pago
$json_data = file_get_contents('php://input');
$notification_data = json_decode($json_data, true);

// É uma excelente prática registrar todas as notificações para depuração
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $json_data . "\n", FILE_APPEND);

// Verifica se a notificação é sobre um pagamento
if (isset($notification_data['type']) && $notification_data['type'] === 'payment') {

    // 2. Pega o ID do pagamento da notificação
    $payment_id = $notification_data['data']['id'];

    // Suas credenciais do Mercado Pago
    $mercadoPagoAccessToken = 'APP_USR-6022564160361452-081112-6db29656652e1d72d2b47ad7b5321594-578403532';

    // 3. Busca os detalhes completos do pagamento na API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.mercadopago.com/v1/payments/" . $payment_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $mercadoPagoAccessToken"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $payment_details = json_decode($response, true);

    // Registra a resposta da API também
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Payment Details: " . $response . "\n", FILE_APPEND);

    // 4. Verifica se o pagamento foi aprovado e se temos os dados necessários
    if ($payment_details && isset($payment_details['status']) && $payment_details['status'] === 'approved' && isset($payment_details['external_reference'])) {

        // 5. Extrai a external_reference para encontrar nossa mensalidade_id
        $external_reference = $payment_details['external_reference'];

        // Extrai o ID do final da string 'ID_UNICO_DA_FATURA_123'
        $parts = explode('_', $external_reference);
        $mensalidade_id = end($parts);

        if (is_numeric($mensalidade_id)) {
            // 6. Atualiza o banco de dados
            // Usando a função que você já parece ter:
            marcarFaturaComoPaga((int)$mensalidade_id);
        }
    }
}
http_response_code(200);
exit();
?>