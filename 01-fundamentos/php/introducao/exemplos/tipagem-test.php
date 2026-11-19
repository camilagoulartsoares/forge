<?php

declare(strict_types=1);

require_once __DIR__ . '/chamada-coerciva.php';
require_once __DIR__ . '/../../testes-automatizados/exemplos/verificacoes.php';

verificarIgual(6, calcularTotal(3, 2));
verificarExcecao(TypeError::class, function () { calcularTotal('3', 2); });
verificarIgual(6, Forge\Tipos\totalComCoercao('3', '2'));
verificarExcecao(TypeError::class, function () { Forge\Tipos\totalComCoercao('tres', '2'); });
verificarExcecao(InvalidArgumentException::class, function () { Forge\Tipos\totalComCoercao('-3', '2'); });
echo "OK: tipagem estrita e coerciva sem alterar a regra de total\n";
