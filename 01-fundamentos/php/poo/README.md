# Programação Orientada a Objetos

## Objetivo

Estudar abstração, encapsulamento, herança e polimorfismo em PHP.

## Conceitos abordados

- Classes e objetos
- Contratos e composição

## Pré-requisitos

Introdução ao PHP.

## Como executar

Salve o exemplo em `estoque.php` e execute `php estoque.php` no mesmo diretório.
O resultado esperado é `Saldo: 3`. Requer PHP 7.4 ou superior.

## Exemplos

```php
<?php

declare(strict_types=1);

final class Estoque
{
    private int $saldo = 0;

    public function adicionar(int $quantidade): void
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException('Quantidade deve ser positiva.');
        }

        $this->saldo += $quantidade;
    }

    public function saldo(): int
    {
        return $this->saldo;
    }
}

$estoque = new Estoque();
$estoque->adicionar(3);
echo 'Saldo: ' . $estoque->saldo() . PHP_EOL;
```

O saldo privado só muda pela operação validada da classe. Como exercício,
tente adicionar zero e um número negativo: ambos devem ser rejeitados sem
alterar o saldo. Crie duas instâncias e confira que os saldos são independentes.

## Exercícios

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
