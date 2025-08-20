<?php
// Lê corpo do webhook
$input = file_get_contents("php://input");

// Lê headers
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';

// Log para depuração
file_put_contents("log.txt", date("Y-m-d H:i:s")." - Recebido: ".$input."\n", FILE_APPEND);
file_put_contents("log.txt", date("Y-m-d H:i:s")." - X-Signature: ".$signature."\n", FILE_APPEND);
file_put_contents("log.txt", date("Y-m-d H:i:s")." - X-Request-Id: ".$requestId."\n", FILE_APPEND);

// Extrair ts e v1 do header
$parts = explode(',', $signature);
$ts = null;
$v1 = null;
foreach ($parts as $part) {
    [$key, $value] = explode('=', $part, 2);
    $key = trim($key);
    $value = trim($value);
    if ($key === 'ts') $ts = $value;
    if ($key === 'v1') $v1 = $value;
}

// Decodifica JSON
$dataJson = json_decode($input, true);
$dataId = strtolower($dataJson['data']['id'] ?? ''); // minúsculas se alfanumérico

// Monta manifest string
$manifest = "id:$dataId;request-id:$requestId;ts:$ts;";

// Webhook Signing Secret
$secret = $_ENV['SEGREDO_WEBHOOK_MP'];

// Calcula hash HMAC
$expected_hash = hash_hmac('sha256', $manifest, $secret);

// Log hashes
file_put_contents("log.txt", date("Y-m-d H:i:s")." - Hash esperado: ".$expected_hash."\n", FILE_APPEND);
file_put_contents("log.txt", date("Y-m-d H:i:s")." - Hash recebido: ".$v1."\n", FILE_APPEND);

// Verifica assinatura
if (!hash_equals($expected_hash, $v1)) {
    file_put_contents("log.txt", date("Y-m-d H:i:s")." - Tentativa inválida - assinatura incorreta\n", FILE_APPEND);
    exit;
}

// Processa pagamento
if (isset($dataJson['type']) && $dataJson['type'] === 'payment') {
    $payment_id = $dataJson['data']['id'];

    file_put_contents("log.txt", date("Y-m-d H:i:s")." - Pagamento verificado: ".$payment_id ."\n", FILE_APPEND);

    // atualizando banco de dados
    marcarFaturaComoPaga($payment_id);
    http_response_code(200);
    exit();
}
