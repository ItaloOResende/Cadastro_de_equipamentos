import pandas as pd
import os

def sanitize_for_sql(valor):
    """
    Formata valores para uma string SQL segura.
    """
    if pd.isna(valor):
        return "NULL"
    elif isinstance(valor, str):
        # Escapa aspas simples e envolve a string em aspas simples
        valor_sanitizado = valor.replace("'", "''")
        return f"'{valor_sanitizado}'"
    else:
        # Converte outros tipos de dados para string
        return str(valor)

def excel_para_sql(caminho_excel, nome_tabela, caminho_saida_sql):
    """
    Lê uma planilha Excel e gera um arquivo SQL com comandos INSERT.
    """
    # Mapeamento das colunas da planilha para as colunas da tabela SQL
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
            'Marca': 'marca_modelo',
            'Modelo': 'marca_modelo_modelo',
            'Entradas de vídeo': 'entradas_video',
            'Observação': 'observacao',
            'Entrada': 'data_entrada',
        },
        'Outros equipamentos': {
            'Equipamento': 'tipo_equipamento',
            'Antigo/Etiqueta': 'etiqueta_antiga',
            'Marca/Modelo': 'marca_modelo',
            'Modelo': 'marca_modelo_modelo',
            'Observação': 'observacao',
            'Entrada': 'data_entrada',
        }
    }

    # As colunas da tabela SQL que serão preenchidas
    colunas_sql = [
        'empresa', 'tipo_equipamento', 'nome_equipamento', 'etiqueta_antiga',
        'marca_modelo', 'cpu', 'ram', 'armazenamento',
        'entradas_video', 'observacao', 'data_entrada', 'situacao'
    ]
    colunas_sql_str = ', '.join(colunas_sql)

    todos_inserts = []
    
    # Usa o pd.ExcelFile para ler todas as abas
    try:
        xls = pd.ExcelFile(caminho_excel)
    except FileNotFoundError:
        print(f"Erro: O arquivo '{caminho_excel}' não foi encontrado.")
        return

    for sheet_name in xls.sheet_names:
        if sheet_name in colunas_map:
            df = pd.read_excel(xls, sheet_name=sheet_name)
            
            # Ajusta as colunas do DataFrame para o mapeamento
            df = df.rename(columns={k: v for k, v in colunas_map[sheet_name].items()})
            
            # Garante que todas as colunas SQL existam, preenchendo com None se necessário
            for col in colunas_sql:
                if col not in df.columns:
                    df[col] = None
            
            # Combina colunas de marca e modelo
            if 'marca_modelo_modelo' in df.columns:
                df['marca_modelo'] = df['marca_modelo'].fillna('') + ' ' + df['marca_modelo_modelo'].fillna('')
                df['marca_modelo'] = df['marca_modelo'].str.strip()
            
            # Combina observações
            df['observacao'] = df['observacao'].fillna('')
            if 'Usuário' in df.columns:
                df['observacao'] = df.apply(lambda row: f"{row['observacao']} | Usuário: {row['Usuário']}" if pd.notna(row['Usuário']) else row['observacao'], axis=1)
            if 'Setor' in df.columns:
                df['observacao'] = df.apply(lambda row: f"{row['observacao']} | Setor: {row['Setor']}" if pd.notna(row['Setor']) else row['observacao'], axis=1)
            
            # Adiciona valores padrões
            df['empresa'] = df['empresa'].fillna('GVU')
            df['tipo_equipamento'] = df['tipo_equipamento'].fillna(sheet_name)
            df['situacao'] = df['situacao'].fillna('Em estoque')
            df['data_entrada'] = pd.to_datetime(df['data_entrada'], errors='coerce').dt.strftime('%Y-%m-%d')
            
            # Filtra apenas as colunas da tabela SQL
            df = df[colunas_sql]

            # Gera as instruções INSERT
            sql_inserts = []
            for _, row in df.iterrows():
                valores = [sanitize_for_sql(row[col]) for col in colunas_sql]
                valores_str = ', '.join(valores)
                insert_statement = f"INSERT INTO {nome_tabela} ({colunas_sql_str}) VALUES ({valores_str});\n"
                sql_inserts.append(insert_statement)
            
            todos_inserts.extend(sql_inserts)

    # Salva as instruções em um arquivo SQL
    with open(caminho_saida_sql, 'w', encoding='utf-8') as f:
        f.writelines(todos_inserts)
    
    print(f"Arquivo SQL gerado com sucesso em '{caminho_saida_sql}'!")
    print(f"Total de linhas convertidas: {len(todos_inserts)}")

if __name__ == "__main__":
    caminho_do_excel = 'EQUIPAMENTOS GVU.xlsx'
    nome_da_tabela = 'equipamentos'
    caminho_do_sql = 'inserts_equipamentos.sql'

    excel_para_sql(
        caminho_excel=caminho_do_excel,
        nome_tabela=nome_da_tabela,
        caminho_saida_sql=caminho_do_sql
    )
