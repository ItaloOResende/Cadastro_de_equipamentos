<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Cadastro de Cliente</h1>
        <form action="#" method="post">
            <div class="campo">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" >
                <span class="obrigatorio">Campo Obrigatório</span>
            </div>

            <div class="campo">
                <label for="rg">RG:</label>
                <input type="text" id="rg" name="rg" required>
                <span class="obrigatorio">Campo Obrigatório</span>
            </div>

            <div class="campo">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required>
                <span class="obrigatorio">Campo Obrigatório</span>
            </div>

            <p class="aviso-obrigatorio">* Campo Obrigatório</p>
            <button type="submit">Exibir</button>
        </form>
    </div>

</body>
</html>