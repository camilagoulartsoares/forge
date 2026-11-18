<?php

declare(strict_types=1);

require_once __DIR__ . '/criar-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$chamadas = [];
$criar = function (array $entrada) use (&$chamadas): array {
    $chamadas[] = $entrada;
    return ['id' => 12, 'titulo' => $entrada['titulo'], 'concluida' => false, 'proprietarioId' => 7];
};
$resposta = Forge\Http\criarTarefaHttp('{"titulo":" Estudar ","proprietarioId":99}', 'application/json', $criar);
verificarIgual(201, $resposta['status']);
verificarIgual('/tarefas/12', $resposta['cabecalhos']['Location']);
verificarIgual(['id' => 12, 'titulo' => 'Estudar', 'concluida' => false], $resposta['corpo']);
verificarIgual([['titulo' => 'Estudar']], $chamadas);
foreach ([['{', 'application/json', 400], ['{}', 'application/json', 422], ['{}', 'text/plain', 415]] as [$json, $tipo, $status]) {
    verificarIgual($status, Forge\Http\criarTarefaHttp($json, $tipo, $criar)['status']);
}
verificarIgual(1, count($chamadas));
foreach ([null, ['id' => 0], ['id' => 1, 'titulo' => ' Valida ', 'concluida' => false]] as $invalida) {
    verificarIgual(500, Forge\Http\criarTarefaHttp('{"titulo":"Valida"}', 'application/json', function () use ($invalida) { return $invalida; })['status']);
}
verificarIgual(['erro' => 'erro_interno'], Forge\Http\criarTarefaHttp('{"titulo":"Valida"}', 'application/json', function () {
    throw new JsonException('Detalhe de persistencia');
})['corpo']);
echo "OK: controller HTTP de criacao\n";

foreach ([null, [], 42, "\xff", str_repeat('x', 121), ' sem normalizar '] as $tituloInvalido) {
    verificarIgual(500, Forge\Http\criarTarefaHttp('{"titulo":"Valida"}', 'application/json', function () use ($tituloInvalido) {
        return ['id' => 1, 'titulo' => $tituloInvalido, 'concluida' => false];
    })['status']);
}
$tituloLimite = str_repeat("\xf0\x9f\x98\x80", 60);
verificarIgual(201, Forge\Http\criarTarefaHttp('{"titulo":"Valida"}', 'application/json', function () use ($tituloLimite) {
    return ['id' => 1, 'titulo' => $tituloLimite, 'concluida' => false];
})['status']);

$noLimite = str_pad('{"titulo":"Limite"}', 16384, ' ');
verificarIgual(201, Forge\Http\criarTarefaHttp($noLimite, 'application/json', $criar)['status']);
$antesDoLimite = count($chamadas);
verificarIgual(413, Forge\Http\criarTarefaHttp($noLimite . ' ', 'application/json', $criar)['status']);
verificarIgual($antesDoLimite, count($chamadas));
