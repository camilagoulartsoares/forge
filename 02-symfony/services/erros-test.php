<?php

declare(strict_types=1);

require_once __DIR__ . '/concluir-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$repositorio = new class implements Forge\Aplicacao\RepositorioTarefas {
    public function buscar(int $id): ?Forge\Aplicacao\Tarefa { return null; }
    public function salvar(Forge\Aplicacao\Tarefa $tarefa): void { throw new LogicException('Nao deve salvar.'); }
};
$servico = new Forge\Aplicacao\ConcluirTarefa($repositorio);
verificarExcecao(Forge\Aplicacao\TarefaNaoEncontrada::class, function () use ($servico) { $servico->executar(1); });
verificarExcecao(Forge\Aplicacao\AcessoNegado::class, function () use ($servico) { $servico->executarAutorizada(1, null); });
verificarIgual(true, is_subclass_of(Forge\Aplicacao\AcessoNegado::class, DomainException::class));
verificarIgual(true, is_subclass_of(Forge\Aplicacao\TarefaNaoEncontrada::class, OutOfBoundsException::class));
echo "OK: erros de aplicacao tipados\n";
