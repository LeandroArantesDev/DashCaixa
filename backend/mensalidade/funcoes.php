<?php
session_start();
function marcarFaturaComoPaga($fatura_id): bool
{
    global $conexao;

    // Buscar fatura
    $stmt = $conexao->prepare("SELECT status, cliente_id, valor FROM mensalidades WHERE id = ?");
    $stmt->bind_param("i", $fatura_id);
    $stmt->execute();
    $fatura = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$fatura || $fatura['status'] == 0) {
        return false; // Já paga ou não encontrada
    }

    $cliente_id = $fatura['cliente_id'];
    $valor = $fatura['valor'];

    $conexao->begin_transaction();

    try {
        // Atualiza fatura atual como paga
        $stmt_update = $conexao->prepare("UPDATE mensalidades SET status = 0, data_pagamento = CURRENT_TIMESTAMP() WHERE id = ?");
        $stmt_update->bind_param("i", $fatura_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Cria próxima mensalidade
        $nova_data = (new DateTime())->modify('+1 month')->format('Y-m-d');
        $stmt_nova = $conexao->prepare("INSERT INTO mensalidades (cliente_id, valor, data_vencimento, status) VALUES (?, ?, ?, 1)");
        $stmt_nova->bind_param("ids", $cliente_id, $valor, $nova_data);
        $stmt_nova->execute();
        $stmt_nova->close();

        $conexao->commit();
        return true;
    } catch (Exception $e) {
        $conexao->rollback();
        registrarErro($cliente_id, $_SESSION["id"], 'marcarFaturaComoPaga', $e->getMessage(), 'TRANSACTION_FAIL', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
        return false;
    }
}
