<?php

declare(strict_types=1);

namespace Forge\Json;

/** Usa o parser nativo para a gramatica e rejeita nomes duplicados apos decodificar escapes. */
function decodificarJson(string $texto)
{
    $resultado = json_decode($texto, false, 512, JSON_THROW_ON_ERROR);
    $padrao = <<<'REGEX'
/"[^"\\]*(?:\\.[^"\\]*)*"|[{}\[\]:,]/s
REGEX;
    if (preg_match_all($padrao, $texto, $tokens) === false) {
        throw new \JsonException('Nao foi possivel verificar as chaves JSON.');
    }
    $pilha = [];
    foreach ($tokens[0] as $indice => $token) {
        if ($token === '{' || $token === '[') {
            $pilha[] = $token === '{' ? [] : null;
        } elseif ($token === '}' || $token === ']') {
            array_pop($pilha);
        } elseif ($token[0] === '"' && ($tokens[0][$indice + 1] ?? null) === ':') {
            $chave = json_decode($token, false, 512, JSON_THROW_ON_ERROR);
            $nivel = count($pilha) - 1;
            if (isset($pilha[$nivel][$chave])) throw new \JsonException('Chave JSON duplicada.');
            $pilha[$nivel][$chave] = true;
        }
    }
    return $resultado;
}
