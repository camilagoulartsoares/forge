<?php

declare(strict_types=1);

namespace Forge\Json;

/** Usa o parser nativo para a gramatica e rejeita nomes duplicados apos decodificar escapes. */
function decodificarJson(string $texto)
{
    $resultado = json_decode($texto, false, 512, JSON_THROW_ON_ERROR);
    $pilha = [];
    $tamanho = strlen($texto);
    for ($posicao = 0; $posicao < $tamanho; $posicao++) {
        $caractere = $texto[$posicao];
        if ($caractere === '{' || $caractere === '[') {
            $pilha[] = $caractere === '{' ? [] : null;
        } elseif ($caractere === '}' || $caractere === ']') {
            array_pop($pilha);
        } elseif ($caractere === '"') {
            $inicio = $posicao++;
            // A gramatica ja foi validada: escapes e fechamento sempre existem.
            while ($texto[$posicao] !== '"') {
                $posicao += $texto[$posicao] === chr(92) ? 2 : 1;
            }
            $proximo = $posicao + 1;
            while ($proximo < $tamanho && strpos(" \t\r\n", $texto[$proximo]) !== false) $proximo++;
            if ($proximo < $tamanho && $texto[$proximo] === ':') {
                $chave = json_decode(substr($texto, $inicio, $posicao - $inicio + 1), false, 512, JSON_THROW_ON_ERROR);
                $nivel = count($pilha) - 1;
                if (isset($pilha[$nivel][$chave])) throw new \JsonException('Chave JSON duplicada.');
                $pilha[$nivel][$chave] = true;
            }
        }
    }
    $pendentes = [$resultado];
    while ($pendentes !== []) {
        $valor = array_pop($pendentes);
        if (is_float($valor) && !is_finite($valor)) throw new \JsonException('Numero fora do intervalo finito.');
        if (is_array($valor) || $valor instanceof \stdClass) {
            foreach ((array) $valor as $filho) $pendentes[] = $filho;
        }
    }
    return $resultado;
}
