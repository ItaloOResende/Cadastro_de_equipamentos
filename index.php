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

                <div class="control-panel">
            <table class="filter-table">
                <tr>
                    <td class="label-cell"><label><b>Empresa:</b></label></td>
                    <td><div class="radio-item"><input type="radio" id="empresa-ambos" name="filtro_empresa" value="ambos" checked><label for="empresa-ambos">Todas</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="empresa-gvu" name="filtro_empresa" value="gvu"><label for="empresa-gvu">GVU</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="empresa-cook" name="filtro_empresa" value="cook"><label for="empresa-cook">COOK</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="empresa-urba" name="filtro_empresa" value="urba"><label for="empresa-urba">URBA</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="empresa-outro" name="filtro_empresa" value="outro"><label for="empresa-outro">Outro</label></div></td>
                </tr>
                <tr>
                    <td class="label-cell"><label><b>Equipamento:</b></label></td>
                    <td><div class="radio-item"><input type="radio" id="tipo-todos" name="filtro_tipo" value="todos" checked><label for="tipo-todos">Todos</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="tipo-maquina" name="filtro_tipo" value="maquina"><label for="tipo-maquina">Desktop</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="tipo-notebook" name="filtro_tipo" value="maquina"><label for="tipo-notebook">Notebook</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="tipo-monitor" name="filtro_tipo" value="monitor"><label for="tipo-monitor">Monitor</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="tipo-outros" name="filtro_tipo" value="outros"><label for="tipo-outros">Outros</label></div></td>
                </tr>
                <tr>
                    <td class="label-cell"><label><b>Situação:</b></label></td>
                    <td><div class="radio-item"><input type="radio" id="filtro-estoque" name="filtro_status" value="estoque" checked><label for="filtro-estoque">Estoque</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="filtro-emprestimo" name="filtro_status" value="emprestimo"><label for="filtro-emprestimo">Empréstimo</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="filtro-lixo" name="filtro_status" value="lixo"><label for="filtro-lixo">Lixo eletrônico</label></div></td>
                    <td><div class="radio-item"><input type="radio" id="filtro-descartar" name="filtro_status" value="descartar"><label for="filtro-descartar">Descarte</label></div></td>
                    <td class="empty-cell"></td>
                </tr>
                <tr>
                    <td class="label-cell"><label for="search-input"><b>Localizar:</b></label></td>
                    <td colspan="5">
                        <div class="actions-cell">
                             <input type="text" id="search-input" placeholder="Pesquisar...">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div class="actions-cell">
                             <div class="actions-group">
                                <button class="btn">Pesquisar</button>
                                <button class="btn btn-primary" data-action="cadastro">Cadastrar Equipamento</button>

                             </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
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
                        // Inclui o arquivo que contém a lógica de listagem de equipamentos
                        include 'listar_equipamentos.php';
                    ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>