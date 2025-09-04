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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $situacao = $_POST['situacao'] ?? null;

    if ($id !== null && $situacao !== null) {
        $sql_update = "UPDATE equipamentos SET situacao = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("si", $situacao, $id);
            $stmt_update->execute();
            $stmt_update->close();
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

// Lógica para carregar a página (requisição GET)
$search_query = $_GET['search-input'] ?? '';
$filtro_empresa = $_GET['filtro_empresa'] ?? 'ambos';
$filtro_tipo = $_GET['filtro_tipo'] ?? 'todos';
$filtro_status = $_GET['filtro_status'] ?? 'todas';

// Define a consulta SQL base para buscar os equipamentos
$sql = "SELECT id, nome_equipamento, etiqueta_antiga, quantidade, situacao, empresa FROM equipamentos WHERE 1=1";
$params = [];
$types = "";

// Lógica para aplicar os filtros da pesquisa de empresa
if ($filtro_empresa !== 'ambos') {
    if ($filtro_empresa === 'outro') {
        $sql .= " AND empresa NOT IN ('gvu', 'cook', 'urba')";
    } else {
        $sql .= " AND empresa = ?";
        $params[] = $filtro_empresa;
        $types .= "s";
    }
}

// Lógica para aplicar os filtros da pesquisa de tipo de equipamento
if ($filtro_tipo !== 'todos') {
    if ($filtro_tipo === 'outros') {
        $sql .= " AND tipo_equipamento NOT IN ('desktop', 'notebook', 'monitor')";
    } else {
        $sql .= " AND tipo_equipamento = ?";
        $params[] = $filtro_tipo;
        $types .= "s";
    }
}

// Lógica para aplicar os filtros da pesquisa de situação
if ($filtro_status !== 'todas') {
    $sql .= " AND situacao = ?";
    $params[] = $filtro_status;
    $types .= "s";
}

if (!empty($search_query)) {
    // AQUI ESTÁ A MUDANÇA: O filtro de pesquisa agora inclui todos os campos relevantes.
    $sql .= " AND (nome_equipamento LIKE ? OR etiqueta_antiga LIKE ? OR marca_modelo LIKE ? OR cpu LIKE ? OR ram LIKE ? OR armazenamento LIKE ? OR entradas_video LIKE ? OR observacao LIKE ?)";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $types .= "ssssssss";
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
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Grupo Vitória da União (GVU)</h1>
        </header>

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
                        <td><div class="radio-item"><input type="radio" id="filtro-descarte" name="filtro_status" value="Descarte" <?php echo ($filtro_status == 'Descarte') ? 'checked' : ''; ?>><label for="filtro-descarte">Descarte</label></div></td>
                    </tr>
                    <tr>
                        <td class="label-cell"><label for="search-input"><b>Localizar:</b></label></td>
                        <td colspan="5">
                            <div class="actions-cell">
                                <input type="text" id="search-input" name="search-input" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <button class="btn" type="submit">Pesquisar</button>
                                <button class="btn btn-primary" type="button" onclick="window.location.href='cadastrar.php'">Cadastrar Equipamento</button>
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
                        <th>Empresa</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nome_equipamento"]); ?></td>
                                <td><?php echo htmlspecialchars($row["etiqueta_antiga"]); ?></td>
                                <td><?php echo htmlspecialchars($row["empresa"]); ?></td>
                                <td><?php echo htmlspecialchars($row["situacao"]); ?></td>
                                <td>
                                    <button class='status-button' data-action='verify' data-id='<?php echo htmlspecialchars($row['id']); ?>' title='Verificar informações'>🔍</button>
                                    <button class='status-button' data-action='Estoque' data-id='<?php echo htmlspecialchars($row['id']); ?>' title='Mover para Estoque'>📦</button>
                                    <button class='status-button' data-action='Empréstimo' data-id='<?php echo htmlspecialchars($row['id']); ?>' title='Mover para Empréstimo'>🤝</button>
                                    <button class='status-button' data-action='Lixo Eletrônico' data-id='<?php echo htmlspecialchars($row['id']); ?>' title='Mover para Lixo Eletrônico'>🗑️</button>
                                    <button class='status-button' data-action='Descarte' data-id='<?php echo htmlspecialchars($row['id']); ?>' title='Mover para Descarte'>🔥</button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum equipamento encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>