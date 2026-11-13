<?php

declare(strict_types=1);

require_once __DIR__ . '/repositorio-memoria.php';
require_once __DIR__ . '/../routes/ler-identificador.php';

// Demonstracao local: o usuario 7 e fixo e ficticio, nao representa autenticacao real.
if (count($argv) !== 2) {
    fwrite(STDERR, "Uso: php 02-symfony/services/executar.php <id>\n");
    exit(2);
}
try {
    $id = Forge\Rotas\lerIdentificador($argv[1]);
    $repositorio = new Forge\Aplicacao\TarefasEmMemoria([new Forge\Aplicacao\Tarefa(1, false, 7)]);
    $tarefa = (new Forge\Aplicacao\ConcluirTarefa($repositorio))->executarAutorizada($id, 7);
    echo json_encode(['id' => $tarefa->id(), 'concluida' => $tarefa->concluida()], JSON_THROW_ON_ERROR) . "\n";
} catch (InvalidArgumentException $erro) {
    fwrite(STDERR, "Identificador invalido.\n");
    exit(2);
} catch (Forge\Aplicacao\TarefaNaoEncontrada $erro) {
    fwrite(STDERR, "Tarefa nao encontrada.\n");
    exit(1);
}
