<?php
include("../conexao.php");
include("funcoes.php");

// função auxiliar para buscar o cliente_id a partir da mensalidade_id
function obterClienteIdDaMensalidade($mensalidade_id, $conexao) {
    $stmt = $conexao->prepare("SELECT cliente_id FROM mensalidades WHERE id = ?");
    $stmt->bind_param("i", $mensalidade_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        return $data['cliente_id'];
    }
    return null;
}


// receber a notificação do Mercado Pago
$json_data = file_get_contents('php://input');
$notification_data = json_decode($json_data, true);

// verifica o tipo de notificação
$is_payment_notification = (isset($notification_data['type']) && $notification_data['type'] === 'payment') ||
    (isset($notification_data['action']) && strpos($notification_data['action'], 'payment') !== false);

if ($is_payment_notification && isset($notification_data['data']['id'])) {
    $payment_id = $notification_data['data']['id'];

    // busca os detalhes completos do pagamento na API
    $mercadoPagoAccessToken = 'APP_USR-6022564160361452-081112-6db29656652e1d72d2b47ad7b5321594-578403532';

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

    // processa a resposta
    if ($payment_details && isset($payment_details['status']) && $payment_details['status'] === 'approved' && isset($payment_details['external_reference'])) {
        $external_reference = $payment_details['external_reference'];
        $parts = explode('_', $external_reference);
        $mensalidade_id = end($parts);

        if (is_numeric($mensalidade_id)) {
            // caso de tudo certo
            marcarFaturaComoPaga((int)$mensalidade_id);
        } else {
            // ERRO: O ID da mensalidade extraído não é numérico.
            $cliente_id_erro = obterClienteIdDaMensalidade($mensalidade_id, $conexao);
            $mensagem_erro = "Webhook MP: external_reference ('$external_reference') não continha um ID numérico válido. Payload: " . $json_data;
            registrarErro($cliente_id_erro, null, __FILE__, $mensagem_erro, 'MP_WH_01', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
        }
    } else {
        // ERRO: Pagamento não está aprovado ou falta a referência externa.
        $mensalidade_id = null;
        if (isset($payment_details['external_reference'])) {
            $parts = explode('_', $payment_details['external_reference']);
            $mensalidade_id_temp = end($parts);
            if (is_numeric($mensalidade_id_temp)) {
                $mensalidade_id = $mensalidade_id_temp;
            }
        }
        $cliente_id_erro = $mensalidade_id ? obterClienteIdDaMensalidade($mensalidade_id, $conexao) : null;
        $status_pagamento = $payment_details['status'] ?? 'N/A';
        $mensagem_erro = "Webhook MP: Pagamento ID $payment_id não aprovado ou sem external_reference. Status: $status_pagamento. Resposta API: " . $response;
        registrarErro($cliente_id_erro, null, __FILE__, $mensagem_erro, 'MP_WH_02', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
    }
}

// resposta ao Mercado Pago com um código 200 (OK) para confirmar o recebimento.
http_response_code(200);
echo "Notificação recebida.";
exit();

?>