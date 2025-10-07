<?php
// Certifique-se de que esta linha está no início do seu script PHP!
require_once 'vendor/autoload.php';

// Define os escopos globalmente para serem usados na função e no bloco de código.
// Note: o define() aqui está correto, mas se SCOPES for uma variável, declare-a aqui.
if (!defined('SCOPES')) {
    define('SCOPES', ['https://www.googleapis.com/auth/drive.file']);
}
if (!defined('TOKEN_PATH')) {
    define('TOKEN_PATH', 'token.json');
}

/**
 * Retorna o URI de redirecionamento dinâmico do script que está sendo executado.
 * Isso garante que o Google sempre retorne para o script que iniciou o OAuth.
 */
function getCurrentUri() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    // Retorna a URL completa: http://localhost/caminho/do/script.php?parametros
    return $protocol . "://" . $host . $uri;
}

function getGoogleClient() {
    $credentials_path = 'credentials.json';

    // 3. Cria e configura o cliente Google
    $client = new Google\Client();
    $client->setApplicationName('Cadastro de Equipamentos');
    $client->setAuthConfig($credentials_path); 
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setScopes(SCOPES);
    
    // CORREÇÃO ESSENCIAL: Define o Redirect URI para o URL atual do script
    $client->setRedirectUri(getCurrentUri()); 

    // --- Lógica de Token ---

    // 4. Verifica se já existe um token de acesso válido
    if (file_exists(TOKEN_PATH)) {
        $accessToken = json_decode(file_get_contents(TOKEN_PATH), true);
        $client->setAccessToken($accessToken);
    }

    // 5. Se o token expirou, tenta renovar ou força um novo login
    if ($client->isAccessTokenExpired()) {
        
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Inicia o processo de login (redireciona o usuário para o Google)
            $authUrl = $client->createAuthUrl();
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit;
        }

        // Salva o token novo/atualizado
        if (!file_exists(dirname(TOKEN_PATH))) {
            mkdir(dirname(TOKEN_PATH), 0700, true);
        }
        file_put_contents(TOKEN_PATH, json_encode($client->getAccessToken()));
    }
    
    // O cliente está pronto
    return $client;
}


// BLOCO DE CÓDIGO CORRIGIDO: Processa o retorno do Google após o login
if (isset($_GET['code'])) {
    
    // Obtém a URL que recebeu o código (incluindo o código)
    $current_uri_with_code = getCurrentUri(); 

    // 1. Cria a URL limpa (sem o '?code=...') para o redirecionamento final
    $redirect_uri_clean = preg_replace('/(\?|&)?code=[^&]+/', '', $current_uri_with_code);

    $client = new Google\Client();
    $client->setAuthConfig('credentials.json'); 
    $client->setScopes(SCOPES);
    
    // CORREÇÃO ESSENCIAL: Define o Redirect URI para a URL limpa
    $client->setRedirectUri($redirect_uri_clean); 
    
    // 2. Troca o código (code) por um Token de acesso real
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($accessToken);

    // Salva o token
    if (!file_exists(dirname(TOKEN_PATH))) {
        mkdir(dirname(TOKEN_PATH), 0700, true);
    }
    file_put_contents(TOKEN_PATH, json_encode($client->getAccessToken()));

    // 3. Redireciona para a URL limpa. O script de geração recomeça a execução.
    header('Location: ' . filter_var($redirect_uri_clean, FILTER_SANITIZE_URL));
    exit;
}
?>