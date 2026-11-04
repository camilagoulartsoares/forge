# Introdução ao PHP

## Objetivo

Descrever fundamentos da linguagem PHP.

## Conceitos abordados

- Sintaxe básica
- Tipos e controle de fluxo

## Pré-requisitos

PHP instalado localmente.

## Como executar

Salve o exemplo abaixo em um arquivo local `introducao.php` e execute,
no diretório onde o salvou:

```sh
php -l introducao.php
php introducao.php
```

O primeiro comando verifica a sintaxe. O segundo deve imprimir `Total: 60`.

## Exemplos

```php
<?php

declare(strict_types=1);

function calcularTotal(int $precoUnitario, int $quantidade): int
{
    if ($precoUnitario < 0 || $quantidade < 0) {
        throw new InvalidArgumentException('Valores devem ser nao negativos.');
    }

    return $precoUnitario * $quantidade;
}

echo 'Total: ' . calcularTotal(20, 3) . PHP_EOL;
```

O exemplo trabalha com valores inteiros; não representa cálculo financeiro
com frações. Experimente quantidade zero e confirme total zero. Um argumento
negativo deve provocar `InvalidArgumentException` em vez de produzir um total inválido.

## Exercícios

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
