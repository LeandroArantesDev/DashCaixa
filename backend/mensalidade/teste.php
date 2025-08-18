<?php
// Testar escrita em arquivo
file_put_contents("log.txt", date("Y-m-d H:i:s") . " - Teste escrita\n", FILE_APPEND);

// Testar se consegue capturar JSON
$rawInput = file_get_contents("php://input");
file_put_contents("log.txt", date("Y-m-d H:i:s") . " - Recebido: " . $rawInput . "\n", FILE_APPEND);

// Testar $_POST também
file_put_contents("log.txt", date("Y-m-d H:i:s") . " - POST: " . json_encode($_POST) . "\n", FILE_APPEND);

echo "OK";
