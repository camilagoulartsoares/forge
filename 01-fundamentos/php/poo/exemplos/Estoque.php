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

        if ($quantidade > PHP_INT_MAX - $this->saldo) {
            throw new OverflowException('Saldo excede o limite de inteiros.');
        }

        $this->saldo += $quantidade;
    }

    public function retirar(int $quantidade): void
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException('Quantidade deve ser positiva.');
        }

        if ($quantidade > $this->saldo) {
            throw new UnderflowException('Saldo insuficiente.');
        }

        $this->saldo -= $quantidade;
    }

    public function transferirPara(Estoque $destino, int $quantidade): void
    {
        if ($destino === $this || $quantidade <= 0) {
            throw new InvalidArgumentException('Destino distinto e quantidade positiva sao obrigatorios.');
        }
        if ($quantidade > $this->saldo) {
            throw new UnderflowException('Saldo insuficiente.');
        }
        if ($quantidade > PHP_INT_MAX - $destino->saldo) {
            throw new OverflowException('Saldo do destino excederia o limite.');
        }

        $this->saldo -= $quantidade;
        $destino->saldo += $quantidade;
    }

    public function saldo(): int
    {
        return $this->saldo;
    }
}
