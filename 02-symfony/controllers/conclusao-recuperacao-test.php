<?php

declare(strict_types=1);

require_once __DIR__ . '/concluir-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$repo = new class implements Forge\Aplicacao\RepositorioTarefas {
    public Forge\Aplicacao\Tarefa $tarefa;
    public int $tentativas = 0;
    public function __construct() { $this->tarefa = new Forge\Aplicacao\Tarefa(1, false, 7); }
    public function buscar(int $id): ?Forge\Aplicacao\Tarefa { return $id === 1 ? $this->tarefa : null; }
    public function salvar(Forge\Aplicacao\Tarefa $tarefa): void {
        if (++$this->tentativas === 1) throw new RuntimeException('Detalhe privado do armazenamento');
        $this->tarefa = $tarefa;
    }
};
$servico = new Forge\Aplicacao\ConcluirTarefa($repo);
$original = $repo->tarefa;
$falha = Forge\Http\concluirTarefaHttp('1', 7, $servico);
verificarIgual(['status' => 500, 'cabecalhos' => ['Content-Type' => 'application/json'],
    'corpo' => ['erro' => 'erro_interno']], $falha);
verificarIgual($original, $repo->tarefa);
verificarIgual(false, $original->concluida());
$sucesso = Forge\Http\concluirTarefaHttp('1', 7, $servico);
verificarIgual(200, $sucesso['status']);
verificarIgual(['id' => 1, 'concluida' => true], $sucesso['corpo']);
verificarIgual(true, $repo->tarefa->concluida());
verificarIgual(false, $original->concluida());
verificarIgual($sucesso, Forge\Http\concluirTarefaHttp('1', 7, $servico));
verificarIgual(2, $repo->tentativas);
echo "OK: falha HTTP preserva estado e permite repeticao idempotente\n";
