<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");

$fatura_id = $_GET['$fatura_id'];

// verificando se a fatura veio corretamente
if (!isset($fatura_id) || $fatura_id == NULL) {
    $_SESSION['resposta'] = "Erro ao processar pagamento da fatura!";
    header("Location: ../../pages/mensalidade");
    exit();
}