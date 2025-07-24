<?php
session_start();
include("../conexao.php");
include("../funcoes/geral.php");
header('Content-Type: application/json');

// Verifica se o ID foi passado na URL
if (!isset($_GET['id'])) {
    echo json_encode(['erro' => 'ID do usuario não fornecido.']);
    exit;
}

$id_usuario = $_GET['id'];

// busca o produto
$stmt = $conexao->prepare("SELECT nome, email, tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if ($usuario) {
    echo json_encode($usuario);
} else {
    echo json_encode(['erro' => 'Usuario não encontrado.']);
}