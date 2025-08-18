<?php

// A única coisa que este script faz é escrever a data e hora em um arquivo de log.
$timestamp = date("Y-m-d H:i:s");
$message = "[$timestamp] O Webhook foi recebido e o script PHP executou com sucesso.";

file_put_contents('teste_webhook.log', $message . PHP_EOL, FILE_APPEND);

// Responde para o Mercado Pago que está tudo OK.
http_response_code(200);
echo "OK";

exit();

?>