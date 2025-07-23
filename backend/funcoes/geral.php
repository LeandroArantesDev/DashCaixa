<?php
function formatarPreco($numero)
{
    return 'R$ ' . number_format($numero, 2, ',', '.');
}

function gerarCSRF()
{
    $_SESSION["csrf"] = (isset($_SESSION["csrf"])) ? $_SESSION["csrf"] : hash('sha256', random_bytes(32));

    return ($_SESSION["csrf"]);
}

function validarCSRF($csrf)
{
    if (!isset($_SESSION["csrf"])) {
        return (false);
    }
    if ($_SESSION["csrf"] !== $csrf) {
        return false;
    }
    if (!hash_equals($_SESSION["csrf"], $csrf)) {
        return false;
    }

    return true;
}

function registrarErro($usuario_id, $rota, $mensagem, $codigo, $ip, $navegador)
{
    global $conexao;
    $stmt = $conexao->prepare("INSERT INTO erros (usuario_id, rota, mensagem, codigo, ip, navegador) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("isssss", $usuario_id, $rota, $mensagem, $codigo, $ip, $navegador);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function pegarIpUsuario()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'IP DESCONHECIDO';
    }

    // Em alguns casos, HTTP_X_FORWARDED_FOR pode conter múltiplos IPs. O primeiro é o real.
    $ips = explode(',', $ipaddress);
    return trim($ips[0]);
}

function pegarNavegadorUsuario()
{
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $userAgent =  $_SERVER['HTTP_USER_AGENT'];

        // Valores padrão, caso algo não seja encontrado
        $os = 'Desconhecido';
        $arch = 'Desconhecida';
        $browser = 'Desconhecido';

        // 1. IDENTIFICAR O SISTEMA OPERACIONAL (OS)
        if (preg_match('/windows nt 10/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/windows nt 6.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/windows nt 6.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/linux/i', $userAgent)) {
            // Verifica se não é Android, que também se identifica como Linux
            if (preg_match('/android/i', $userAgent)) {
                $os = 'Android';
            } else {
                $os = 'Linux';
            }
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        }

        // 2. IDENTIFICAR A ARQUITETURA
        if (preg_match('/wow64|win64|x64|x86_64/i', $userAgent)) {
            $arch = 'x64';
        } elseif (preg_match('/win32|x86|i686|i386/i', $userAgent)) {
            $arch = 'x86'; // x32
        } elseif (preg_match('/aarch64|arm/i', $userAgent) || in_array($os, ['Android', 'iOS'])) {
            // Dispositivos móveis são majoritariamente ARM
            $arch = 'ARM';
        }

        // 3. IDENTIFICAR O NAVEGADOR (a ordem é importante!)
        if (preg_match('/edg/i', $userAgent)) {
            // Edge precisa ser verificado antes de Chrome
            $browser = 'Edge';
        } elseif (preg_match('/opr/i', $userAgent) || preg_match('/opera/i', $userAgent)) {
            // Opera precisa ser verificado antes de Chrome
            $browser = 'Opera';
        } elseif (preg_match('/chrome/i', $userAgent)) {
            // Chrome precisa ser verificado antes de Safari
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/msie|trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        // 4. JUNTAR TUDO E RETORNAR
        return "$os, $arch, $browser";
    } else {
        return 'N/A';
    }
}

function pegarRotaUsuario()
{
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    // Pega o nome do domínio (ex: www.seusite.com.br)
    $dominio = $_SERVER['HTTP_HOST'];

    // Pega o restante da URL (ex: /pagina/produto.php?id=10)
    $caminho = $_SERVER['REQUEST_URI'];

    $urlAtual = $protocolo . $dominio . $caminho;

    return $urlAtual;
}
