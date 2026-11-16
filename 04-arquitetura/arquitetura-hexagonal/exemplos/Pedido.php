<?php

declare(strict_types=1);

namespace Forge\Pedidos;

use InvalidArgumentException;


final class Pedido
{
    private string $id;
    private array $itens;

    public function __construct(string $id, array $itens)
    {
        if (preg_match('//u', $id) !== 1 || trim($id) === '' || $itens === [] || array_keys($itens) !== range(0, count($itens) - 1)) {
            throw new InvalidArgumentException('Pedido exige identificador e itens.');
        }

        $normalizados = [];
        foreach ($itens as $item) {
            if (!is_array($item) || !isset($item['codigo'], $item['quantidade'])
                || !is_string($item['codigo']) || preg_match('//u', $item['codigo']) !== 1 || trim($item['codigo']) === ''
                || !is_int($item['quantidade']) || $item['quantidade'] <= 0) {
                throw new InvalidArgumentException('Item invalido.');
            }
            $normalizados[] = ['codigo' => trim($item['codigo']), 'quantidade' => $item['quantidade']];
        }
        $this->id = trim($id);
        $this->itens = $normalizados;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function itens(): array
    {
        return $this->itens;
    }
}
