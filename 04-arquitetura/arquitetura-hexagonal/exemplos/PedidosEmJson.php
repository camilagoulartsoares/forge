<?php

declare(strict_types=1);

namespace Forge\Pedidos;

require_once __DIR__ . '/ArmazenamentoPedidos.php';

/** Adaptador didatico para arquivos locais; todos os escritores devem respeitar o mesmo lock. */
final class PedidosEmJson implements ArmazenamentoPedidos
{
    private string $arquivo;

    public function __construct(string $arquivo)
    {
        $diretorio = realpath(dirname($arquivo));
        if ($diretorio === false || !is_dir($diretorio) || is_dir($arquivo)) {
            throw new \InvalidArgumentException('Diretorio de armazenamento invalido.');
        }
        $this->arquivo = $diretorio . DIRECTORY_SEPARATOR . basename($arquivo);
    }

    public function existe(string $id): bool
    {
        return $this->buscar($id) !== null;
    }

    public function buscar(string $id): ?Pedido
    {
        return $this->comLock(function () use ($id) { return $this->ler()[$id] ?? null; });
    }

    public function salvar(Pedido $pedido): void
    {
        $this->comLock(function () use ($pedido) {
            $pedidos = $this->ler();
            if (isset($pedidos[$pedido->id()])) {
                throw new \DomainException('Pedido ja existe.');
            }
            $pedidos[$pedido->id()] = $pedido;
            $registros = array_map(function (Pedido $item): array {
                return ['id' => $item->id(), 'itens' => $item->itens()];
            }, array_values($pedidos));
            $json = json_encode($registros, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            $temporario = tempnam(dirname($this->arquivo), 'pedidos-');
            if ($temporario === false) throw new \RuntimeException('Falha ao criar arquivo temporario.');
            try {
                if (file_put_contents($temporario, $json) !== strlen($json)
                    || !rename($temporario, $this->arquivo)) {
                    throw new \RuntimeException('Falha ao persistir pedidos.');
                }
            } finally {
                if (is_file($temporario)) unlink($temporario);
            }
        });
    }

    private function comLock(callable $operacao)
    {
        $lock = fopen($this->arquivo . '.lock', 'c');
        if ($lock === false) throw new \RuntimeException('Falha ao abrir lock.');
        try {
            if (!flock($lock, LOCK_EX)) throw new \RuntimeException('Falha ao adquirir lock.');
            return $operacao();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function ler(): array
    {
        // Limpa o stat cache porque outro processo pode ter criado o arquivo.
        clearstatcache(true, $this->arquivo);
        if (!file_exists($this->arquivo)) return [];
        $json = file_get_contents($this->arquivo);
        if ($json === false) throw new \RuntimeException('Falha ao ler pedidos.');
        $registros = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        if (!is_array($registros)) throw new \UnexpectedValueException('Esperada lista de pedidos.');
        $pedidos = [];
        foreach ($registros as $registro) {
            if (!$registro instanceof \stdClass || !isset($registro->id, $registro->itens)
                || !is_string($registro->id) || !is_array($registro->itens)) {
                throw new \UnexpectedValueException('Registro de pedido invalido.');
            }
            $itens = array_map(function ($item): array {
                if (!$item instanceof \stdClass) throw new \UnexpectedValueException('Item persistido invalido.');
                return (array) $item;
            }, $registro->itens);
            $pedido = new Pedido($registro->id, $itens);
            if ($pedido->id() !== $registro->id || isset($pedidos[$pedido->id()])) {
                throw new \UnexpectedValueException('Identificador persistido invalido ou duplicado.');
            }
            $pedidos[$pedido->id()] = $pedido;
        }
        return $pedidos;
    }
}
