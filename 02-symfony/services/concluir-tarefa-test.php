<?php

declare(strict_types=1);

require_once __DIR__ . '/concluir-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use Forge\Aplicacao\ConcluirTarefa;
use Forge\Aplicacao\RepositorioTarefas;
use Forge\Aplicacao\Tarefa;

$repo = new class implements RepositorioTarefas {
    public array $tarefas = [];
    public int $gravacoes = 0;
    public bool $falhar = false;

    public function buscar(int $id): ?Tarefa
    {
        return $this->tarefas[$id] ?? null;
    }

    public function salvar(Tarefa $tarefa): void
    {
        if ($this->falhar) {
            throw new RuntimeException('Persistencia indisponivel');
        }
        $this->gravacoes++;
        $this->tarefas[$tarefa->id()] = $tarefa;
    }
};
$repo->tarefas[1] = new Tarefa(1);
$original = $repo->tarefas[1];
$servico = new ConcluirTarefa($repo);
verificarIgual(true, $servico->executar(1)->concluida());
verificarIgual(true, $servico->executar(1)->concluida());
verificarIgual(1, $repo->gravacoes);
verificarIgual(false, $original->concluida());
verificarExcecao(OutOfBoundsException::class, function () use ($servico): void { $servico->executar(2); });
verificarExcecao(InvalidArgumentException::class, function () use ($servico): void { $servico->executar(0); });
$repo->tarefas[3] = new Tarefa(3);
$repo->falhar = true;
verificarExcecao(RuntimeException::class, function () use ($servico): void { $servico->executar(3); });
verificarIgual(false, $repo->tarefas[3]->concluida());
verificarIgual(1, $repo->gravacoes);
$repo->falhar = false;
$repo->tarefas[7] = new Tarefa(7, false, 10);
$repo->tarefas[8] = new Tarefa(8, true, 20);
$repo->tarefas[9] = new Tarefa(9);
foreach ([[7, null], [7, 20], [8, 10], [9, 10]] as [$id, $usuario]) {
    verificarExcecao(DomainException::class, function () use ($servico, $id, $usuario): void {
        $servico->executarAutorizada($id, $usuario);
    });
}
verificarIgual(false, $repo->tarefas[7]->concluida());
verificarIgual(1, $repo->gravacoes);
verificarIgual(true, $servico->executarAutorizada(7, 10)->concluida());
verificarIgual(10, $repo->tarefas[7]->proprietario());
$servico->executarAutorizada(7, 10);
verificarIgual(2, $repo->gravacoes);
$incorreto = new class implements RepositorioTarefas {
    public int $gravacoes = 0;

    public function buscar(int $id): ?Tarefa
    {
        return new Tarefa(999, false, 10);
    }

    public function salvar(Tarefa $tarefa): void
    {
        $this->gravacoes++;
    }
};
$inconsistente = new ConcluirTarefa($incorreto);
verificarExcecao(UnexpectedValueException::class, function () use ($inconsistente): void {
    $inconsistente->executar(1);
});
verificarExcecao(UnexpectedValueException::class, function () use ($inconsistente): void {
    $inconsistente->executarAutorizada(1, 10);
});
verificarIgual(0, $incorreto->gravacoes);
echo 'OK: conclusao idempotente' . PHP_EOL;
