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
