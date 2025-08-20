document.addEventListener('DOMContentLoaded', () => {
    // --- Seleção dos elementos do DOM ---
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
                } else if (tipoSelecionado.value === 'desktop') {
                    sufixo = 'DESK';
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

        const isComputer = tipoValue === 'maquina' || tipoValue === 'notebook';
        cpuInput.disabled = !isComputer;
        ramInput.disabled = !isComputer;
        armazenamentoInput.disabled = !isComputer;

        const hasVideoPorts = tipoValue === 'desktop' || tipoValue === 'notebook' || tipoValue === 'monitor';
        videoInput.disabled = !hasVideoPorts;

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
        });
    });

    radiosTipo.forEach(radio => {
        radio.addEventListener('change', () => {
            updateEquipmentName();
            updateFieldAvailability();
            updateOutroFields();
        });
    });

    // --- Funcionalidades de Redirecionamento ---
    const verifyButtons = document.querySelectorAll('.status-button[data-action="verify"]');
    verifyButtons.forEach(button => {
        button.addEventListener('click', () => {
            window.location.href = 'editar_dados.html';
        });
    });

const cadastroButtons = document.querySelectorAll('.btn-primary[data-action="cadastro"]');
cadastroButtons.forEach(button => {
    button.addEventListener('click', () => {
        window.location.href = 'cadastrar.html';
    });
});


    // --- Lógica de preenchimento via URL (para edição de dados) ---
    const params = new URLSearchParams(window.location.search);
    const equipamento = params.get('equipamento');
    const antigo = params.get('antigo');

    if (equipamento) {
        nomeEquipamentoInput.value = equipamento;
    }
    if (antigo) {
        document.getElementById('etiqueta-antiga').value = antigo;
    }

    // --- Chamadas de inicialização ---
    updateEquipmentName();
    updateFieldAvailability();
    updateOutroFields();
});
