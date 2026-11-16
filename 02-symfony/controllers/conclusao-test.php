<?php

declare(strict_types=1);

require_once __DIR__ . '/concluir-tarefa.php';
require_once __DIR__ . '/../services/repositorio-memoria.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$repo = new Forge\Aplicacao\TarefasEmMemoria([new Forge\Aplicacao\Tarefa(1, false, 7)]);
$servico = new Forge\Aplicacao\ConcluirTarefa($repo);
foreach ([['1', null, 403], ['invalido', null, 403], ['1', 8, 403], ['1e2', 7, 422], ['9', 7, 404]] as [$id, $usuario, $status]) {
    verificarIgual($status, Forge\Http\concluirTarefaHttp($id, $usuario, $servico)['status']);
    verificarIgual(false, $repo->buscar(1)->concluida());
}
$resposta = Forge\Http\concluirTarefaHttp('1', 7, $servico);
verificarIgual(['status' => 200, 'cabecalhos' => ['Content-Type' => 'application/json'],
    'corpo' => ['id' => 1, 'concluida' => true]], $resposta);
verificarIgual($resposta, Forge\Http\concluirTarefaHttp('1', 7, $servico));
$falho = new class implements Forge\Aplicacao\RepositorioTarefas {
    public function buscar(int $id): ?Forge\Aplicacao\Tarefa { return new Forge\Aplicacao\Tarefa($id, false, 7); }
    public function salvar(Forge\Aplicacao\Tarefa $tarefa): void { throw new RuntimeException('Dados internos'); }
};
verificarIgual(500, Forge\Http\concluirTarefaHttp('1', 7, new Forge\Aplicacao\ConcluirTarefa($falho))['status']);
echo "OK: controller HTTP de conclusao\n";
