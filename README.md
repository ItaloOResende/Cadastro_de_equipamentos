# Cadastro_de_equipamentos
# ⚙️ Sistema de Gestão de Ativos e Empréstimos

[![Status do Projeto](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow)]() 
[![Licença](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Tecnologia Principal](https://img.shields.io/badge/Backend-PHP-777BB4)]()
[![Tecnologia Front](https://img.shields.io/badge/Frontend-HTML%2FCSS%2FJS-orange)]()

## 💻 Sobre o Projeto

Este é um sistema completo e dinâmico para o **cadastro e gestão do ciclo de vida de equipamentos/ativos**. Ele permite o controle total sobre o inventário e automatiza a geração de documentação essencial.

O grande diferencial é a **automação de empréstimos** através da integração com as APIs do Google Drive e Docs, que gera um **Termo de Compromisso** personalizado de forma imediata.

## ✨ Funcionalidades Principais

O sistema permite gerenciar os equipamentos com as seguintes ações:

* **Cadastro e Edição:** Inclusão e atualização de todos os dados do equipamento.
* **Gestão de Status:** Movimentação do ativo entre:
    * **Estoque** (Disponível)
    * **Empréstimo** (Em uso por um colaborador)
    * **Lixo Eletrônico (Descarte)**
* **Consulta:** Verificação de informações e histórico.
* **Automatização de Termos (Via Google Docs/Drive API):**
    * Solicita o nome do responsável.
    * Gera automaticamente um Termo de Compromisso (novo documento) a partir de um modelo.
    * Preenche as variáveis do documento (nome, ID do equipamento, nome do responsável) com os dados do sistema.

## 🛠️ Tecnologias Utilizadas

* **Linguagens:** PHP, HTML, CSS, JavaScript (JS).
* **Formato de Dados:** JSON.
* **Integrações:** Google Docs API e Google Drive API.
* **Servidor Local:** XAMPP (Apache e MySQL/MariaDB).

## ⚙️ Como Executar o Projeto

Instruções passo a passo para configurar e rodar a aplicação localmente.

### 📋 Pré-requisitos

Para rodar a aplicação, você precisará ter instalado:

1.  **XAMPP:** Para o ambiente de servidor local (Apache e MySQL/MariaDB).
2.  **Composer:** O gerenciador de dependências do PHP.
3.  **Credenciais da API do Google:** É necessário obter um arquivo de credenciais (`credentials.json`) com as permissões do Google Docs e Drive.

### 🔧 Instalação e Configuração

1.  **Clone o Repositório:** Baixe uma cópia do código-fonte para sua máquina local.
    ```bash
    git clone [https://github.com/ItaloOResende/Cadastro_de_equipamentos.git](https://github.com/ItaloOResende/Cadastro_de_equipamentos.git)
    ```
2.  **Mova para o Servidor Local:** Copie a pasta `Cadastro_de_equipamentos` clonada para o diretório de projetos do XAMPP (geralmente `C:\xampp\htdocs`).
3.  **Inicie os Serviços do XAMPP:**
    * Abra o painel de controle do XAMPP.
    * **Ligue (Start)** os serviços **Apache** e **MySQL/MariaDB**.
4.  **Instale as Dependências (PHP):**
    * No terminal, navegue até a pasta do projeto.
    * Execute: `composer install` (para instalar as bibliotecas PHP que tratam a API do Google).
5.  **Configuração de Credenciais da API:**
    * Coloque o arquivo **`credentials.json`** na pasta raiz do projeto.
    * Certifique-se de que o **ID do Documento Modelo** do Termo de Compromisso esteja configurado corretamente no seu código PHP.
6.  **Configuração do Banco de Dados:**
    * Acesse o phpMyAdmin (`http://localhost/phpmyadmin`).
    * Crie um novo banco de dados.
    * **Importe a Estrutura:** Use a função "Importar" para carregar o arquivo **`SQL.txt`** do repositório.
    * Ajuste as credenciais de conexão do banco de dados (usuário, senha, nome do DB) no seu código PHP para a configuração local do XAMPP.

### ▶️ Executando a Aplicação

1.  Com o Apache ativo no XAMPP, acesse a aplicação pelo seu navegador:
    ```
    http://localhost/Cadastro_de_equipamentos/index.php
    ```

## 👤 Autor

| [<img src="https://avatars.githubusercontent.com/u/ItaloOResende?v=4" width=115><br><sub>Italo Oliveira Resende</sub>](https://github.com/ItaloOResende) |
| :---: |

* **GitHub:** [ItaloOResende](https://github.com/ItaloOResende)
* **LinkedIn:** [[italooresende]](https://www.linkedin.com/in/italooresende/)
* **E-mail:** italoliveira5@gmail.com

## 📄 Licença

Este projeto está licenciado sob a **Licença MIT**. Veja o arquivo `LICENSE` para mais detalhes.
