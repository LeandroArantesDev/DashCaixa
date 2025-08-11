<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
include("funcoes.php");

// coleta informações do ambiente
$cliente_id = $_SESSION['cliente_id'] ?? null;
$rota_atual = pegarRotaUsuario();
$ip_usuario = pegarIpUsuario();
$navegador_usuario = pegarNavegadorUsuario();

// validando o input
if (empty($_GET['mensalidade_id'])) {
    registrarErro($cliente_id, $_SESSION["id"], $rota_atual, 'ID da mensalidade não recebido na URL.', 'INPUT_INVALID', $ip_usuario, $navegador_usuario);
    $_SESSION['resposta'] = "Erro: Identificador da fatura não foi encontrado.";
    header("Location: " . BASE_URL . "pages/mensalidade");
    exit();
}

$mensalidade_paga_id = (int)$_GET['mensalidade_id'];

// checar se a fatura é do cliente
$stmt = $conexao->prepare("SELECT cliente_id FROM mensalidades WHERE id = ?");
$stmt->bind_param("i", $mensalidade_paga_id);
$stmt->execute();
$mensalidade = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$mensalidade || $mensalidade['cliente_id'] != $cliente_id) {
    registrarErro($cliente_id, $_SESSION["cliente_id"], $rota_atual, "Fatura inválida ou de outro cliente.", 'AUTH_VIOLATION', $ip_usuario, $navegador_usuario);
    $_SESSION['resposta'] = "Fatura não encontrada ou não pertence a você.";
    header("Location: " . BASE_URL . "pages/mensalidade");
    exit();
}

// Processa o pagamento
if (marcarFaturaComoPaga($mensalidade_paga_id)) {
    $_SESSION['mensalidade'] = 0;
    $_SESSION['resposta'] = "Pagamento confirmado!";
} else {
    $_SESSION['resposta'] = "Esta fatura já consta como paga em nosso sistema.";
}

header("Location: " . BASE_URL . "pages/mensalidade");
exit();
