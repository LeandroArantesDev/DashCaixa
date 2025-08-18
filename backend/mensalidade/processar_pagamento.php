<?php
date_default_timezone_set('America/Sao_Paulo');

// Caminho do arquivo de log
$logFile = __DIR__ . "/log.txt";

// Função de log simples
function logWebhook($mensagem)
{
    global $logFile;
    file_put_contents($logFile, date("Y-m-d H:i:s") . " - " . $mensagem . PHP_EOL, FILE_APPEND);
}

// Responde sempre 200 para o Mercado Pago
http_response_code(200);

// Lê o corpo cru da requisição
$rawInput = file_get_contents("php://input");
logWebhook("RAW INPUT: " . $rawInput);

// Tenta decodificar JSON
$data = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    logWebhook("JSON inválido: " . json_last_error_msg());
    echo json_encode(["status" => "ok", "error" => "JSON inválido"]);
    exit;
}

// Loga o JSON decodificado
logWebhook("JSON DECODIFICADO: " . print_r($data, true));

// Verifica se é uma notificação de pagamento
if (isset($data['type']) && $data['type'] === 'payment' && isset($data['data']['id'])) {
    $payment_id = $data['data']['id'];
    logWebhook("Pagamento recebido: ID " . $payment_id);

    // Aqui você pode colocar a chamada da sua função de processamento real:
    // ex: marcarFaturaComoPaga($payment_id);
    // Exemplo de log:
    logWebhook("Processamento do pagamento ID $payment_id executado (ainda só logando)");
} else {
    logWebhook("Notificação ignorada ou formato inválido.");
}

// Retorna sempre OK
echo json_encode(["status" => "ok"]);
exit;
