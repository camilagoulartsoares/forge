<?php

declare(strict_types=1);

require_once __DIR__ . '/json-estrito.php';
require_once __DIR__ . '/../../testes-automatizados/exemplos/verificacoes.php';

use function Forge\Json\decodificarJson;

foreach (['{"a":1,"a":2}', '{"a":1,"\u0061":2}', '[{"x":{"a":1,"a":2}}]'] as $texto) {
    verificarExcecao(JsonException::class, function () use ($texto) { decodificarJson($texto); });
}
verificarIgual(2, count(decodificarJson('[{"id":1},{"id":2}]')));
verificarIgual('brace } and colon :', decodificarJson('{"texto":"brace } and colon :"}')->texto);
verificarIgual('quote " and slash \\', decodificarJson('{"texto":"quote \" and slash \\\\"}')->texto);
verificarIgual(2, count((array) decodificarJson('{"a":1,"A":2}')));
verificarIgual(null, decodificarJson('null'));
verificarExcecao(JsonException::class, function () { decodificarJson('{'); });
echo "OK: JSON estrito sem chaves ambiguas\n";

foreach (['1e999', '-1e999', '{"x":[1e309]}'] as $numero) {
    verificarExcecao(JsonException::class, function () use ($numero) { decodificarJson($numero); });
}
verificarIgual(1e308, decodificarJson('1e308'));

$textoEscapado = str_repeat(chr(92), 10000);
$documento = json_encode(['texto' => $textoEscapado, 'outro' => '{:}'], JSON_THROW_ON_ERROR);
verificarIgual($textoEscapado, decodificarJson($documento)->texto);

$casosJson = json_decode(file_get_contents(__DIR__ . '/../../testes-automatizados/exemplos/casos-json.json'), true, 512, JSON_THROW_ON_ERROR);
foreach ($casosJson as $casoJson) {
    if ($casoJson['valido']) {
        decodificarJson($casoJson['texto']);
    } else {
        verificarExcecao(JsonException::class, function () use ($casoJson) { decodificarJson($casoJson['texto']); });
    }
}
