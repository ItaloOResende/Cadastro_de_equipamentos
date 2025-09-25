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

// -------------------------------------------------------------------
// Lógica para processar o formulário quando o botão "Salvar" for clicado
// -------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $equipamento_id = $_POST['id'];

    $empresa = $_POST['filtro_empresa'] ?? null;
    $tipo_equipamento = $_POST['filtro_tipo'] ?? null;
    $nome_equipamento = $_POST['equipamento_nome'] ?? null;
    $etiqueta_antiga = $_POST['etiqueta_antiga'] ?? null;
    $marca_modelo = $_POST['marca_modelo'] ?? null;
    $cpu = $_POST['cpu'] ?? null;
    $ram = $_POST['ram'] ?? null;
    $armazenamento = $_POST['armazenamento'] ?? null;
    $entradas_video = $_POST['entradas_video'] ?? null;
    $data_entrada = $_POST['data_entrada'] ?? null;
    $observacao = $_POST['observacao'] ?? null;
    
    $sql = "UPDATE equipamentos SET
        empresa = ?, tipo_equipamento = ?, nome_equipamento = ?, etiqueta_antiga = ?, 
        marca_modelo = ?, cpu = ?, ram = ?, armazenamento = ?,
        entradas_video = ?, data_entrada = ?, observacao = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }
    
    $stmt->bind_param("sssssssssssi",
        $empresa, $tipo_equipamento, $nome_equipamento, $etiqueta_antiga,
        $marca_modelo, $cpu, $ram, $armazenamento, $entradas_video, $data_entrada,
        $observacao, $equipamento_id
    );

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao salvar as informações: " . $stmt->error;
    }

    $stmt->close();
} else {
    // -------------------------------------------------------------------
    // Lógica para exibir o formulário (requisição GET)
    // -------------------------------------------------------------------
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $equipamento_id = $_GET['id'];
        
        $sql = "SELECT * FROM equipamentos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $equipamento_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $equipamento = $result->fetch_assoc();

        if (!$equipamento) {
            die("Equipamento não encontrado.");
        }
        $stmt->close();
    } else {
        die("ID do equipamento não especificado.");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipamento - GVU</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>

    <div class="container">
        <header class="main-header">
            <h1><a href="index.php" class="header-link">Grupo Vitória da União (GVU)</a></h1>
            <h1>Editar Informações do Equipamento</h1>
        </header>
        
        <form class="cadastro-form" action="editar.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($equipamento['id']); ?>">
            
            <div class="opcoes-cadastro">
                <table class="filter-table">
                    <tr>
                        <td class="label-cell"><label><b>Empresa:</b></label></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-gvu" name="filtro_empresa" value="GVU" <?php echo ($equipamento['empresa'] == 'gvu') ? 'checked' : ''; ?>><label for="empresa-gvu">GVU</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-cook" name="filtro_empresa" value="COOK" <?php echo ($equipamento['empresa'] == 'cook') ? 'checked' : ''; ?>><label for="empresa-cook">COOK</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-urba" name="filtro_empresa" value="URBA" <?php echo ($equipamento['empresa'] == 'urba') ? 'checked' : ''; ?>><label for="empresa-urba">URBA</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-outro" name="filtro_empresa" value="outro" <?php echo (!in_array($equipamento['empresa'], ['gvu', 'cook', 'urba'])) ? 'checked' : ''; ?>><label for="empresa-outro">Outro</label></div></td>
                    </tr>
                    <tr>
                        <td class="label-cell"><label><b>Equipamento:</b></label></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-desktop" name="filtro_tipo" value="desktop" <?php echo ($equipamento['tipo_equipamento'] == 'desktop') ? 'checked' : ''; ?>><label for="tipo-desktop">Desktop</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-notebook" name="filtro_tipo" value="notebook" <?php echo ($equipamento['tipo_equipamento'] == 'notebook') ? 'checked' : ''; ?>><label for="tipo-notebook">Notebook</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-monitor" name="filtro_tipo" value="monitor" <?php echo ($equipamento['tipo_equipamento'] == 'monitor') ? 'checked' : ''; ?>><label for="tipo-monitor">Monitor</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-outros" name="filtro_tipo" value="outros" <?php echo (!in_array($equipamento['tipo_equipamento'], ['desktop', 'notebook', 'monitor'])) ? 'checked' : ''; ?>><label for="tipo-outros">Outro</label></div></td>
                    </tr>
                </table>
            </div>

            <div class="form-fields-grid">
                <div class="field-group">
                    <label for="equipamento-nome">Nome do Equipamento:</label>
                    <input type="text" id="equipamento-nome" name="equipamento_nome" value="<?php echo htmlspecialchars($equipamento['nome_equipamento']); ?>" ...>
                </div>
                <div class="field-group">
                    <label for="etiqueta-antiga">Etiqueta Antiga:</label>
                    <input type="text" id="etiqueta-antiga" name="etiqueta_antiga" value="<?php echo htmlspecialchars($equipamento['etiqueta_antiga']); ?>">
                </div>
                <div class="field-group">
                    <label for="marca-modelo">Marca/Modelo:</label>
                    <input type="text" id="marca-modelo" name="marca_modelo" value="<?php echo htmlspecialchars($equipamento['marca_modelo']); ?>" placeholder="Ex: Dell Optiplex 3080...">
                </div>
                <div class="field-group">
                    <label for="cpu">CPU:</label>
                    <input type="text" id="cpu" name="cpu" value="<?php echo htmlspecialchars($equipamento['cpu']); ?>" placeholder="Apenas para Computadores/Notebooks">
                </div>
                <div class="field-group">
                    <label for="ram">RAM:</label>
                    <input type="text" id="ram" name="ram" value="<?php echo htmlspecialchars($equipamento['ram']); ?>" placeholder="Apenas para Computadores/Notebooks">
                </div>
                <div class="field-group">
                    <label for="armazenamento">Armazenamento:</label>
                    <input type="text" id="armazenamento" name="armazenamento" value="<?php echo htmlspecialchars($equipamento['armazenamento']); ?>" placeholder="Apenas para Computadores/Notebooks">
                </div>
                <div class="field-group">
                    <label for="entradas-video">Entradas de Vídeo:</label>
                    <input type="text" id="entradas-video" name="entradas_video" value="<?php echo htmlspecialchars($equipamento['entradas_video']); ?>" placeholder="Ex: HDMI, VGA, DisplayPort...">
                </div>
                <div class="field-group">
                    <label for="data_entrada">Data de Entrega:</label>
                    <input type="date" id="data_entrada" name="data_entrada" value="<?php echo htmlspecialchars($equipamento['data_entrada']); ?>">
                </div>
                <div class="field-group full-width">
                    <label for="observacao">Observação:</label>
                    <textarea id="observacao" name="observacao" rows="4"><?php echo htmlspecialchars($equipamento['observacao']); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="index.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
    
</body>
</html>
<?php
$conn->close();
?>