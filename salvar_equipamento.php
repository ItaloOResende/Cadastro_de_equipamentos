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
    // Se a conexão falhar, exibe uma mensagem de erro na tabela
    echo "<tr><td colspan='6'>Erro de conexão com o banco de dados: " . $conn->connect_error . "</td></tr>";
} else {
    // Seleciona os dados da tabela
    $sql = "SELECT nome_equipamento, etiqueta_antiga, situacao FROM equipamentos";
    $result = $conn->query($sql);

    // Gera as linhas da tabela
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["nome_equipamento"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["etiqueta_antiga"]) . "</td>";
            echo "<td>-</td>"; // Usuário (fixo)
            echo "<td>-</td>"; // Setor (fixo)
            echo "<td>" . htmlspecialchars($row["situacao"]) . "</td>";
            echo "<td>
                <button class='status-button' data-action='verify' title='Verificar informações'>🔍</button>
                <button class='status-button' data-action='Estoque' title='Mover para Estoque'>📦</button>
                <button class='status-button' data-action='Empréstimo' title='Mover para Empréstimo'>🤝</button>
                <button class='status-button' data-action='Lixo Eletrônico' title='Mover para Lixo Eletrônico'>🗑️</button>
                <button class='status-button' data-action='Descarte' title='Mover para Descarte'>🔥</button>
            </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Nenhum equipamento encontrado.</td></tr>";
    }
    
// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitiza e obtém os dados do formulário
    $empresa = $conn->real_escape_string($_POST['filtro_empresa']); // AGORA CORRETO
    if ($empresa === 'outro') {
        $empresa = $_POST['empresa_outro_texto']; // Pega o valor do novo campo
    }
    $tipo_equipamento = $conn->real_escape_string($_POST['tipo_equipamento']); // AGORA CORRETO
    if ($tipo_equipamento === 'outro') {
        $tipo_equipamento = $_POST['equipamento_outro_texto']; // Pega o valor do novo campo
    }
    $nome_equipamento = $conn->real_escape_string($_POST['nome_equipamento']); // AGORA CORRETO
    $etiqueta_antiga = $conn->real_escape_string($_POST['etiqueta_antiga']);
    
    // CORREÇÃO: Verifica se 'quantidade' está vazio e define 1 como padrão
    $quantidade = isset($_POST['quantidade']) && !empty($_POST['quantidade']) ? $conn->real_escape_string($_POST['quantidade']) : 1;

    $marca_modelo = $conn->real_escape_string($_POST['marca_modelo']);
    $cpu = $conn->real_escape_string($_POST['cpu']);
    $ram = $conn->real_escape_string($_POST['ram']);
    $armazenamento = $conn->real_escape_string($_POST['armazenamento']);
    $entradas_video = $conn->real_escape_string($_POST['entradas_video']);
    $observacao = $conn->real_escape_string($_POST['observacao']);
    $data_entrada = $conn->real_escape_string($_POST['data_entrada']); // AGORA CORRETO
    
    // Constrói a consulta SQL para inserir os dados
    $sql = "INSERT INTO equipamentos (
        empresa, tipo_equipamento, nome_equipamento, etiqueta_antiga, quantidade,
        marca_modelo, cpu, ram, armazenamento, entradas_video, observacao,
        data_entrada
    ) VALUES (
        '$empresa', '$tipo_equipamento', '$nome_equipamento', '$etiqueta_antiga', '$quantidade',
        '$marca_modelo', '$cpu', '$ram', '$armazenamento', '$entradas_video', '$observacao',
        '$data_entrada'
    )";

    // Executa a consulta e verifica se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        // Redireciona o usuário para uma página de sucesso
        header("Location: sucesso.html");
        exit();
    } else {
        echo "Erro ao salvar o equipamento: " . $conn->error;
    }
}
}

// Fecha a conexão com o banco de dados
$conn->close();

?>