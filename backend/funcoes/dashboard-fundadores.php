<?php
function buscar_alerta_estoque_baixo()
{
    global $conexao;
    $stmt = $conexao->prepare("SELECT nome, estoque FROM produtos WHERE estoque < 5 AND cliente_id = ? AND status IN (0,1)");
    $stmt->bind_param("i", $_SESSION['cliente_id']);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $produtos = [];
    while ($row = $resultado->fetch_assoc()) {
        $produtos[] = $row;
    }

    return ($produtos);
}

?>