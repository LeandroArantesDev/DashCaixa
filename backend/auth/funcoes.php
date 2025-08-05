<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/DashCaixa/');
    } else {
        define('BASE_URL', '/');
    }
}

include(__DIR__ . '/../conexao.php');

function validarNome($nome)
{
    // Remove espaços no início/fim e valida com regex
    $nome = trim($nome);

    // Regex: letras (com acento), espaço, mínimo 3 e máximo 50 caracteres
    if (!preg_match('/^[\p{L} ]{3,50}$/u', $nome)) {
        return false;
    }

    return true;
}

function validarEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}


function validarSenha($senha)
{
    // Pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha)) {
        return false;
    }

    return true;
}

// função de verificações da mensalidade
function verificarEMarcarMensalidadeVencida($cliente_id) {
    global $conexao;

    $status_mensalidade = 0;

    // buscar a próxima mensalidade pendente ou vencida
    $stmt = $conexao->prepare("SELECT id, status, data_vencimento FROM mensalidades WHERE cliente_id = ? AND status IN (1,2) ORDER BY data_vencimento ASC LIMIT 1");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $stmt->bind_result($id_mensalidade, $status_mensalidade_db, $data_vencimento);

    if ($stmt->fetch()) {
        if ($data_vencimento < date("Y-m-d")) {
            $stmt->close();
            $stmt = $conexao->prepare("UPDATE mensalidades SET status = 2 WHERE id = ?");
            $stmt->bind_param("i", $id_mensalidade);
            $stmt->execute();
            $status_mensalidade_db = 2;
        }
        $status_mensalidade = $status_mensalidade_db;
    }

    $stmt->close();

    // atualiza o status do cliente
    $stmt = $conexao->prepare("UPDATE clientes SET status_mensalidade = ? WHERE id = ?");
    $stmt->bind_param("ii", $status_mensalidade, $cliente_id);
    $stmt->execute();
    $stmt->close();

    return $status_mensalidade;
}
