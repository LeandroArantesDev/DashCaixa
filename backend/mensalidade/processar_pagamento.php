<?php
// Arquivo: processar_pagamento.php

// Caminho do log
$logFile = __DIR__ . "/webhook.log";

// Função de log
function registrarLog($mensagem) {
    global $logFile;
    file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "] " . $mensagem . PHP_EOL, FILE_APPEND);
}

try {
    // Lê cabeçalhos
    $headers = getallheaders();
    $contentType = isset($headers["Content-Type"]) ? $headers["Content-Type"] : "não informado";

    // Lê corpo da requisição
    $input = file_get_contents("php://input");
    registrarLog("Conteúdo recebido: " . $input);
    registrarLog("Content-Type: " . $contentType);

    $dados = null;

    // Se for JSON
    if (stripos($contentType, "application/json") !== false) {
        $dados = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            registrarLog("Erro ao decodificar JSON: " . json_last_error_msg());
        } else {
            registrarLog("JSON decodificado com sucesso: " . print_r($dados, true));
        }
    }
    // Se for formulário normal
    elseif (stripos($contentType, "application/x-www-form-urlencoded") !== false) {
        parse_str($input, $dados);
        registrarLog("Form data parseado: " . print_r($dados, true));
    }
    // Caso não identificado
    else {
        registrarLog("Formato de requisição não identificado. Dados crus: " . $input);
    }

    // Aqui você pode colocar a lógica real de salvar no banco
    // Exemplo (somente para debug):
    if ($dados) {
        registrarLog("Processando dados: " . json_encode($dados));
    }

    // Sempre responde 200
    http_response_code(200);
    echo "OK";

} catch (Exception $e) {
    registrarLog("Erro inesperado: " . $e->getMessage());
    http_response_code(200); // responde 200 mesmo em erro
    echo "OK";
}
