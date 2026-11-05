# Introdução ao PHP

## Objetivo

Descrever fundamentos da linguagem PHP.

## Conceitos abordados

- Sintaxe básica
- Tipos e controle de fluxo

## Pré-requisitos

PHP instalado localmente.

## Como executar

Na raiz do repositorio, execute o exemplo versionado:

```sh
php -l 01-fundamentos/php/introducao/exemplos/calcular-total.php
php 01-fundamentos/php/introducao/exemplos/executar.php
```

O primeiro comando verifica a sintaxe. O segundo deve imprimir `Total: 60`.

## Exemplos

Veja a [regra de total](exemplos/calcular-total.php) e o [programa executavel](exemplos/executar.php).

O exemplo trabalha com valores inteiros; não representa cálculo financeiro
com frações. Experimente quantidade zero e confirme total zero. Um argumento
negativo deve provocar `InvalidArgumentException` em vez de produzir um total inválido.
Produtos acima de `PHP_INT_MAX` lançam `OverflowException` antes da multiplicação,
evitando conversão implícita para ponto flutuante e falha no tipo de retorno.

## Exercícios

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
