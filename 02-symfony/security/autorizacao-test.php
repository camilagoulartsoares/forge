<?php

declare(strict_types=1);

require_once __DIR__ . '/autorizacao.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use function Forge\Autorizacao\podeAlterarTarefa;
use function Forge\Autorizacao\filtrarTarefasAutorizadas;

verificarIgual(true, podeAlterarTarefa(1, 1));
foreach ([[null, 1], [1, 2], [0, 0], [-1, -1], [1, 0]] as [$usuario, $dono]) {
    verificarIgual(false, podeAlterarTarefa($usuario, $dono));
}
$tarefas = [4 => ['id' => 1, 'proprietarioId' => 1], 8 => ['id' => 2, 'proprietarioId' => 2],
    9 => ['id' => 3, 'proprietarioId' => '1'], 10 => null, 11 => ['id' => 4]];
verificarIgual([['id' => 1, 'proprietarioId' => 1]], filtrarTarefasAutorizadas(1, $tarefas));
verificarIgual([], filtrarTarefasAutorizadas(null, $tarefas));
verificarIgual(5, count($tarefas));
echo 'OK: autorizacao por proprietario' . PHP_EOL;

verificarIgual(true, podeAlterarTarefa(9007199254740991, 9007199254740991));
verificarIgual(false, podeAlterarTarefa(9007199254740992, 9007199254740992));
verificarIgual([], filtrarTarefasAutorizadas(9007199254740992, [['proprietarioId' => 9007199254740992]]));
require_once __DIR__ . '/../services/concluir-tarefa.php';
verificarExcecao(InvalidArgumentException::class, function () {
    new Forge\Aplicacao\Tarefa(1, false, 9007199254740992);
});
verificarIgual(9007199254740991, (new Forge\Aplicacao\Tarefa(1, false, 9007199254740991))->proprietario());
