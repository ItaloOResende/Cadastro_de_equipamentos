import pandas as pd
import os

def sanitize_for_sql(valor):
    """
    Formata valores para uma string SQL segura.
    Se o valor for nulo, retorna uma string vazia.
    """
    if pd.isna(valor):
        return "''" # Alterado para retornar string vazia
    elif isinstance(valor, (int, float)):
        return str(valor)
    elif isinstance(valor, str):
        valor_sanitizado = valor.replace("'", "''")
        return f"'{valor_sanitizado}'"
    else:
        return f"'{str(valor)}'"

def excel_para_sql(caminho_excel, nome_tabela, caminho_saida_sql):
    """
    Lê uma planilha Excel e gera um arquivo SQL com comandos INSERT.
    """
    colunas_map = {
        'Computadores': {
            'Empresa': 'empresa',
            'Equipamento': 'tipo_equipamento',
            'Nome': 'nome_equipamento',
            'Antigo/Etiqueta': 'etiqueta_antiga',
            'Marca/modelo': 'marca_modelo',
            'CPU': 'cpu',
            'RAM': 'ram',
            'Armazenamento': 'armazenamento',
            'Entradas de vídeo': 'entradas_video',
            'Observação': 'observacao',
            'Entrada': 'data_entrada',
            'Situação': 'situacao'
        },
        'Monitores': {
            'Empresa': 'empresa',
            'Equipamento': 'tipo_equipamento',
            'Nome': 'nome_equipamento',
            'Antigo/Etiqueta': 'etiqueta_antiga',
            'Marca/modelo': 'marca_modelo',
            'CPU': 'cpu',
            'RAM': 'ram',
            'Armazenamento': 'armazenamento',
            'Entradas de vídeo': 'entradas_video',
            'Observação': 'observacao',
            'Entrada': 'data_entrada',
            'Situação': 'situacao'
        },
        'Outros equipamentos': {
            'Empresa': 'empresa',
            'Equipamento': 'tipo_equipamento',
            'Nome': 'nome_equipamento',
            'Antigo/Etiqueta': 'etiqueta_antiga',
            'Marca/modelo': 'marca_modelo',
            'CPU': 'cpu',
            'RAM': 'ram',
            'Armazenamento': 'armazenamento',
            'Entradas de vídeo': 'entradas_video',
            'Observação': 'observacao',
            'Entrada': 'data_entrada',
            'Situação': 'situacao'
        }
    }
    
    colunas_sql = [
        'empresa', 'tipo_equipamento', 'nome_equipamento', 'etiqueta_antiga',
        'marca_modelo', 'cpu', 'ram', 'armazenamento',
        'entradas_video', 'observacao', 'data_entrada', 'situacao'
    ]

    todos_inserts = []
    
    try:
        xls = pd.ExcelFile(caminho_excel)
    except FileNotFoundError:
        print(f"Erro: O arquivo '{caminho_excel}' não foi encontrado.")
        return

    for sheet_name in xls.sheet_names:
        if sheet_name in colunas_map:
            df = pd.read_excel(xls, sheet_name=sheet_name)
            
            # Renomeia as colunas
            df = df.rename(columns={k: v for k, v in colunas_map[sheet_name].items()})
            
            # Adiciona colunas que podem não existir na planilha, para garantir consistência
            for col in colunas_sql:
                if col not in df.columns:
                    df[col] = None

            # Ajuste para garantir que a coluna 'empresa' exista
            if 'empresa' not in df.columns or df['empresa'].isnull().all():
                df['empresa'] = 'GVU'

            # Combina colunas de marca e modelo
            if 'marca_modelo_modelo' in df.columns:
                df['marca_modelo'] = df['marca_modelo'].fillna('') + ' ' + df['marca_modelo_modelo'].fillna('')
                df['marca_modelo'] = df['marca_modelo'].str.strip().replace('', None)
                df.drop(columns=['marca_modelo_modelo'], inplace=True)

            # Combina observações
            df['observacao'] = df['observacao'].fillna('')
            if 'Usuário' in df.columns:
                df['observacao'] = df.apply(lambda row: f"{row['observacao']} | Usuário: {row['Usuário']}".strip(' | ') if pd.notna(row['Usuário']) else row['observacao'], axis=1)
                df.drop(columns=['Usuário'], inplace=True)
            
            if 'Setor' in df.columns:
                df.drop(columns=['Setor'], inplace=True)

            # Aplica valores padrão
            df['tipo_equipamento'] = df['tipo_equipamento'].fillna(sheet_name)
            df['situacao'] = df['situacao'].fillna('Em estoque')
            
            # Converte a coluna de data para o formato YYYY-MM-DD
            df['data_entrada'] = pd.to_datetime(df['data_entrada'], errors='coerce').dt.strftime('%Y-%m-%d')
            df.dropna(subset=['data_entrada'], inplace=True)
            
            # Garante a ordem correta das colunas
            df = df[colunas_sql]

            # Gera as instruções INSERT
            colunas_sql_str = ', '.join(colunas_sql)
            for _, row in df.iterrows():
                valores = [sanitize_for_sql(row[col]) for col in colunas_sql]
                valores_str = ', '.join(valores)
                insert_statement = f"INSERT INTO {nome_tabela} ({colunas_sql_str}) VALUES ({valores_str});\n"
                todos_inserts.append(insert_statement)
            
            print(f"Processado a planilha '{sheet_name}'. Adicionadas {len(df)} linhas ao script SQL.")

    # Salva as instruções em um arquivo SQL
    if todos_inserts:
        with open(caminho_saida_sql, 'w', encoding='utf-8') as f:
            f.writelines(todos_inserts)
        print(f"\nArquivo SQL gerado com sucesso em '{caminho_saida_sql}'!")
        print(f"Total de linhas convertidas: {len(todos_inserts)}")
    else:
        print("\nNenhum dado válido encontrado para gerar o arquivo SQL.")

if __name__ == "__main__":
    caminho_do_excel = 'EQUIPAMENTOS GVU.xlsx'
    nome_da_tabela = 'equipamentos'
    caminho_do_sql = 'inserts_equipamentos.sql'

    excel_para_sql(
        caminho_excel=caminho_do_excel,
        nome_tabela=nome_da_tabela,
        caminho_saida_sql=caminho_do_sql
    )