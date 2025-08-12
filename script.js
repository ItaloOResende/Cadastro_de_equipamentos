document.addEventListener('DOMContentLoaded', () => {
    // Seleciona todos os botões de verificar
    const verifyButtons = document.querySelectorAll('.status-button[data-action="verify"]');

    // Itera sobre cada botão e adiciona um "ouvidor" de eventos de clique
    verifyButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Redireciona para a página de edição
            window.location.href = 'editar_dados.html';
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    // Seleciona todos os botões de verificar
    const cadastroButtons = document.querySelectorAll('.btn-primary[data-action="cadastro"]');

    // Itera sobre cada botão e adiciona um "ouvidor" de eventos de clique
    cadastroButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Redireciona para a página de edição
            window.location.href = 'cadastrar.html';
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
            // --- SELEÇÃO DOS ELEMENTOS ---
            const nomeEquipamentoInput = document.getElementById('equipamento-nome');
            const radiosEmpresa = document.querySelectorAll('input[name="filtro_empresa"]');
            const radiosTipo = document.querySelectorAll('input[name="tipo_equipamento"]');
            
            const cpuInput = document.getElementById('cpu');
            const ramInput = document.getElementById('ram');
            const armazenamentoInput = document.getElementById('armazenamento');
            const videoInput = document.getElementById('entradas-video');
            // Seleciona o novo campo
            const quantidadeInput = document.getElementById('quantidade');

            // --- FUNÇÕES ---

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
                const tipoSelecionado = document.querySelector('input[name="filtro_tipo"]:checked');
                if (!tipoSelecionado) return;

                const tipoValue = tipoSelecionado.value;

                // Lógica para CPU, RAM e Armazenamento
                const isComputer = tipoValue === 'desktop' || tipoValue === 'notebook';
                cpuInput.disabled = !isComputer;
                ramInput.disabled = !isComputer;
                armazenamentoInput.disabled = !isComputer;

                // Lógica para Entradas de Vídeo
                const hasVideoPorts = tipoValue === 'desktop' || tipoValue === 'notebook' || tipoValue === 'monitor';
                videoInput.disabled = !hasVideoPorts;

                // LÓGICA ATUALIZADA: Habilita/desabilita o campo Quantidade
                const isOtherEquipment = tipoValue === 'outros';
                quantidadeInput.disabled = !isOtherEquipment;

                // Limpa os valores dos campos ao desabilitá-los
                if (!isComputer) {
                    cpuInput.value = '';
                    ramInput.value = '';
                    armazenamentoInput.value = '';
                }
                if (!hasVideoPorts) {
                    videoInput.value = '';
                }
                // Se o campo Quantidade for desabilitado, redefine para 1
                if (!isOtherEquipment) {
                    quantidadeInput.value = '1';
                }
            };

            // --- EVENTOS ---

            radiosEmpresa.forEach(radio => {
                radio.addEventListener('change', updateEquipmentName);
            });

            radiosTipo.forEach(radio => {
                radio.addEventListener('change', () => {
                    updateEquipmentName();
                    updateFieldAvailability();
                });
            });

            // --- INICIALIZAÇÃO ---
            updateEquipmentName();
            updateFieldAvailability();
        });
        // Funções para habilitar/desabilitar campos (copiadas do cadastro)
        const updateFieldAvailability = () => {
            // ... (a mesma função de antes para habilitar/desabilitar campos)
        };
    
        document.addEventListener('DOMContentLoaded', () => {
            // 1. LER OS PARÂMETROS DA URL
            const params = new URLSearchParams(window.location.search);

            const equipamento = params.get('equipamento');
            const antigo = params.get('antigo');
            // Adicione aqui outras variáveis que você queira passar, como:
            // const marca = params.get('marca');
            // const modelo = params.get('modelo');
            
            // 2. PREENCHER OS CAMPOS DO FORMULÁRIO
            if (equipamento) {
                document.getElementById('equipamento-nome').value = equipamento;
            }
            if (antigo) {
                document.getElementById('etiqueta-antiga').value = antigo;
            }
            // Exemplo para preencher outros campos:
            // if (marca) {
            //     document.getElementById('marca-modelo').value = marca;
            // }

            // Lógica para simular a seleção dos filtros e habilitar/desabilitar campos
            // (Esta parte é um pouco mais avançada, focaremos no preenchimento por enquanto)
        });


