<?php
//includes
include("funcoes.php");
include("../funcoes/geral.php");
include("../conexao.php");

// Verificação inicial para garantir que a função registrarErro exista antes de prosseguir.
if (!function_exists('registrarErro')) {
    http_response_code(500);
    // Se a função de log de erro não existir, não há como registrar o problema.
    exit("Falha crítica: Função de log de erros não encontrada.");
}

$ip_origem = pegarIpUsuario();

// verificando se as funções foram carregadas
if (!function_exists('marcarFaturaComoPaga')) {
    registrarErro(null, null, 'webhook_mercado_pago', "ERRO FATAL: Função marcarFaturaComoPaga() não encontrada!", '500', $ip_origem, 'Webhook: MercadoPago');
    http_response_code(500);
    exit();
}

// captura e valida o payload
$json_data = file_get_contents('php://input');
$notification_data = json_decode($json_data, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    $json_error_msg = json_last_error_msg();
    // Registra o erro de JSON inválido no banco.
    registrarErro(null, null, 'webhook_mercado_pago', "ERRO: Payload JSON inválido. Erro: " . $json_error_msg, '400', $ip_origem, 'Webhook: MercadoPago');
    http_response_code(400); // Bad Request
    exit();
}

// verificando se a notificação é de pagamento
$is_payment_notification = (isset($notification_data['type']) && $notification_data['type'] === 'payment') ||
    (isset($notification_data['action']) && strpos($notification_data['action'], 'payment') !== false);

if (!$is_payment_notification || !isset($notification_data['data']['id'])) {
    // Caso não for de pagamento ignorar a notificação.
    http_response_code(200);
    echo "Notificação ignorada (não é de pagamento).";
    exit();
}

// detalhes da API Mercado Pago
$payment_id = $notification_data['data']['id'];
$mercadoPagoAccessToken = $_ENV['SENHA_API_MP'];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.mercadopago.com/v1/payments/" . $payment_id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $mercadoPagoAccessToken"
    ],
]);
$response = curl_exec($curl);

if ($response === false) {
    $curl_error = curl_error($curl);
    $curl_errno = curl_errno($curl);
    curl_close($curl);
    // Registra a falha na comunicação com a API do Mercado Pago.
    registrarErro(null, null, 'webhook_mercado_pago', "ERRO: Falha na requisição CURL: " . $curl_error, $curl_errno, $ip_origem, 'Webhook: MercadoPago');
    http_response_code(500);
    exit();
}
curl_close($curl);

$payment_details = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $json_error_msg = json_last_error_msg();
    // Registra o erro de resposta inválida da API.
    registrarErro(null, null, 'webhook_mercado_pago', "ERRO: Resposta da API MP não é um JSON válido. Erro: " . $json_error_msg, 'API_RESPONSE_INVALID', $ip_origem, 'Webhook: MercadoPago');
    http_response_code(500);
    exit();
}

// processando pagamento aprovado
if ($payment_details && isset($payment_details['status']) && $payment_details['status'] === 'approved' && isset($payment_details['external_reference'])) {
    $external_reference = $payment_details['external_reference'];
    $parts = explode('_', $external_reference);
    $mensalidade_id = end($parts);

    if (is_numeric($mensalidade_id)) {
        $resultado = marcarFaturaComoPaga((int)$mensalidade_id);

        if ($resultado === false) {
            // Registra a falha ao tentar atualizar o status da fatura no banco de dados.
            registrarErro(null, null, 'webhook_mercado_pago', "ERRO: marcarFaturaComoPaga retornou FALSE para mensalidade ID: $mensalidade_id.", 'DB_UPDATE_FAILED', $ip_origem, 'Webhook: MercadoPago');
        }
    } else {
        // Registra o erro de referência externa malformada.
        registrarErro(null, null, 'webhook_mercado_pago', "ERRO: external_reference inválido ('$external_reference'). Não contém ID numérico.", 'INVALID_REFERENCE', $ip_origem, 'Webhook: MercadoPago');
    }
}

// caso de tudo certo resposta 200
http_response_code(200);
echo "Notificação recebida e processada.";
exit();
?>