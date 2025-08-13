document.addEventListener('DOMContentLoaded', () => {
    // --- Botões de verificar ---
    const verifyButtons = document.querySelectorAll('.status-button[data-action="verify"]');
    verifyButtons.forEach(button => {
        button.addEventListener('click', () => {
            window.location.href = 'editar_dados.html';
        });
    });

    // --- Botões de cadastro ---
    const cadastroButtons = document.querySelectorAll('.btn-primary[data-action="cadastro"]');
    cadastroButtons.forEach(button => {
        button.addEventListener('click', () => {
            window.location.href = 'cadastrar.html';
        });
    });

    // --- Seleção dos elementos ---
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

    // --- Funções ---
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

    const updateFieldAvailability = () => {
        const tipoSelecionado = document.querySelector('input[name="tipo_equipamento"]:checked');
        if (!tipoSelecionado) return;

        const tipoValue = tipoSelecionado.value;

        // CPU, RAM e Armazenamento apenas para Desktop ou Notebook
        const isComputer = tipoValue === 'maquina' || tipoValue === 'notebook';
        cpuInput.disabled = !isComputer;
        ramInput.disabled = !isComputer;
        armazenamentoInput.disabled = !isComputer;

        // Entradas de vídeo para Desktop, Notebook e Monitor
        const hasVideoPorts = tipoValue === 'maquina' || tipoValue === 'notebook' || tipoValue === 'monitor';
        videoInput.disabled = !hasVideoPorts;

        // Quantidade habilitada apenas para "Outros"
        const isOtherEquipment = tipoValue === 'outros';
        quantidadeInput.disabled = !isOtherEquipment;

        // Limpeza ao desabilitar
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

    const updateOutroFields = () => {
        // Empresa
        const isOutroEmpresa = document.getElementById('empresa-outro').checked;
        inputEmpresaOutro.disabled = !isOutroEmpresa;
        if (!isOutroEmpresa) inputEmpresaOutro.value = '';

        // Tipo de equipamento
        const isOutroEquip = document.getElementById('tipo-outros').checked;
        inputEquipOutro.disabled = !isOutroEquip;
        if (!isOutroEquip) inputEquipOutro.value = '';
    };

    // --- Eventos ---
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

    // --- Inicialização ---
    updateEquipmentName();
    updateFieldAvailability();
    updateOutroFields();
});

// --- Preenchimento via URL ---
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);

    const equipamento = params.get('equipamento');
    const antigo = params.get('antigo');

    if (equipamento) {
        document.getElementById('equipamento-nome').value = equipamento;
    }
    if (antigo) {
        document.getElementById('etiqueta-antiga').value = antigo;
    }
});
