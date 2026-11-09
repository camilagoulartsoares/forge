# Programação Orientada a Objetos

## Objetivo

Estudar abstração, encapsulamento, herança e polimorfismo em PHP.

## Conceitos abordados

- Classes e objetos
- Contratos e composição

## Pré-requisitos

Introdução ao PHP.

## Como executar

Na raiz do repositorio, execute `php 01-fundamentos/php/poo/exemplos/executar.php`.
O resultado esperado é `Saldo: 3`. Requer PHP 7.4 ou superior.

## Exemplos

Veja a [classe Estoque](exemplos/Estoque.php) e o [programa executavel](exemplos/executar.php).

O saldo privado só muda pela operação validada da classe. Como exercício,
tente adicionar zero e um número negativo: ambos devem ser rejeitados sem
alterar o saldo. Crie duas instâncias e confira que os saldos são independentes.
Uma adição que ultrapasse `PHP_INT_MAX` lança `OverflowException` e preserva
o saldo anterior; esse limite acompanha a plataforma onde o PHP é executado.

A operação `retirar` exige quantidade positiva e saldo suficiente. Quantidade
inválida lança `InvalidArgumentException`; insuficiência lança `UnderflowException`.
Ambas preservam o estado. Retirar exatamente o saldo é válido e deixa zero.
Os testes cobrem retirada parcial, total, de estoque vazio e acima do disponível.

## Exercícios

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
