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
    die("Erro de conexão: " . $conn->connect_error);
}

// Inicializa variáveis para o formulário e a consulta
// Usa o operador de coalescência nula (??) para definir valores padrão
$search_query = $_GET['search-input'] ?? '';
$filtro_empresa = $_GET['filtro_empresa'] ?? 'ambos';
$filtro_tipo = $_GET['filtro_tipo'] ?? 'todos';
$filtro_status = $_GET['filtro_status'] ?? 'todas';

// Define a consulta SQL base para buscar todos os equipamentos
$sql = "SELECT * FROM equipamentos WHERE 1=1";
$params = [];
$types = "";

// Lógica para aplicar os filtros e construir a consulta
if ($filtro_empresa !== 'ambos') {
    $sql .= " AND empresa = ?";
    $params[] = $filtro_empresa;
    $types .= "s";
}

if ($filtro_tipo !== 'todos') {
    $sql .= " AND tipo_equipamento = ?";
    $params[] = $filtro_tipo;
    $types .= "s";
}

if ($filtro_status !== 'todas') {
    $sql .= " AND situacao = ?";
    $params[] = $filtro_status;
    $types .= "s";
}

if (!empty($search_query)) {
    $sql .= " AND (nome_equipamento LIKE ? OR etiqueta_antiga LIKE ?)";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $types .= "ss";
}

// Prepara e executa a consulta com Prepared Statements
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro na preparação da consulta: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque - Grupo Vitória da União</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Grupo Vitória da União (GVU)</h1>
        </header>

        <!-- Formulário com método GET para enviar os dados de pesquisa -->
        <form method="GET" action="index.php">
            <div class="control-panel">
                <table class="filter-table">
                    <tr>
                        <td class="label-cell"><label><b>Empresa:</b></label></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-ambos" name="filtro_empresa" value="ambos" <?php echo ($filtro_empresa == 'ambos') ? 'checked' : ''; ?>><label for="empresa-ambos">Todas</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-gvu" name="filtro_empresa" value="gvu" <?php echo ($filtro_empresa == 'gvu') ? 'checked' : ''; ?>><label for="empresa-gvu">GVU</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-cook" name="filtro_empresa" value="cook" <?php echo ($filtro_empresa == 'cook') ? 'checked' : ''; ?>><label for="empresa-cook">COOK</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-urba" name="filtro_empresa" value="urba" <?php echo ($filtro_empresa == 'urba') ? 'checked' : ''; ?>><label for="empresa-urba">URBA</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="empresa-outro" name="filtro_empresa" value="outro" <?php echo ($filtro_empresa == 'outro') ? 'checked' : ''; ?>><label for="empresa-outro">Outro</label></div></td>
                    </tr>
                    <tr>
                        <td class="label-cell"><label><b>Equipamento:</b></label></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-todos" name="filtro_tipo" value="todos" <?php echo ($filtro_tipo == 'todos') ? 'checked' : ''; ?>><label for="tipo-todos">Todos</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-desktop" name="filtro_tipo" value="desktop" <?php echo ($filtro_tipo == 'desktop') ? 'checked' : ''; ?>><label for="tipo-desktop">Desktop</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-notebook" name="filtro_tipo" value="notebook" <?php echo ($filtro_tipo == 'notebook') ? 'checked' : ''; ?>><label for="tipo-notebook">Notebook</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-monitor" name="filtro_tipo" value="monitor" <?php echo ($filtro_tipo == 'monitor') ? 'checked' : ''; ?>><label for="tipo-monitor">Monitor</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="tipo-outros" name="filtro_tipo" value="outros" <?php echo ($filtro_tipo == 'outros') ? 'checked' : ''; ?>><label for="tipo-outros">Outros</label></div></td>
                    </tr>
                    <tr>
                        <td class="label-cell"><label><b>Situação:</b></label></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-todas" name="filtro_status" value="todas" <?php echo ($filtro_status == 'todas') ? 'checked' : ''; ?>><label for="filtro-todas">Todas</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-estoque" name="filtro_status" value="estoque" <?php echo ($filtro_status == 'estoque') ? 'checked' : ''; ?>><label for="filtro-estoque">Estoque</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-emprestimo" name="filtro_status" value="emprestimo" <?php echo ($filtro_status == 'emprestimo') ? 'checked' : ''; ?>><label for="filtro-emprestimo">Empréstimo</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-lixo" name="filtro_status" value="lixo" <?php echo ($filtro_status == 'lixo') ? 'checked' : ''; ?>><label for="filtro-lixo">Lixo eletrônico</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-descartar" name="filtro_status" value="descartar" <?php echo ($filtro_status == 'descartar') ? 'checked' : ''; ?>><label for="filtro-descartar">Descarte</label></div></td>
                        <td class="empty-cell"></td>
                    </tr>
                    <tr>
                        <td class="label-cell"><label for="search-input"><b>Localizar:</b></label></td>
                        <td colspan="5">
                            <div class="actions-cell">
                                <input type="text" id="search-input" name="search-input" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <!-- Botão de Pesquisar: type="submit" para enviar o formulário -->
                                <button class="btn" type="submit">Pesquisar</button>
                                <!-- Botão de Cadastrar: type="button" para evitar o envio do formulário, e onclick para redirecionar -->
                                <button class="btn btn-primary" type="button" onclick="window.location.href='cadastrar.html'">Cadastrar Equipamento</button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        
        <main>
            <table class="main-data-table">
                <thead>
                    <tr>
                        <th>Equipamento</th>
                        <th>Antigo</th>
                        <th>Usuário</th>
                        <th>Setor</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Gera as linhas da tabela
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["nome_equipamento"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["etiqueta_antiga"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["usuario"] ?? '-') . "</td>";
                            echo "<td>" . htmlspecialchars($row["setor"] ?? '-') . "</td>";
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
                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
