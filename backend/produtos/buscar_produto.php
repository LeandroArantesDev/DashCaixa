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

$id_produto = $_GET['id'];

// busca o produto
$stmt = $conexao->prepare("SELECT nome, categoria_id, preco, estoque FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$resultado = $stmt->get_result();
$produto = $resultado->fetch_assoc();
$stmt->close();

if ($produto) {
    echo json_encode($produto);
} else {
    echo json_encode(['erro' => 'Produto não encontrado.']);
}