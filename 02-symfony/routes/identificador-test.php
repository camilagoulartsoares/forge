<?php

declare(strict_types=1);

require_once __DIR__ . '/ler-identificador.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

verificarIgual(1, Forge\Rotas\lerIdentificador('1'));
if (PHP_INT_SIZE >= 8) verificarIgual(9007199254740991, Forge\Rotas\lerIdentificador('9007199254740991'));
foreach (['', '0', '01', '-1', '+1', '1.0', '1e2', ' 1', "1\n", '9007199254740992', str_repeat('9', 1000)] as $valor) {
    verificarExcecao(InvalidArgumentException::class, function () use ($valor) { Forge\Rotas\lerIdentificador($valor); });
}
echo "OK: identificadores de rota sem coercao\n";
