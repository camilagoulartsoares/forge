<?php

declare(strict_types=1);

namespace Forge\Aplicacao;

require_once __DIR__ . '/concluir-tarefa.php';

final class TarefasEmMemoria implements RepositorioTarefas
{
    private array $tarefas = [];

    public function __construct(array $tarefas = [])
    {
        foreach ($tarefas as $tarefa) {
            if (!$tarefa instanceof Tarefa || isset($this->tarefas[$tarefa->id()])) {
                throw new \InvalidArgumentException('Carga inicial invalida ou duplicada.');
            }
            $this->tarefas[$tarefa->id()] = $tarefa;
        }
    }

    public function buscar(int $id): ?Tarefa
    {
        return $this->tarefas[$id] ?? null;
    }

    public function salvar(Tarefa $tarefa): void
    {
        $this->tarefas[$tarefa->id()] = $tarefa;
    }
}
