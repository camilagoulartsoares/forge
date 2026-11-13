<?php

declare(strict_types=1);

require_once __DIR__ . '/repositorio-memoria.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use Forge\Aplicacao\Tarefa;
use Forge\Aplicacao\TarefasEmMemoria;
use Forge\Aplicacao\ConcluirTarefa;

$tarefa = new Tarefa(1, false, 7);
$repositorio = new TarefasEmMemoria([$tarefa]);
verificarIgual(null, $repositorio->buscar(2));
$resultado = (new ConcluirTarefa($repositorio))->executarAutorizada(1, 7);
verificarIgual($resultado, $repositorio->buscar(1));
verificarIgual(false, $tarefa->concluida());
verificarIgual(null, (new TarefasEmMemoria())->buscar(1));
foreach ([[$tarefa, $tarefa], [null], [$tarefa, new stdClass()]] as $carga) {
    verificarExcecao(InvalidArgumentException::class, function () use ($carga) { new TarefasEmMemoria($carga); });
}
echo "OK: repositorio de tarefas em memoria\n";
