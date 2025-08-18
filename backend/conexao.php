<?php

// Função para carregar variáveis do .env
function carregarEnv($caminho)
{
    if (!file_exists($caminho)) {
        throw new Exception("Arquivo .env não encontrado em: " . $caminho);
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        // Ignorar comentários
        if (strpos(trim($linha), '#') === 0) {
            continue;
        }

        // Separar chave e valor
        list($chave, $valor) = explode('=', $linha, 2);

        $chave = trim($chave);
        $valor = trim($valor, " \"'"); // já remove espaços e aspas

        // Salvar no ambiente
        putenv("$chave=$valor");
        $_ENV[$chave] = $valor;
    }
}

// Carrega o .env
carregarEnv(__DIR__ . '/../senhas.env');

// Detecta o host atual
$hostAtual = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Configuração de conexão
if ($hostAtual == 'localhost') {
    // Ambiente local
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'dash_caixa';
} else {
    // Ambiente de produção
    $host = $_ENV['DB_HOST'] ?? '';
    $username = $_ENV['DB_USER'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    $dbname = $_ENV['DB_NAME'] ?? '';
}

// Conexão MySQL
$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Erro na conexão! " . $conexao->connect_error);
}