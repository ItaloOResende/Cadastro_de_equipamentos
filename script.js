document.addEventListener('DOMContentLoaded', () => {

<<<<<<< HEAD
    // --- Lógica para os botões de status (página index.php) ---
    const tableBody = document.querySelector('.main-data-table tbody');
    if (tableBody) {
        tableBody.addEventListener('click', (event) => {
            const button = event.target.closest('.status-button[data-action]');
            
            if (button) {
                const action = button.dataset.action;
                const equipmentId = button.dataset.id;
                
                if (action === 'verify') {
                    // Lógica CORRIGIDA para o botão de 'Verificar'
                    // Redireciona para a página de edição, passando o ID
                    window.location.href = `editar.php?id=${equipmentId}`;
                } else {
                    // Lógica para atualização do status
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
=======
    const cpuInput = document.getElementById('cpu');
    const ramInput = document.getElementById('ram');
    const armazenamentoInput = document.getElementById('armazenamento');
    const videoInput = document.getElementById('entradas-video');
    const quantidadeInput = document.getElementById('quantidade');

    const inputEmpresaOutro = document.getElementById('empresa-outro-texto');
    const inputEquipOutro = document.getElementById('equipamento-outro-texto');

    // --- Funções principais ---

    // Função para atualizar o campo de Nome do Equipamento
    const updateEquipmentName = () => {
        const empresaSelecionada = document.querySelector('input[name="filtro_empresa"]:checked');
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');
        let nomeFinal = '';

        if (empresaSelecionada && empresaSelecionada.value !== 'outro') {
            let nomeBase = empresaSelecionada.value.toUpperCase();
            let sufixo = '';

            if (tipoSelecionado && tipoSelecionado.value !== 'outros') {
                if (tipoSelecionado.value === 'monitor') {
                    sufixo = 'MON';
                } else if (tipoSelecionado.value === 'notebook') {
                    sufixo = 'NOT';
            }
        }
            nomeFinal = nomeBase + sufixo;
        }
        nomeEquipamentoInput.value = nomeFinal;
    };

    // Função para controlar a disponibilidade dos campos
    const updateFieldAvailability = () => {
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');
        if (!tipoSelecionado) return;

        const tipoValue = tipoSelecionado.value;

        const isComputer = tipoValue === 'monitor';
        cpuInput.disabled = isComputer;
        ramInput.disabled = isComputer;
        armazenamentoInput.disabled = isComputer;

        const hasVideoPorts = tipoValue === 'desktop' || tipoValue === 'notebook' || tipoValue === 'monitor';


        const isOtherEquipment = tipoValue === 'outros';
        quantidadeInput.disabled = !isOtherEquipment;

        if (!isComputer) {
            cpuInput.value = '';
            ramInput.value = '';
            armazenamentoInput.value = '';
        }
        if (!hasVideoPorts) {
            videoInput.value = '';
        }
        if (!isOtherEquipment) {
            quantidadeInput.value = '1';
        }
    };

    // Função para habilitar/desabilitar os campos de texto "Outro"
    const updateOutroFields = () => {
        // CORREÇÃO: Usando querySelector para encontrar o elemento checado
        const empresaSelecionada = document.querySelector('input[name="filtro_empresa"]:checked');
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');
        
        // Campo "Outro" da Empresa
        const isOutroEmpresa = empresaSelecionada && empresaSelecionada.value === 'outro';
        inputEmpresaOutro.disabled = !isOutroEmpresa;
        if (!isOutroEmpresa) inputEmpresaOutro.value = '';
        
        // Campo "Outro" do Equipamento
        const isOutroEquip = tipoSelecionado && tipoSelecionado.value === 'outros';
        inputEquipOutro.disabled = !isOutroEquip;
        if (!isOutroEquip) inputEquipOutro.value = '';
    };

    // --- Definição dos ouvintes de eventos ---
    radiosEmpresa.forEach(radio => {
        radio.addEventListener('change', () => {
            updateEquipmentName();
            updateOutroFields();
>>>>>>> 7d430a2bdc09651c06807b1a538567168408e9ca
        });
    }

<<<<<<< HEAD
    // --- Lógica para o formulário de Cadastro e Edição ---
    const formEquipamento = document.querySelector('.cadastro-form');
    if (formEquipamento) {
        const nomeEquipamentoInput = document.getElementById('equipamento-nome');
        const radiosEmpresa = document.querySelectorAll('input[name="filtro_empresa"]');
        const radiosTipo = document.querySelectorAll('input[name="filtro_tipo"]');
    
        const cpuInput = document.getElementById('cpu');
        const ramInput = document.getElementById('ram');
        const armazenamentoInput = document.getElementById('armazenamento');
        const videoInput = document.getElementById('entradas-video');
        const quantidadeInput = document.getElementById('quantidade');
    
        const inputEmpresaOutro = document.getElementById('empresa-outro-texto');
        const inputEquipOutro = document.getElementById('equipamento-outro-texto');
    
        const updateEquipmentName = () => {
            const empresaSelecionada = document.querySelector('input[name="filtro_empresa"]:checked');
            const tipoSelecionado = document.querySelector('input[name="filtro_tipo"]:checked');
            let nomeFinal = '';
    
            if (empresaSelecionada && empresaSelecionada.value !== 'outro') {
                let nomeBase = empresaSelecionada.value.toUpperCase();
                let sufixo = '';
    
                if (tipoSelecionado && tipoSelecionado.value !== 'outros') {
                    if (tipoSelecionado.value === 'monitor') {
                        sufixo = 'MON';
                    } else if (tipoSelecionado.value === 'notebook') {
                        sufixo = 'NOT';
                    }
                nomeFinal = nomeBase + (sufixo ? `${sufixo}` : '');
            }
            if (nomeEquipamentoInput) {
                nomeEquipamentoInput.value = nomeFinal;
            }
        }
        };

        const updateFieldAvailability = () => {
            const tipoSelecionado = document.querySelector('input[name="filtro_tipo"]:checked');
            if (!tipoSelecionado) return;
            const tipoValue = tipoSelecionado.value;
            const isMonitor = tipoValue === 'monitor';
            const isComputer = tipoValue === 'desktop' || tipoValue === 'notebook';

            if (cpuInput) cpuInput.disabled = isMonitor || !isComputer;
            if (ramInput) ramInput.disabled = isMonitor || !isComputer;
            if (armazenamentoInput) armazenamentoInput.disabled = isMonitor || !isComputer;

            const hasVideoPorts = tipoValue === 'desktop' || tipoValue === 'notebook' || tipoValue === 'monitor';
            if (videoInput) videoInput.disabled = !hasVideoPorts;

            const isOtherEquipment = tipoValue === 'outros';
            if (quantidadeInput) quantidadeInput.disabled = !isOtherEquipment;

            if (!isComputer) {
                if (cpuInput) cpuInput.value = '';
                if (ramInput) ramInput.value = '';
                if (armazenamentoInput) armazenamentoInput.value = '';
            }
            if (!hasVideoPorts) {
                if (videoInput) videoInput.value = '';
            }
            if (!isOtherEquipment) {
                if (quantidadeInput) quantidadeInput.value = '1';
            }
        };

        const updateOutroFields = () => {
            const empresaSelecionada = document.querySelector('input[name="filtro_empresa"]:checked');
            const tipoSelecionado = document.querySelector('input[name="filtro_tipo"]:checked');
            const isOutroEmpresa = empresaSelecionada && empresaSelecionada.value === 'outro';
            if (inputEmpresaOutro) {
                inputEmpresaOutro.disabled = !isOutroEmpresa;
                if (!isOutroEmpresa) inputEmpresaOutro.value = '';
            }
            const isOutroEquip = tipoSelecionado && tipoSelecionado.value === 'outros';
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

        // Chamadas de inicialização
        updateEquipmentName();
        updateFieldAvailability();
        updateOutroFields();
    }
    
=======
    // --- Chamadas de inicialização ---
    updateEquipmentName();
    updateFieldAvailability();
    updateOutroFields();
});

//Parte do código para os botões de situação
document.addEventListener('DOMContentLoaded', () => {

    // Adiciona um ouvinte de eventos na tabela para gerenciar os cliques nos botões de status
    const tableBody = document.querySelector('.main-data-table tbody');
    if (tableBody) {
        tableBody.addEventListener('click', (event) => {
            const button = event.target.closest('.status-button[data-action]');
            
            if (button) {
                const action = button.dataset.action;
                const equipmentId = button.dataset.id;
                
                if (action === 'verify') {
                    // Lógica para o botão de 'Verificar' (redireciona para edição)
                    const row = button.closest('tr');
                    const equipamento = row.querySelector('td:nth-child(1)').textContent;
                    const antigo = row.querySelector('td:nth-child(2)').textContent;
                    window.location.href = `editar_dados.html?equipamento=${encodeURIComponent(equipamento)}&antigo=${encodeURIComponent(antigo)}`;
                } else {
                    // Lógica para atualização do status
                    if (confirm(`Tem certeza de que deseja alterar a situação para "${action}"?`)) {
                        // Envia uma requisição POST para o servidor
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
                                window.location.reload(); // Recarrega a página para mostrar a mudança
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

>>>>>>> 7d430a2bdc09651c06807b1a538567168408e9ca
    // Funcionalidades de Redirecionamento para o botão de cadastro
    const cadastroButton = document.querySelector('.btn-primary[data-action="cadastro"]');
    if (cadastroButton) {
        cadastroButton.addEventListener('click', () => {
            window.location.href = 'cadastrar.html';
        });
    }
<<<<<<< HEAD
});
=======

});
>>>>>>> 7d430a2bdc09651c06807b1a538567168408e9ca
