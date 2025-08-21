<?php
// Configurações do Banco de Dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gvu";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
    
// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se os dados do formulário foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitiza e obtém os dados do formulário
    $empresa = ($_POST['filtro_empresa'] === 'outro') ? $_POST['empresa_outro_texto'] : $_POST['filtro_empresa'];
    $tipo_equipamento = ($_POST['tipo_equipamento'] === 'outros') ? $_POST['equipamento_outro_texto'] : $_POST['tipo_equipamento'];
    $nome_equipamento = $_POST['nome_equipamento'] ?? null;
    $etiqueta_antiga = $_POST['etiqueta_antiga'] ?? null;
    $quantidade = isset($_POST['quantidade']) && !empty($_POST['quantidade']) ? $_POST['quantidade'] : 1;
    $marca_modelo = $_POST['marca_modelo'] ?? null;
    $cpu = $_POST['cpu'] ?? null;
    $ram = $_POST['ram'] ?? null;
    $armazenamento = $_POST['armazenamento'] ?? null;
    $entradas_video = $_POST['entradas_video'] ?? null;
    $observacao = $_POST['observacao'] ?? null;
    $data_entrada = $_POST['data_entrada'] ?? date("Y-m-d");

    // Prepara a consulta SQL com placeholders (?)
    $sql = "INSERT INTO equipamentos (
        empresa, tipo_equipamento, nome_equipamento, etiqueta_antiga, quantidade,
        marca_modelo, cpu, ram, armazenamento, entradas_video, observacao,
        data_entrada
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepara o statement
    $stmt = $conn->prepare($sql);

    // Verifica se a preparação falhou
    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    // Vincula os parâmetros aos placeholders
    $stmt->bind_param("ssssisssssss",
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
        $data_entrada
    );

    // Executa a consulta
    if ($stmt->execute()) {
        header("Location: sucesso.html");
        exit();
    } else {
        echo "Erro ao salvar o equipamento: " . $stmt->error;
    }

    // Fecha o statement
    $stmt->close();
}

// Fecha a conexão com o banco de dados
$conn->close();

?>