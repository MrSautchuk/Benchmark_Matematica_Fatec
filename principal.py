import subprocess
import time
import sqlite3

RUNS = 10

def preparar_ambiente_de_dados():
    # Cria o banco SQLite real em arquivo para o PHP ler
    conexao = sqlite3.connect('trabalho.db')
    cursor = conexao.cursor()
    cursor.execute('DROP TABLE IF EXISTS usuarios')
    cursor.execute('CREATE TABLE usuarios (id INT, nome TEXT)')
    dados = [(i, f"Usuario_{i}") for i in range(1, 1001)]
    cursor.executemany('INSERT INTO usuarios VALUES (?, ?)', dados)
    conexao.commit()
    conexao.close()

    # Cria o arquivo de texto com 1000 registros para o Rust ler de forma justa
    with open("dados_banco.txt", "w") as f:
        for i in range(1, 1001):
            f.write(f"{i},Usuario_{i}\n")

def rodar(cmd):
    res = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return float(res.stdout.strip())

# Prepara os arquivos de 1000 registros na pasta antes de começar
preparar_ambiente_de_dados()

for _ in range(3):
    print("Executando o benchmark completo, aguarde...")
    tempos_php = []
    tempos_rust = []

    for _ in range(RUNS):
        tempos_php.append(rodar("php funcoes.php"))
        tempos_rust.append(rodar("funcoes_exe.exe"))

    diferenca = sum(tempos_php) / sum(tempos_rust)


    print("=" * 60)
    print("     RESULTADO DO BENCHMARK (MÉDIAS)")
    print("=" * 60)
    print(f"PHP (Algoritmos + SQL) : {sum(tempos_php)/RUNS:.6f} segundos")
    print(f"Rust (Algoritmos + SQL): {sum(tempos_rust)/RUNS:.6f} segundos")
    print(f"Rust foi aproximadamente {diferenca:.2f} vezes mais rapido")
    print("=" * 60)