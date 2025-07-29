<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

// coleta informações do ambiente
$cliente_id = $_SESSION['cliente_id'] ?? null;
$rota_atual = pegarRotaUsuario();
$ip_usuario = pegarIpUsuario();
$navegador_usuario = pegarNavegadorUsuario();

// validando o input
if (empty($_GET['fatura_id'])) {
    registrarErro($cliente_id, $rota_atual, 'ID da fatura não recebido na URL.', 'INPUT_INVALID', $ip_usuario, $navegador_usuario);
    $_SESSION['resposta'] = "Erro: Identificador da fatura não foi encontrado.";
    header("Location: " . BASE_URL . "pages/mensalidade");
    exit();
}

$fatura_paga_id = (int)$_GET['fatura_id'];

// verificar dados da fatura
$stmt_busca = $conexao->prepare("SELECT valor, status FROM mensalidades WHERE id = ? AND cliente_id = ?");
$stmt_busca->bind_param("ii", $fatura_paga_id, $cliente_id);
$stmt_busca->execute();
$resultado = $stmt_busca->get_result()->fetch_assoc();
$stmt_busca->close();

if (!$resultado) {
    registrarErro($cliente_id, $rota_atual, "Tentativa de processar fatura inexistente ou de outro cliente. ID: $fatura_paga_id", 'AUTH_VIOLATION', $ip_usuario, $navegador_usuario);
    $_SESSION['resposta'] = "Fatura não encontrada ou não pertence a você.";
    header("Location: " . BASE_URL . "pages/mensalidade");
    exit();
}

if ($resultado['status'] == 0) {
    $_SESSION['resposta'] = "Esta fatura já consta como paga em nosso sistema.";
    header("Location: " . BASE_URL . "pages/mensalidade");
    exit();
}

$valor_mensalidade = $resultado['valor'];
$conexao->begin_transaction();

try {
    // atualiza a variavel do cliente para pago
    $_SESSION['mensalidade'] = 0;

    // atualizar a fatura atual para "Pago" (status 0)
    $stmt_update = $conexao->prepare("UPDATE mensalidades SET status = 0, data_pagamento = CURRENT_TIMESTAMP() WHERE id = ?");
    $stmt_update->bind_param("i", $fatura_paga_id);
    if (!$stmt_update->execute()) {
        throw new Exception("Falha ao ATUALIZAR fatura paga. Erro: " . $stmt_update->error, 500);
    }
    $stmt_update->close();

    // criar a próxima mensalidade como "Pendente" (status 1)
    $data_vencimento = new DateTime();
    $data_vencimento->modify('+1 month');
    $proxima_data_formatada = $data_vencimento->format('Y-m-d');
    $status_pendente = 1;

    $stmt_insert = $conexao->prepare("INSERT INTO mensalidades (cliente_id, valor, data_vencimento, status) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("idsi", $cliente_id, $valor_mensalidade, $proxima_data_formatada, $status_pendente);
    if (!$stmt_insert->execute()) {
        throw new Exception("Falha ao INSERIR próxima fatura. Erro: " . $stmt_insert->error, 501);
    }
    $stmt_insert->close();

    // confirma as alterações no banco
    $conexao->commit();
    $_SESSION['resposta'] = "Pagamento confirmado!";

} catch (Exception $e) {
    // Se qualquer uma das etapas falhou, desfaz TUDO.
    $conexao->rollback();

    // Registra o erro usando sua função e os dados coletados no início
    registrarErro($cliente_id, $rota_atual, $e->getMessage(), 'TRANSACTION_FAIL_' . $e->getCode(), $ip_usuario, $navegador_usuario);

    // Define uma mensagem de erro genérica para o usuário
    $_SESSION['resposta'] = "Ocorreu um erro crítico ao processar seu pagamento. Nossa equipe já foi notificada.";
}

header("Location: " . BASE_URL . "pages/mensalidade");
exit();
?>