<?php
// Forçar logging de todos os erros
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$logFile = __DIR__ . "/log_pagamento.txt";

function registrarLog($mensagem) {
    global $logFile;
    $data = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$data] $mensagem\n", FILE_APPEND);
}

// Captura o corpo cru da requisição
$input = file_get_contents("php://input");

// Captura cabeçalhos
$headers = getallheaders();

// Registrar a requisição bruta
registrarLog("Requisição recebida:");
registrarLog("Headers: " . json_encode($headers));
registrarLog("Body: " . $input);

// Tentar decodificar JSON
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    registrarLog("Erro ao decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}

// Verificação de campos obrigatórios
if (!isset($data['type']) || !isset($data['data']['id'])) {
    registrarLog("Payload inválido: faltando campos obrigatórios.");
    http_response_code(400);
    echo "Invalid payload";
    exit;
}

// Tudo certo, processar evento
registrarLog("Evento recebido: " . $data['type'] . " | ID: " . $data['data']['id']);

http_response_code(200);
echo "OK";
