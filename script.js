document.addEventListener('DOMContentLoaded', () => {

    // ------------------------------------------------------------------
    // NOVO BLOCO: Lógica para exibir alerta de sucesso após o redirecionamento do PHP
    // Esta parte verifica os parâmetros da URL para exibir a mensagem.
    // ------------------------------------------------------------------
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    const nomePessoaParam = params.get('nome_pessoa');

    if (status === 'success_emprestimo' && nomePessoaParam) {
        // Exibe o alerta com a mensagem de sucesso que você solicitou
        alert(`Empréstimo realizado com sucesso para ${nomePessoaParam}!`);
        
        // Limpa os parâmetros da URL para evitar que o alerta apareça em um recarregamento acidental
        // A URL volta a ser index.php sem os parâmetros de status.
        history.replaceState(null, '', window.location.pathname);
    }
    // ------------------------------------------------------------------


    // --- Lógica para os botões da tabela (index.php) ---
    const tableBody = document.querySelector('.main-data-table tbody');
    if (tableBody) {
        tableBody.addEventListener('click', (event) => {
            const button = event.target.closest('.status-button[data-action]');
            
            if (button) {
                const action = button.dataset.action;
                const equipmentId = button.dataset.id;
                
                if (action === 'verify') {
                    window.location.href = `editar.php?id=${equipmentId}`;
                } else if (action === 'Empréstimo') {
                    const nomePessoa = prompt("Para quem você está emprestando este equipamento?");
                    
                    if (nomePessoa === null || nomePessoa.trim() === "") {
                        alert("Empréstimo cancelado ou nome não fornecido.");
                        return;
                    }

                    // Mensagem pré-ação para feedback imediato
                    alert("Atualizando Status e Gerando Termo de Compromisso...");

                    // FLUXO CORRIGIDO: Redirecionamento Direto. 
                    // O PHP (gerarDocumento.php) fará o UPDATE, a Geração do Documento e o Redirecionamento de volta para index.php.
                    window.location.href = `gerarDocumento.php?id=${equipmentId}&nome_pessoa=${encodeURIComponent(nomePessoa.trim())}&action=${action}`;

                } else {
                    // Comportamento para os outros botões (Estoque, Lixo, Descarte) - Mantendo o FETCH original
                    if (confirm(`Tem certeza de que deseja alterar a situação para "${action}"?`)) {
                        fetch('index.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${equipmentId}&situacao=${action}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // O alerta é genérico, pois a geração de documento não é tratada aqui.
                                alert('Situação atualizada com sucesso!'); 
                                window.location.reload();
                            } else {
                                alert('Erro ao atualizar a situação: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Ocorreu um erro na requisição.');
                        });
                    }
                }
            }
        });
    }

    // --- O restante do código (Lógica de preenchimento e inicialização) permanece inalterado ---
    const nomeEquipamentoInput = document.getElementById('equipamento-nome');
    const radiosEmpresa = document.querySelectorAll('input[name="filtro_empresa"]');
    const radiosTipo = document.querySelectorAll('input[name="tipo_equipamento"]');

    const cpuInput = document.getElementById('cpu');
    const ramInput = document.getElementById('ram');
    const armazenamentoInput = document.getElementById('armazenamento');
    const videoInput = document.getElementById('entradas-video');
    const quantidadeInput = document.getElementById('quantidade');

    const inputEmpresaOutro = document.getElementById('empresa-outro-texto');
    const inputEquipOutro = document.getElementById('equipamento-outro-texto');

    const updateEquipmentName = () => {
        const empresaSelecionada = document.querySelector('input[name="filtro_empresa"]:checked');
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');
        let nomeFinal = '';

        if (empresaSelecionada && empresaSelecionada.value !== 'outro') {
            let nomeBase = empresaSelecionada.value.toUpperCase();
            let sufixo = '';

            if (tipoSelecionado && tipoSelecionado.value !== 'outro') {
                if (tipoSelecionado.value === 'monitor') {
                    sufixo = 'MON';
                } else if (tipoSelecionado.value === 'notebook') {
                    sufixo = 'NOT';
                }
            }
            nomeFinal = nomeBase + (sufixo ? `${sufixo}` : '');
        }
        if (nomeEquipamentoInput) {
            nomeEquipamentoInput.value = nomeFinal;
        }
    };
    
    const updateFieldAvailability = () => {
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');
        if (!tipoSelecionado) return;
        const tipoValue = tipoSelecionado.value;

        // Apenas o monitor desabilita esses campos.
        const isMonitor = tipoValue === 'monitor';
        if (cpuInput) cpuInput.disabled = isMonitor;
        if (ramInput) ramInput.disabled = isMonitor;
        if (armazenamentoInput) armazenamentoInput.disabled = isMonitor;

        const isOtherEquipment = tipoValue === 'outro';
        if (quantidadeInput) quantidadeInput.disabled = !isOtherEquipment;

        // Lógica para limpar os campos quando desabilitados.
        if (isMonitor) {
            if (cpuInput) cpuInput.value = '';
            if (ramInput) ramInput.value = '';
            if (armazenamentoInput) armazenamentoInput.value = '';
        }
        if (!isOtherEquipment) {
            if (quantidadeInput) quantidadeInput.value = '1';
        }
    };

    const updateOutroFields = () => {
        const empresaSelecionada = document.querySelector('input[name="filtro_empresa"]:checked');
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');

        const isOutroEmpresa = empresaSelecionada && empresaSelecionada.value === 'outro';
        if (inputEmpresaOutro) {
            inputEmpresaOutro.disabled = !isOutroEmpresa;
            if (!isOutroEmpresa) inputEmpresaOutro.value = '';
        }

        const isOutroEquip = tipoSelecionado && tipoSelecionado.value === 'outro';
        if (inputEquipOutro) {
            inputEquipOutro.disabled = !isOutroEquip;
            if (!isOutroEquip) inputEquipOutro.value = '';
        }
    };

    // Re-selecionando os radios (melhor prática para garantir que a atualização de evento está correta)
    document.querySelectorAll('input[name="filtro_empresa"]').forEach(radio => {
        radio.addEventListener('change', () => {
            updateEquipmentName();
            updateOutroFields();
        });
    });
    document.querySelectorAll('input[name="tipo_equipamento"]').forEach(radio => {
        radio.addEventListener('change', () => {
            updateEquipmentName();
            updateFieldAvailability();
            updateOutroFields();
        });
    });

    const formEquipamento = document.querySelector('.cadastro-form');
    if (formEquipamento) {
        // Lógica de preenchimento via URL (para edição de dados)
        const params = new URLSearchParams(window.location.search);
        const equipamento = params.get('equipamento');
        const antigo = params.get('antigo');
        const etiquetaAntigaInput = document.getElementById('etiqueta-antiga');
        const nomeEquipamentoInput = document.getElementById('equipamento-nome');
        
        if (equipamento) {
            if (nomeEquipamentoInput) nomeEquipamentoInput.value = equipamento;
        }
        if (antigo) {
            if (etiquetaAntigaInput) etiquetaAntigaInput.value = antigo;
        }

        // Lógica para preencher a data de entrada com a data atual
        const dataEntradaInput = document.getElementById('entrada');
        if (dataEntradaInput) {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            dataEntradaInput.value = `${year}-${month}-${day}`;
        }

        // Chamadas de inicialização
        updateEquipmentName();
        updateFieldAvailability();
        updateOutroFields();
    }
    
    // Funcionalidades de Redirecionamento para o botão de cadastro
    const cadastroButton = document.querySelector('.btn-primary[data-action="cadastro"]');
    if (cadastroButton) {
        cadastroButton.addEventListener('click', () => {
            window.location.href = 'cadastrar.html';
        });
    }
});