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
    echo "<tr><td colspan='6'>Erro de conexão: " . $conn->connect_error . "</td></tr>";
} else {

    // Define a consulta SQL base para buscar todos os equipamentos
    $sql = "SELECT * FROM equipamentos";
    $params = [];
    $types = "";

    // A lógica de filtro só será aplicada SE a requisição for do tipo GET
    // E o formulário de pesquisa foi enviado (ou seja, se o botão "Pesquisar" foi clicado)
    // Para isso, vamos assumir que o formulário de pesquisa tem um campo oculto ou o botão de submit tem um nome
    // A forma mais simples é verificar se o campo de pesquisa tem algum valor
    
    $search_query = $_GET['search-input'] ?? '';
    $filtro_empresa = $_GET['filtro_empresa'] ?? null;
    $filtro_tipo = $_GET['filtro_tipo'] ?? null;
    $filtro_status = $_GET['filtro_status'] ?? null;
    
    // Se algum dos campos de filtro/pesquisa foi preenchido, aplicamos os filtros
    if (!empty($search_query) || $filtro_empresa !== null || $filtro_tipo !== null || $filtro_status !== null) {
        $sql .= " WHERE 1=1";

        // Adicionar a condição de empresa se não for "ambos"
        if ($filtro_empresa !== 'ambos' && $filtro_empresa !== null) {
            $sql .= " AND empresa = ?";
            $params[] = $filtro_empresa;
            $types .= "s";
        }

        // Adicionar a condição de tipo de equipamento se não for "todos"
        if ($filtro_tipo !== 'todos' && $filtro_tipo !== null) {
            $sql .= " AND tipo_equipamento = ?";
            $params[] = $filtro_tipo;
            $types .= "s";
        }

        // Adicionar a condição de situação se não for nula ou "todas"
        if ($filtro_status !== 'todas' && $filtro_status !== null) {
            $sql .= " AND situacao = ?";
            $params[] = $filtro_status;
            $types .= "s";
        }

        // Adicionar a condição para o campo de pesquisa
        if (!empty($search_query)) {
            $sql .= " AND (nome_equipamento LIKE ? OR etiqueta_antiga LIKE ?)";
            $params[] = "%" . $search_query . "%";
            $params[] = "%" . $search_query . "%";
            $types .= "ss";
        }
    }


    // Prepara a consulta com Prepared Statements
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "<tr><td colspan='6'>Erro na preparação da consulta: " . $conn->error . "</td></tr>";
    } else {
        // Vincula os parâmetros se houver
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Executa a consulta
        $stmt->execute();
        $result = $stmt->get_result();

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
    }
}
$conn->close();
?>