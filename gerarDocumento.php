<?php
// ARQUIVO: gerarDocumento.php - Teste Mínimo de Conexão

// 1. CARREGAMENTO E AUTENTICAÇÃO
require_once 'vendor/autoload.php';
require_once 'google_auth.php'; // Seu script de autenticação

// Inicia a autenticação OAuth. Se for a primeira vez, redireciona para o Google.
$client = getGoogleClient();

// Se chegamos aqui, o cliente está autenticado!
$docsService = new Google\Service\Docs($client);

// -----------------------------------------------------
// 2. CRIAÇÃO DO DOCUMENTO VAZIO
// -----------------------------------------------------

$documentTitle = 'TESTE DE CONEXÃO BEM-SUCEDIDO - ' . date('Y-m-d H:i:s');

try {
    // Cria um documento novo
    $newDoc = $docsService->documents->create(new Google\Service\Docs\Document([
        'title' => $documentTitle
    ]));
    $documentId = $newDoc->getDocumentId();

    // -----------------------------------------------------
    // 3. REDIRECIONAMENTO FINAL
    // -----------------------------------------------------
    
    // Redireciona o usuário para o documento que acabou de ser criado
    header('Location: https://docs.google.com/document/d/' . $documentId . '/edit');
    exit;
    
} catch (Google\Service\Exception $e) {
    // Se a criação falhar (o que não deve acontecer se a autenticação funcionou), mostra o erro.
    echo "<h1>FALHA NA CRIAÇÃO DO DOCUMENTO</h1>";
    echo "<p>Verifique se o escopo `drive.file` está ativo e sua conta de teste está autorizada.</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    exit;
}

?>