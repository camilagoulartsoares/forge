<?php

declare(strict_types=1);

require_once __DIR__ . '/ler-criacao-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

verificarIgual(['titulo' => 'Estudar'], Forge\Http\lerCriacaoTarefa('{"titulo":"  Estudar ","id":99,"concluida":true}', 'Application/JSON; charset=utf-8'));
foreach (['{', '[]', 'null', '1', '"texto"'] as $json) {
    verificarExcecao(Forge\Http\CorpoJsonInvalido::class, function () use ($json) { Forge\Http\lerCriacaoTarefa($json); });
}
foreach (['{}', '{"titulo":null}', '{"titulo":[]}', '{"titulo":" "}'] as $json) {
    verificarExcecao(Forge\Http\EntradaInvalida::class, function () use ($json) { Forge\Http\lerCriacaoTarefa($json); });
}
verificarExcecao(Forge\Http\TipoConteudoNaoSuportado::class, function () { Forge\Http\lerCriacaoTarefa('{}', 'text/plain'); });
echo "OK: entrada HTTP de criacao\n";

foreach (['{"titulo":"A","titulo":"B"}', '{"titulo":"A","ti\u0074ulo":"B"}',
    '{"titulo":"A","extra":{"id":1,"id":2}}'] as $duplicado) {
    verificarExcecao(Forge\Http\CorpoJsonInvalido::class, function () use ($duplicado) {
        Forge\Http\lerCriacaoTarefa($duplicado);
    });
}
