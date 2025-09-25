document.addEventListener('DOMContentLoaded', () => {

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
                    // Novo comportamento para o botão "Empréstimo"
                    const nomePessoa = prompt("Para quem você está emprestando este equipamento?");
                    
                    // Se o usuário cancelar ou não digitar nada, a ação é abortada
                    if (nomePessoa === null || nomePessoa.trim() === "") {
                        alert("Empréstimo cancelado ou nome não fornecido.");
                        return;
                    }
                    
                    // Envia a requisição com o nome da pessoa
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${equipmentId}&situacao=${action}&situacao=${encodeURIComponent(nomePessoa.trim())}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Empréstimo realizado com sucesso para ${nomePessoa}!`);
                            window.location.reload();
                        } else {
                            alert('Erro ao atualizar a situação: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Ocorreu um erro na requisição.');
                    });
                } else {
                    // Comportamento padrão para os outros botões
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

    // --- Lógica para o formulário de Cadastro e Edição (não alterada) ---
    const formEquipamento = document.querySelector('.cadastro-form');
    if (formEquipamento) {
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

        radiosEmpresa.forEach(radio => {
            radio.addEventListener('change', () => {
                updateEquipmentName();
                updateOutroFields();
            });
        });
        radiosTipo.forEach(radio => {
            radio.addEventListener('change', () => {
                updateEquipmentName();
                updateFieldAvailability();
                updateOutroFields();
            });
        });

        // Lógica de preenchimento via URL (para edição de dados)
        const params = new URLSearchParams(window.location.search);
        const equipamento = params.get('equipamento');
        const antigo = params.get('antigo');

        if (equipamento) {
            if (nomeEquipamentoInput) nomeEquipamentoInput.value = equipamento;
        }
        if (antigo) {
            const etiquetaAntigaInput = document.getElementById('etiqueta-antiga');
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