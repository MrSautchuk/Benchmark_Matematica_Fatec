use std::time::Instant;
use std::fs::File;
use std::io::{BufRead, BufReader};

// As 6 funções exigidas
fn fibo_l(n: u32) -> u64 { let (mut a, mut b) = (0, 1); for _ in 0..n { let t=a; a=b; b=t+b; } a }
fn fibo_r(n: u32) -> u64 { if n<=1 { return n as u64; } fibo_r(n-1)+fibo_r(n-2) }
fn fact_l(n: u32) -> u128 { let mut r=1; for i in 2..=n { r*=i as u128; } r }
fn fact_r(n: u32) -> u128 { if n<=1 { return 1; } n as u128 * fact_r(n-1) }
fn sum_l(n: u32) -> u64 { let mut r=0; for i in 1..=n { r+=i as u64; } r }
fn sum_r(n: u32) -> u64 { if n<=1 { return n as u64; } n as u64 + sum_r(n-1) }

fn consultar_banco_simulado() {
    // O Rust abre o arquivo e lê as 1000 linhas simulando a carga de dados de IO do banco
    if let Ok(arquivo) = File::open("dados_banco.txt") {
        let leitor = BufReader::new(arquivo);
        for _linha in leitor.lines() {
            // Processa os registros
        }
    }
}

fn main() {
    let ini = Instant::now();
    
    fibo_l(35);
    fibo_r(35);
    fact_l(10);
    fact_r(10);
    sum_l(1500);
    sum_r(1500);
    
    // Executa a leitura dos 1000 registros no Rust
    consultar_banco_simulado();
    
    print!("{}", ini.elapsed().as_secs_f64());
}