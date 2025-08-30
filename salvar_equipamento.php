<?php
// Configurações do Banco de Dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gvu";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão e encerra o script se houver erro
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitiza e obtém os dados do formulário
    $empresa = $_POST['filtro_empresa'] ?? '';
    if ($empresa === 'outro') {
        $empresa = $_POST['empresa_outro_texto'] ?? '';
    }
    
    // Pegando o valor do campo 'tipo_equipamento'
    $tipo_equipamento = $_POST['tipo_equipamento'] ?? '';
    if ($tipo_equipamento === 'outro') {
        $tipo_equipamento = $_POST['equipamento_outro_texto'] ?? '';
    }
    
    $nome_equipamento = $_POST['nome_equipamento'] ?? '';
    $etiqueta_antiga = $_POST['etiqueta_antiga'] ?? '';
    $quantidade = isset($_POST['quantidade']) && !empty($_POST['quantidade']) ? $_POST['quantidade'] : 1;
    $marca_modelo = $_POST['marca_modelo'] ?? '';
    $cpu = $_POST['cpu'] ?? '';
    $ram = $_POST['ram'] ?? '';
    $armazenamento = $_POST['armazenamento'] ?? '';
    $entradas_video = $_POST['entradas_video'] ?? '';
    $observacao = $_POST['observacao'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';
    
    // Constrói a consulta SQL para inserir os dados
    $sql = "INSERT INTO equipamentos (
        empresa, tipo_equipamento, nome_equipamento, etiqueta_antiga, quantidade,
        marca_modelo, cpu, ram, armazenamento, entradas_video, observacao,
        data_entrada, situacao
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Estoque'
    )";

    // Prepara e executa a consulta com prepared statements para evitar SQL Injection
    $stmt = $conn->prepare($sql);
    // CORREÇÃO: 'ram' é um valor numérico, então o tipo do parâmetro foi alterado de 's' para 'i'.
    $stmt->bind_param("ssssiissssss", 
        $empresa, $tipo_equipamento, $nome_equipamento, $etiqueta_antiga, $quantidade, 
        $marca_modelo, $cpu, $ram, $armazenamento, $entradas_video, $observacao, 
        $data_entrada);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        die("Erro ao salvar o equipamento: " . $stmt->error);
    }
    $stmt->close();

} else {
    die("Acesso inválido.");
}

$conn->close();
?>
