<?php

declare(strict_types=1);

require_once __DIR__ . '/identificador.php';
require_once __DIR__ . '/../services/concluir-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

foreach ([1, 9007199254740991] as $id) {
    verificarIgual(true, Forge\Identidade\identificadorValido($id));
    verificarIgual($id, (new Forge\Aplicacao\Tarefa($id))->id());
}
foreach ([0, -1, 1.5, '1', null, 9007199254740992] as $id) {
    verificarIgual(false, Forge\Identidade\identificadorValido($id));
}
verificarExcecao(InvalidArgumentException::class, function () { new Forge\Aplicacao\Tarefa(9007199254740992); });
echo "OK: identificadores PHP\n";
