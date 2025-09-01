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
    if ($empresa === 'outro') {
        $empresa = $_POST['empresa_outro_texto'] ?? '';
    }
    
    $tipo_equipamento = $_POST['tipo_equipamento'] ?? '';
    if ($tipo_equipamento === 'outro') {
        $tipo_equipamento = $_POST['equipamento_outro_texto'] ?? '';
    }
    
    $nome_equipamento = $_POST['nome_equipamento'] ?? '';
    $etiqueta_antiga = $_POST['etiqueta_antiga'] ?? '';
    $quantidade = isset($_POST['quantidade']) && !empty($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
    $marca_modelo = $_POST['marca_modelo'] ?? '';
    $cpu = $_POST['cpu'] ?? '';
    $ram = isset($_POST['ram']) && !empty($_POST['ram']) ? (int)$_POST['ram'] : 0;
    $armazenamento = $_POST['armazenamento'] ?? '';
    $entradas_video = $_POST['entradas_video'] ?? '';
    $observacao = $_POST['observacao'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';
    
    // VARIÁVEL ADICIONAL: Definindo a situação do equipamento
    $situacao = 'Estoque';

    // Constrói a consulta SQL para inserir os dados
    $sql = "INSERT INTO equipamentos (
        empresa, tipo_equipamento, nome_equipamento, etiqueta_antiga, quantidade,
        marca_modelo, cpu, ram, armazenamento, entradas_video, observacao,
        data_entrada, situacao
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    // Prepara a consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Erro na preparação da query: " . $conn->error;
        header("Location: cadastrar.php");
        exit();
    }
    
    // CORREÇÃO: A string de tipos agora tem 13 caracteres e 13 variáveis,
    // um para cada placeholder '?' na query.
    // O tipo para $situacao ('Estoque') foi adicionado como 's'.
    $stmt->bind_param("ssssissssssss", 
        $empresa, 
        $tipo_equipamento, 
        $nome_equipamento, 
        $etiqueta_antiga, 
        $quantidade, 
        $marca_modelo, 
        $cpu, 
        $ram, 
        $armazenamento, 
        $entradas_video, 
        $observacao, 
        $data_entrada, 
        $situacao
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Equipamento salvo com sucesso!";
        header("Location: cadastrar.php");
        exit();
    } else {
        $_SESSION['error'] = "Erro ao salvar o equipamento: " . $stmt->error;
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
