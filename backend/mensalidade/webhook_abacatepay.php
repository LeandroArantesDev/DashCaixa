<?php
include("../conexao.php");
include("../funcoes/geral.php");
include("funcoes.php");

// segredo do webhook
define('WEBHOOK_SECRET', 'segredo_webhook');

// Verifica se o secret enviado nos headers bate com o esperado
$headers = getallheaders();
$received_secret = $headers['X-Abacatepay-Secret'] ?? $headers['x-abacatepay-secret'] ?? null;

if ($received_secret !== WEBHOOK_SECRET) {
    http_response_code(401);
    echo 'Acesso não autorizado.';
    exit;
}

// Recebe os dados enviados
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

// Log para debug (opcional, remover depois de testar)
file_put_contents("webhook_log.txt", $payload . PHP_EOL, FILE_APPEND);

// Verifica se o evento é de pagamento confirmado
if (!isset($data['event']) || $data['event'] !== 'billing.paid') {
    http_response_code(400);
    echo 'Evento não suportado.';
    exit;
}

// Pega os dados principais
$billing = $data['data']['billing'];
$status = $billing['status'] ?? '';
$externalId = $billing['products'][0]['externalId'] ?? null;
$valor_pago = $billing['paidAmount'] ?? 0;

// Validação básica
if ($status !== 'PAID' || $externalId === null) {
    http_response_code(400);
    echo 'Dados inválidos.';
    exit;
}

// Atualiza a mensalidade como paga
if (marcarFaturaComoPaga($externalId)) {
    http_response_code(200);
    echo 'OK';
} else {
    http_response_code(400);
    echo 'ERRO';
}
