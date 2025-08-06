<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
header('Content-Type: application/json');

// Verifica se o ID foi passado na URL
if (!isset($_GET['id'])) {
    echo json_encode(['erro' => 'ID do produto não fornecido.']);
    exit;
}

$id = $_GET['id'];

// busca o produto
$stmt = $conexao->prepare("SELECT titulo, descricao, status, criado_em, concluido_em FROM roadmap WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$funcionalidade = $resultado->fetch_assoc();
$stmt->close();

if ($funcionalidade) {
    echo json_encode($funcionalidade);
} else {
    echo json_encode(['erro' => 'Produto não encontrado.']);
}
