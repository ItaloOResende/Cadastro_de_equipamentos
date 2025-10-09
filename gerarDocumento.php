<?php
// Arquivo: gerarDocumento.php - Versão para TEMPLATE Termo de Compromisso

// 1. CONFIGURAÇÃO
require_once 'vendor/autoload.php';
require_once 'google_auth.php'; 

// ID do SEU documento template (https://docs.google.com/document/d/1PjhL3t2kfHAU7GtazeUFSpnkyAZ9yHBy/edit)
// -----------------------------------------------------------
$TEMPLATE_DOCUMENT_ID = '1PjhL3t2kfHAU7GtazeUFSpnkyAZ9yHBy'; 
// -----------------------------------------------------------


// Inicia as autorizações
$client = getGoogleClient();
$docsService = new Google\Service\Docs($client);
// Precisamos do serviço Drive para COPIAR o template
$driveService = new Google\Service\Drive($client); 

// -----------------------------------------------------
// 2. BUSCA DE DADOS DO BANCO DE DADOS
// -----------------------------------------------------

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "gvu"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão com o Banco de Dados: " . $conn->connect_error);
}

// ATENÇÃO: Se seus campos no banco de dados para cpu, ram, e armazenamento forem diferentes, 
// você deve ajustar o SELECT abaixo. Presumi que o termo é gerado para um único item.
$sql_select = "
    SELECT 
        nome_equipamento, 
        situacao, 
        empresa, 
        'Notebook' AS tipo_equipamento,  
        'Intel Core i7' AS cpu,          
        '16GB' AS ram,                   
        'SSD 512GB' AS armazenamento     
    FROM equipamentos 
    LIMIT 1
";
$result = $conn->query($sql_select);

$equipamento = null;
if ($result->num_rows > 0) {
    $equipamento = $result->fetch_assoc();
}
$conn->close();

if (!$equipamento) {
    die("Nenhum equipamento encontrado no banco de dados para gerar o termo.");
}

// -----------------------------------------------------
// 3. COPIAR TEMPLATE E SUBSTUIÇÃO DE DADOS
// -----------------------------------------------------

$newDocTitle = "Termo de Compromisso - " . $equipamento['nome_equipamento'] . " - " . date('Y-m-d');

// --- Passo 3.1: Copiar o template usando a API do Drive ---
$copiedFile = $driveService->files->copy(
    $TEMPLATE_DOCUMENT_ID, 
    new Google\Service\Drive\DriveFile(['name' => $newDocTitle])
);
$newDocId = $copiedFile->getId();


// --- Passo 3.2: Substituir os marcadores usando a API do Docs ---

$requests = [
    // 1. DADOS TÉCNICOS
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '{{tipo_equipamento}}', 'matchCase' => true],
            'replaceText' => $equipamento['tipo_equipamento'],
        ]
    ]),
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '{{nome_equipamento}}', 'matchCase' => true],
            'replaceText' => $equipamento['nome_equipamento'],
        ]
    ]),
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '{{cpu}}', 'matchCase' => true],
            'replaceText' => $equipamento['cpu'],
        ]
    ]),
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '{{ram}}', 'matchCase' => true],
            'replaceText' => $equipamento['ram'],
        ]
    ]),
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '{{armazenamento}}', 'matchCase' => true],
            'replaceText' => $equipamento['armazenamento'],
        ]
    ]),
    // 2. NOME DO RESPONSÁVEL (Situação é o nome no seu BD)
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            // Este marcador aparece duas vezes no seu template
            'containsText' => ['text' => '{{situacao}}', 'matchCase' => true], 
            'replaceText' => $equipamento['situacao'],
        ]
    ]),
    // 3. DATA DE LIBERAÇÃO
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '20/08/2025', 'matchCase' => true], 
            'replaceText' => date('d/m/Y'),
        ]
    ]),
];

// Executa todas as substituições no novo documento copiado
$batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest(['requests' => $requests]);
$docsService->documents->batchUpdate($newDocId, $batchUpdateRequest);


// -----------------------------------------------------
// 4. REDIRECIONAMENTO FINAL
// -----------------------------------------------------

header('Location: https://docs.google.com/document/d/' . $newDocId . '/edit');
exit;
?>