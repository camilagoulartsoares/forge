<?php

declare(strict_types=1);

function verificarIgual($esperado, $obtido): void
{
    if ($esperado !== $obtido) {
        throw new RuntimeException('Esperado ' . var_export($esperado, true) . ', obtido ' . var_export($obtido, true));
    }
}

function verificarExcecao(string $classe, callable $acao): void
{
    try {
        $acao();
    } catch (Throwable $erro) {
        if ($erro instanceof $classe) {
            return;
        }

        throw new RuntimeException('Excecao inesperada: ' . get_class($erro), 0, $erro);
    }

    throw new RuntimeException('Excecao nao lancada: ' . $classe);
}
