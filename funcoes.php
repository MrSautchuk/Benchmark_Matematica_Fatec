<?php

function fibonacci_loop(int $posicao) {
    $atual = 0;
    $proximo = 1;
    
    for ($i = 0; $i < $posicao; $i++) {
        $temporario = $atual;
        $atual = $proximo;
        $proximo = $temporario + $proximo;
    }
    
    return $atual;
}

function fibonacci_recursivo(int $posicao) {
    if ($posicao <= 1) {
        return $posicao;
    }
    
    return fibonacci_recursivo($posicao - 1) + fibonacci_recursivo($posicao - 2);
}

function fatorial_loop(int $numero) {
    $resultado = 1;
    
    for ($i = 2; $i <= $numero; $i++) {
        $resultado = $resultado * $i;
    }
    
    return $resultado;
}

function fatorial_recursivo(int $numero) {
    if ($numero <= 1) {
        return 1;
    }
    
    return $numero * fatorial_recursivo($numero - 1);
}

function somatoria_loop(int $limite) {
    $soma_total = 0;
    
    for ($i = 1; $i <= $limite; $i++) {
        $soma_total = $soma_total + $i;
    }
    
    return $soma_total;
}

function somatoria_recursivo(int $limite) {
    if ($limite <= 1) {
        return $limite;
    }
    
    return $limite + somatoria_recursivo($limite - 1);
}

function executar_consulta_banco() {
    // Abre o arquivo de banco de dados criado pelo Python
    $banco = new PDO('sqlite:trabalho.db');
    
    // Executa a consulta de 1000 registros
    $comando = $banco->query("SELECT * FROM usuarios");
    $resultados = $comando->fetchAll(PDO::FETCH_ASSOC);
}

// 1. Inicia o cronômetro
$tempo_inicial = microtime(true);

// 2. Executa as 6 funções matemáticas
fibonacci_loop(35);
fibonacci_recursivo(35);
fatorial_loop(10);
fatorial_recursivo(10);
somatoria_loop(1500);
somatoria_recursivo(1500);

// 3. Executa a consulta no banco de dados dentro do PHP
executar_consulta_banco();

// 4. Calcula o tempo total acumulado (Matemática + Banco)
$tempo_final = microtime(true);
$tempo_total_gasto = $tempo_final - $tempo_inicial;

echo $tempo_total_gasto . PHP_EOL;