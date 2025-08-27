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
    die("Erro de conexão: " . $conn->connect_error);
}

// Lógica para processar a atualização do status (requisição POST)
// Esta parte do código é executada quando um dos botões de status é clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $situacao = $_POST['situacao'] ?? null;

    if ($id !== null && $situacao !== null) {
        // Prepara e executa a consulta para atualizar a situação no banco de dados
        $sql_update = "UPDATE equipamentos SET situacao = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("si", $situacao, $id);
            $stmt_update->execute();
            $stmt_update->close();
            // Retorna uma resposta JSON de sucesso para o JavaScript
            echo json_encode(['success' => true]);
            exit; // Interrompe o script para não carregar o resto da página HTML
        }
    }
    // Retorna uma resposta de erro para o JavaScript
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

// Lógica para carregar a página (requisição GET)
// Esta parte do código é executada quando a página é acessada pela primeira vez ou via pesquisa
$search_query = $_GET['search-input'] ?? '';
$filtro_empresa = $_GET['filtro_empresa'] ?? 'ambos';
$filtro_tipo = $_GET['filtro_tipo'] ?? 'todos';
$filtro_status = $_GET['filtro_status'] ?? 'todas';

// Define a consulta SQL base para buscar os equipamentos
// Corrigido: a consulta agora seleciona apenas as colunas que existem na sua tabela
$sql = "SELECT id, nome_equipamento, etiqueta_antiga, quantidade, situacao FROM equipamentos WHERE 1=1";
$params = [];
$types = "";

// Lógica para aplicar os filtros da pesquisa
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
                        <td><div class="radio-item"><input type="radio" id="filtro-estoque" name="filtro_status" value="Estoque" <?php echo ($filtro_status == 'Estoque') ? 'checked' : ''; ?>><label for="filtro-estoque">Estoque</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-emprestimo" name="filtro_status" value="Empréstimo" <?php echo ($filtro_status == 'Empréstimo') ? 'checked' : ''; ?>><label for="filtro-emprestimo">Empréstimo</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-lixo" name="filtro_status" value="Lixo Eletrônico" <?php echo ($filtro_status == 'Lixo Eletrônico') ? 'checked' : ''; ?>><label for="filtro-lixo">Lixo eletrônico</label></div></td>
                        <td><div class="radio-item"><input type="radio" id="filtro-descartar" name="filtro_status" value="Descarte" <?php echo ($filtro_status == 'Descarte') ? 'checked' : ''; ?>><label for="filtro-descartar">Descarte</label></div></td>
                        <td class="empty-cell"></td>
                    </tr>
                    <tr>
                        <td class="label-cell"><label for="search-input"><b>Localizar:</b></label></td>
                        <td colspan="5">
                            <div class="actions-cell">
                                <input type="text" id="search-input" name="search-input" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <button class="btn" type="submit">Pesquisar</button>
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
                        <th>Quantidade</th>
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
                            echo "<td>" . htmlspecialchars($row["quantidade"] ?? '-') . "</td>";
                            echo "<td>" . htmlspecialchars($row["situacao"]) . "</td>";
<<<<<<< HEAD
echo "<td>
    <button class='status-button' data-action='verify' data-id='" . htmlspecialchars($row['id']) . "' title='Verificar informações'>🔍</button>
    <button class='status-button' data-action='Estoque' data-id='" . htmlspecialchars($row['id']) . "' title='Mover para Estoque'>📦</button>
    <button class='status-button' data-action='Empréstimo' data-id='" . htmlspecialchars($row['id']) . "' title='Mover para Empréstimo'>🤝</button>
    <button class='status-button' data-action='Lixo Eletrônico' data-id='" . htmlspecialchars($row['id']) . "' title='Mover para Lixo Eletrônico'>🗑️</button>
    <button class='status-button' data-action='Descarte' data-id='" . htmlspecialchars($row['id']) . "' title='Mover para Descarte'>🔥</button>
</td>";
=======
                            echo "<td>
                                <button class='status-button' data-action='verify' title='Verificar informações'>🔍</button>
                                <button class='status-button' data-action='Estoque' data-id='{$row['id']}' title='Mover para Estoque'>📦</button>
                                <button class='status-button' data-action='Empréstimo' data-id='{$row['id']}' title='Mover para Empréstimo'>🤝</button>
                                <button class='status-button' data-action='Lixo Eletrônico' data-id='{$row['id']}' title='Mover para Lixo Eletrônico'>🗑️</button>
                                <button class='status-button' data-action='Descarte' data-id='{$row['id']}' title='Mover para Descarte'>🔥</button>
                            </td>";
>>>>>>> 7d430a2bdc09651c06807b1a538567168408e9ca
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum equipamento encontrado.</td></tr>"; // Corrigido para 5 colunas
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
