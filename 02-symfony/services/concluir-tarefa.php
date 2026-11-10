<?php

declare(strict_types=1);

namespace Forge\Aplicacao;

use InvalidArgumentException;
use OutOfBoundsException;

final class Tarefa
{
    private int $id;
    private bool $concluida;

    public function __construct(int $id, bool $concluida = false)
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Identificador deve ser positivo.');
        }
        $this->id = $id;
        $this->concluida = $concluida;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function concluida(): bool
    {
        return $this->concluida;
    }

    public function concluir(): self
    {
        return $this->concluida ? $this : new self($this->id, true);
    }
}

interface RepositorioTarefas
{
    public function buscar(int $id): ?Tarefa;
    public function salvar(Tarefa $tarefa): void;
}

final class ConcluirTarefa
{
    private RepositorioTarefas $repositorio;

    public function __construct(RepositorioTarefas $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    public function executar(int $id): Tarefa
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Identificador deve ser positivo.');
        }
        $tarefa = $this->repositorio->buscar($id);
        if ($tarefa === null) {
            throw new OutOfBoundsException('Tarefa nao encontrada.');
        }
        if ($tarefa->concluida()) {
            return $tarefa;
        }
        $resultado = $tarefa->concluir();
        $this->repositorio->salvar($resultado);

        return $resultado;
    }
}
