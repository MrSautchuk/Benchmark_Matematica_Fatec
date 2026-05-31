# Benchmark Comparativo de Performance Híbrida: PHP 8.x vs. Rust
**Disciplina:** Matemática para computação  
**Instituição:** Fatec (Faculdade de Tecnologia) de Olímpia  
**Autor:** Allan Sautchuk - Vando Souza

---

## 1. Introdução
O estudo comparativo de performance entre ecossistemas computacionais exige o isolamento de variáveis ambientais e a aplicação de testes simétricos. Ambientes modernos de produção frequentemente lidam com dois tipos de restrições operacionais: limitações de processamento puramente aritmético (CPU-Bound) e gargalos de leitura e escrita de dados de fontes externas (I/O-Bound).

Este artigo analisa empiricamente a eficiência em runtime do PHP 8.x, executado sob a Máquina Virtual Zend, em comparação com o Rust, compilado nativamente. O diferencial deste experimento consiste no uso de um teste híbrido contendo algoritmos complexos iterativos e recursivos, somados à leitura simultânea de uma base de dados compartilhada com volumetria idêntica.

---

## 2. Metodologia e Arquitetura do Sistema

### 2.1 Cenário de Dados Simétrico
Para certificar a equidade do teste de Entrada e Saída (I/O), um script em Python atua exclusivamente como o preparador de ambiente. Ele gera uma amostragem idêntica de 1.000 registros numéricos e textuais distribuídos em duas vertentes:
* **Para o PHP:** Uma base estruturada através de um arquivo relacional físico SQLite (`trabalho.db`).
* **Para o Rust:** Um arquivo de texto plano estruturado com delimitadores lineares (`dados_banco.txt`).

### 2.2 O Orquestrador e Coleta por Saída Padrão (Stdout)
Diferente de sistemas de auditoria externa de tempo, o papel do script Python é gerenciar o ciclo de execuções sem interferir no tempo computacional das ferramentas. 
A precisão da métrica reside dentro de cada linguagem. Ao finalizar o processamento interno, o script PHP e o executável Rust imprimem de forma limpa o tempo decorrido em segundos para o terminal. O script Python, utilizando o módulo `subprocess` com flags de captura ativa, recolhe esse valor textual da saída padrão (`res.stdout`), convertendo-o em ponto flutuante para o cálculo das médias estatísticas.

### 2.3 Estabilização por Ciclos de Execução
O ecossistema executa um loop mestre de 3 grandes repetições. Cada repetição dispara uma série de 10 rodadas consecutivas (`RUNS = 10`) para cada linguagem. Essa técnica permite à banca avaliar explicitamente o impacto físico do cache da CPU. No primeiro macrociclo, o sistema operacional realiza leituras frias de disco. Nos ciclos subsequentes, os ponteiros de arquivos e opcodes já se encontram armazenados na memória de cache volátil (L1/L2/L3) e na RAM, revelando a real eficiência do motor de runtime.

---

## 3. Algoritmos Computacionais de Estresse (CPU-Bound)
As linguagens foram submetidas ao processamento síncrono de seis rotinas matemáticas clássicas:

1. **Fibonacci Iterativo (`fibo_l` / `fibonacci_loop`):** Resolução por laço linear (Complexidade $O(n)$) com substituição de variáveis temporárias.
2. **Fibonacci Recursivo (`fibo_r` / `fibonacci_recursivo`):** Resolução por árvore de recursão cruzada. Descarrega um volume expressivo de chamadas de escopo empilhadas na memória Stack.
3. **Fatorial Iterativo e Recursivo (`fact_l` / `fact_r`):** Multiplicações sequenciais que testam o limite de armazenamento de tipos numéricos primitivos de alta capacidade (`u128` no Rust).
4. **Somatória Iterativa e Recursiva (`sum_l` / `sum_r`):** Adições consecutivas de limites escalares estressando o estouro de pilha.

Após o término computacional dos algoritmos de CPU, as linguagens ativam seus métodos de Entrada e Saída: o PHP instancia uma conexão via drive abstrato de dados `PDO` executando uma query estruturada `SELECT * FROM usuarios`; o Rust realiza a abertura com buffer estruturado `BufReader` percorrendo a cadeia de linhas do arquivo.

---

## 4. Dinâmica Interna e Análise de Runtime

### 4.1 PHP (Zend VM)
Ao rodar `php funcoes.php`, o tempo é capturado por funções nativas baseadas no clock de sistema (`microtime(true)`). O código passa por um processo em tempo de execução onde a infraestrutura lê, valida e converte as strings de texto em Opcodes gerenciados e interpretados pela Zend VM. O acesso ao banco SQLite via PDO exige a instanciação dinâmica de objetos e o Garbage Collector monitora ativamente as alocações em background, inserindo um custo computacional perceptível durante loops intensos.

### 4.2 Rust (Nativo)
Ao rodar o binário `funcoes_exe.exe` (compilado sob a diretiva `--release`), o tempo é extraído via estrutura monolítica `Instant::now()`, que lê instruções de hardware diretas e invariáveis. O código executa sem nenhuma camada intermediária, interpretadores ou máquinas virtuais. 

As recursões complexas de árvore (como o Fibonacci) rodam sob otimizações agressivas de registradores feitas pelo compilador LLVM. Além disso, a ausência de um Garbage Collector garante que a leitura de strings através do `BufReader` ocorra com segurança de memória gerenciada integralmente em tempo de compilação pelo conceito de *Ownership*.
