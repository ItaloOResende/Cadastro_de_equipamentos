<?php
// Arquivo: gerarDocumento.php - Versão ATUALIZADA com UPDATE no BD e SALVAMENTO EM PASTA ESPECÍFICA

// 1. CONFIGURAÇÃO E AUTORIZAÇÃO GOOGLE
require_once 'vendor/autoload.php';
require_once 'google_auth.php'; 

// ID do SEU documento template
// -----------------------------------------------------------
$TEMPLATE_DOCUMENT_ID = '1QxsvfoAZmz_gntZ1kLWMwAZWzoN96uio2EJ2yb49VeU'; 
// ID da pasta de destino fornecida pelo usuário: 1TBvZq9fyK1z7NNbRP45askR_QX1IC2Nj
$TARGET_FOLDER_ID = '1TBvZq9fyK1z7NNbRP45askR_QX1IC2Nj';
// -----------------------------------------------------------


// Inicia as autorizações
$client = getGoogleClient();
$docsService = new Google\Service\Docs($client);
$driveService = new Google\Service\Drive($client); 

// -----------------------------------------------------
// 2. CAPTURA DOS DADOS DO JAVASCRIPT E CONEXÃO BD
// -----------------------------------------------------

$equipmentId = $_GET['id'] ?? null;
$nomePessoa = $_GET['nome_pessoa'] ?? null;
$action = $_GET['action'] ?? null;

// Validação básica dos parâmetros
if (!$equipmentId || !$nomePessoa || $action !== 'Empréstimo') {
    die("Erro: Parâmetros de empréstimo ausentes ou inválidos. ID: {$equipmentId}, Pessoa: {$nomePessoa}, Ação: {$action}");
}

// O nome real que será salvo na coluna 'situacao' do BD
$novaSituacaoBD = $nomePessoa;


// Conexão com o Banco de Dados
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "gvu"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão com o Banco de Dados: " . $conn->connect_error);
}

// -----------------------------------------------------
// 3. EXECUÇÃO DO UPDATE NO BANCO DE DADOS
// -----------------------------------------------------

$sql_update = "UPDATE equipamentos SET situacao = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);

// 's' para string (novaSituacaoBD), 'i' para integer (equipmentId)
$stmt_update->bind_param("si", $novaSituacaoBD, $equipmentId);
$stmt_update->execute();

if ($stmt_update->error) {
    // Se falhar o UPDATE, exibe o erro e encerra
    $conn->close();
    die("Erro ao atualizar a situação no BD: " . $stmt_update->error);
}

// -----------------------------------------------------
// 4. BUSCA DE DADOS DO EQUIPAMENTO PARA O DOCUMENTO
// -----------------------------------------------------

// ATENÇÃO: Verifique se os nomes das colunas aqui (nome_equipamento, tipo_equipamento, cpu, etc.) 
// correspondem exatamente aos nomes reais no seu banco de dados.

$sql_select = "
    SELECT 
        nome_equipamento, 
        empresa, 
        tipo_equipamento, 
        cpu, 
        ram, 
        armazenamento 
    FROM equipamentos 
    WHERE id = ?
";

$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $equipmentId); // 'i' para integer (equipmentId)
$stmt_select->execute();
$result = $stmt_select->get_result();

$equipamento = null;
if ($result->num_rows > 0) {
    $equipamento = $result->fetch_assoc();
    // Adiciona o nome da pessoa ao array para usar no Google Docs
    $equipamento['nome_responsavel'] = $nomePessoa;
}

$conn->close(); // Fecha a conexão após todas as operações

if (!$equipamento) {
    die("Nenhum equipamento encontrado com o ID: " . $equipmentId);
}

// -----------------------------------------------------
// 5. COPIAR TEMPLATE E SUBSTUIÇÃO DE DADOS (GOOLGE DOCS API)
// -----------------------------------------------------

$newDocTitle = "Termo de Compromisso - " . $nomePessoa . " - " . date('d-m-y');

// --- Passo 5.1: Copiar o template usando a API do Drive ---
// ALTERAÇÃO AQUI: Passamos a nova pasta ($TARGET_FOLDER_ID) na propriedade 'parents'
$copiedFile = $driveService->files->copy(
    $TEMPLATE_DOCUMENT_ID, 
    new Google\Service\Drive\DriveFile([
        'name' => $newDocTitle,
        // Define a pasta de destino para a criação do arquivo
        'parents' => [$TARGET_FOLDER_ID] 
    ])
);
$newDocId = $copiedFile->getId();


// --- Passo 5.2: Substituir os marcadores usando a API do Docs ---

$requests = [
    // 1. DADOS TÉCNICOS
    new Google\Service\Docs\Request(['replaceAllText' => [
        'containsText' => ['text' => '{{tipo_equipamento}}', 'matchCase' => true],
        'replaceText' => $equipamento['tipo_equipamento'],
    ]]),
    new Google\Service\Docs\Request(['replaceAllText' => [
        'containsText' => ['text' => '{{nome_equipamento}}', 'matchCase' => true],
        'replaceText' => $equipamento['nome_equipamento'],
    ]]),
    new Google\Service\Docs\Request(['replaceAllText' => [
        'containsText' => ['text' => '{{cpu}}', 'matchCase' => true],
        'replaceText' => $equipamento['cpu'],
    ]]),
    new Google\Service\Docs\Request(['replaceAllText' => [
        'containsText' => ['text' => '{{ram}}', 'matchCase' => true],
        'replaceText' => $equipamento['ram'],
    ]]),
    new Google\Service\Docs\Request(['replaceAllText' => [
        'containsText' => ['text' => '{{armazenamento}}', 'matchCase' => true],
        'replaceText' => $equipamento['armazenamento'],
    ]]),
    // 2. NOME DO RESPONSÁVEL (Usamos o nome capturado da URL)
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            // Presumindo que o seu marcador no template para o nome da pessoa seja {{situacao}}
            'containsText' => ['text' => '{{situacao}}', 'matchCase' => true], 
            'replaceText' => $equipamento['nome_responsavel'], // Usa o nome da pessoa, não o status do BD
        ]
    ]),
    // 3. DATA DE LIBERAÇÃO
    new Google\Service\Docs\Request([
        'replaceAllText' => [
            'containsText' => ['text' => '20/08/2025', 'matchCase' => true], // Altere para seu marcador de data se for diferente
            'replaceText' => date('d/m/Y'),
        ]
    ]),
];

// Executa todas as substituições no novo documento copiado
$batchUpdateRequest = new Google\Service\Docs\BatchUpdateDocumentRequest(['requests' => $requests]);
$docsService->documents->batchUpdate($newDocId, $batchUpdateRequest);


// -----------------------------------------------------
// 6. REDIRECIONAMENTO FINAL
// -----------------------------------------------------

$docLink = 'https://docs.google.com/document/d/' . $newDocId . '/edit';

// REDIRECIONA PARA index.php, INCLUINDO O LINK DO DOCUMENTO NA URL!
header('Location: index.php?status=success_emprestimo&nome_pessoa=' . urlencode($nomePessoa) . '&doc_link=' . urlencode($docLink));
exit;
?>