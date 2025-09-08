<?php
// CRUCIAL: Inicia a sessão para poder usar a variável $_SESSION
session_start();

// Configurações do Banco de Dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gvu";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão e, se houver erro, armazena a mensagem na sessão e redireciona
if ($conn->connect_error) {
    $_SESSION['error'] = "Conexão falhou: " . $conn->connect_error;
    header("Location: cadastrar.php");
    exit();
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitiza e obtém os dados do formulário
    $empresa = $_POST['filtro_empresa'] ?? '';
    $tipo_equipamento = $_POST['tipo_equipamento'] ?? '';

    // Array para armazenar erros
    $errors = [];

    // --- Validação principal no servidor (PHP) ---
    if ($empresa === '') {
        $errors[] = "O campo 'Empresa' é obrigatório.";
    }
    if ($tipo_equipamento === '') {
        $errors[] = "O campo 'Tipo de Equipamento' é obrigatório.";
    }

    // Lógica para campos "Outro"
    if ($empresa === 'outro') {
        $empresa = $_POST['empresa_outro_texto'] ?? '';
        if (empty(trim($empresa))) {
            $errors[] = "O campo 'Outra Empresa' não pode ficar vazio.";
        }
    }

    if ($tipo_equipamento === 'outro') {
        $tipo_equipamento = $_POST['equipamento_outro_texto'] ?? '';
        if (empty(trim($tipo_equipamento))) {
            $errors[] = "O campo 'Outro Equipamento' não pode ficar vazio.";
        }
    }

    // Verifica se outros campos importantes estão vazios
    if (empty(trim($_POST['nome_equipamento'] ?? ''))) {
        $errors[] = "O campo 'Nome do Equipamento' é obrigatório.";
    }
    if (empty(trim($_POST['data_entrada'] ?? ''))) {
        $errors[] = "O campo 'Data de Entrada' é obrigatório.";
    }
    
    // NOVO: Validação para o campo de quantidade
    $quantidade = isset($_POST['quantidade']) && !empty($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
    if ($quantidade < 1) {
        $errors[] = "A quantidade deve ser no mínimo 1.";
    }

    // Se houver erros, armazena na sessão e redireciona
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: cadastrar.php");
        exit();
    }
    
    // --- FIM DA LÓGICA DE VALIDAÇÃO ---

    $nome_equipamento = $_POST['nome_equipamento'] ?? '';
    $etiqueta_antiga = $_POST['etiqueta_antiga'] ?? '';
    $marca_modelo = $_POST['marca_modelo'] ?? '';
    $cpu = $_POST['cpu'] ?? '';
    $ram = isset($_POST['ram']) && !empty($_POST['ram']) ? (int)$_POST['ram'] : 0;
    $armazenamento = $_POST['armazenamento'] ?? '';
    $entradas_video = $_POST['entradas_video'] ?? '';
    $observacao = $_POST['observacao'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';
    
    $situacao = 'Estoque';

    // A coluna "quantidade" foi removida da consulta
    $sql = "INSERT INTO equipamentos (
        empresa, tipo_equipamento, nome_equipamento, etiqueta_antiga,
        marca_modelo, cpu, ram, armazenamento, entradas_video, observacao,
        data_entrada, situacao
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Erro na preparação da query: " . $conn->error;
        header("Location: cadastrar.php");
        exit();
    }
    
    // ATUALIZADO: A string de tipos agora tem 12 caracteres e 12 variáveis
    $stmt->bind_param("ssssssssssss", 
        $empresa, 
        $tipo_equipamento, 
        $nome_equipamento, 
        $etiqueta_antiga, 
        $marca_modelo, 
        $cpu, 
        $ram, 
        $armazenamento, 
        $entradas_video, 
        $observacao, 
        $data_entrada, 
        $situacao
    );

    $success = true;
    for ($i = 0; $i < $quantidade; $i++) {
        if (!$stmt->execute()) {
            $success = false;
            break; // Sai do loop se um erro ocorrer
        }
    }

    if ($success) {
        $_SESSION['message'] = "Equipameto(s) salvo(s) com sucesso" . $quantidade . " equipamento(s)!";
        header("Location: cadastrar.php");
        exit();
    } else {
        $_SESSION['error'] = "Erro ao salvar os equipamentos: " . $stmt->error;
        header("Location: cadastrar.php");
        exit();
    }
    $stmt->close();

} else {
    $_SESSION['error'] = "Acesso inválido.";
    header("Location: cadastrar.php");
    exit();
}

$conn->close();
?>