<?php
// Arquivo: gerarDocumento.php

// 1. CARREGAMENTO E AUTENTICAÇÃO
// Inclua o autoload do Composer e a lógica de autenticação
require_once 'vendor/autoload.php';
require_once 'google_auth.php'; // Seu script com a função getGoogleClient()

// Inicia a autenticação OAuth (Redireciona para o Google se não estiver logado)
$client = getGoogleClient();

// Se chegamos aqui, o cliente está autenticado!
$docsService = new Google\Service\Docs($client);

// -----------------------------------------------------
// 2. BUSCA DE DADOS DO BANCO DE DADOS
// -----------------------------------------------------

// Configurações do Banco de Dados (Use suas credenciais do index.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gvu";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão com o Banco de Dados: " . $conn->connect_error);
}

// Consulta simples para buscar todos os equipamentos para o relatório
$sql_select = "SELECT nome_equipamento, etiqueta_antiga, situacao, empresa FROM equipamentos ORDER BY empresa, nome_equipamento";
$result = $conn->query($sql_select);

$equipamentos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $equipamentos[] = $row;
    }
}
$conn->close();

// -----------------------------------------------------
// 3. CRIAÇÃO E PREENCHIMENTO DO DOCUMENTO GOOGLE DOCS
// -----------------------------------------------------

$documentTitle = 'Relatório de Inventário GVU - ' . date('Y-m-d H:i:s');

// Cria um documento novo e obtém o ID
$newDoc = $docsService->documents->create(new Google\Service\Docs\Document([
    'title' => $documentTitle
]));
$documentId = $newDoc->getDocumentId();

$requests = []; // Array para armazenar as requisições de modificação

// --- Inserir o Título Principal ---
$requests[] = new Google\Service\Docs\Request([
    'insertText' => [
        'location' => ['index' => 1], // Início do documento
        'text' => $documentTitle . "\n\n",
    ]
]);
// Opcional: Formatar o título
$requests[] = new Google\Service\Docs\Request([
    'updateTextStyle' => [
        'range' => [
            'startIndex' => 1,
            'endIndex' => strlen($documentTitle) + 1, // +1 para pegar a quebra de linha
        ],
        'textStyle' => [
            'bold' => true,
            'fontSize' => ['magnitude' => 18, 'unit' => 'PT'],
        ],
        'fields' => 'bold,fontSize',
    ]
]);

// --- Inserir a Lista de Equipamentos ---
$listContent = "Total de Equipamentos Encontrados: " . count($equipamentos) . "\n\n";

foreach ($equipamentos as $eq) {
    $listContent .= 
        " - Empresa: " . $eq['empresa'] . 
        " | Nome: " . $eq['nome_equipamento'] . 
        " | Situação: " . $eq['situacao'] . 
        " | Etiqueta Antiga: " . $eq['etiqueta_antiga'] . 
        "\n";
}

$requests[] = new Google\Service\Docs\Request([
    'insertText' => [
        'location' => ['index' => 1 + strlen($documentTitle) + 2], // Após o título
        'text' => $listContent,
    ]
]);

// 4. Executa todas as requisições de modificação em lote
$batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest([
    'requests' => $requests
]);
$docsService->documents->batchUpdate($documentId, $batchUpdateRequest);


// -----------------------------------------------------
// 5. REDIRECIONAMENTO FINAL
// -----------------------------------------------------

// Redireciona o usuário para o documento que acabou de ser criado
header('Location: https://docs.google.com/document/d/' . $documentId . '/edit');
exit;
?>