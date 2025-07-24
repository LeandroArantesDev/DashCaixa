<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
header('Content-Type: application/json');

// Verifica se o ID foi passado na URL
if (!isset($_GET['id'])) {
    echo json_encode(['erro' => 'ID da categoria não fornecido.']);
    exit;
}

$id_categoria = $_GET['id'];

// busca a categoria
$stmt = $conexao->prepare("SELECT nome FROM categorias WHERE id = ?");
$stmt->bind_param("i", $id_categoria);
$stmt->execute();
$resultado = $stmt->get_result();
$categoria = $resultado->fetch_assoc();
$stmt->close();

if ($categoria) {
    echo json_encode($categoria);
} else {
    echo json_encode(['erro' => 'Categoria não encontrada.']);
}