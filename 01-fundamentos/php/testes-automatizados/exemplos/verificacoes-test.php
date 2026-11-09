<?php

declare(strict_types=1);

require_once __DIR__ . '/verificacoes.php';

// These checks intentionally do not use the assertion helpers under test.
function exigirFalha(callable $acao, string $mensagem, ?Throwable $causa = null): void
{
    try {
        $acao();
    } catch (RuntimeException $erro) {
        if (strpos($erro->getMessage(), $mensagem) === false || $erro->getPrevious() !== $causa) {
            throw new LogicException('Diagnostico de falha inesperado.', 0, $erro);
        }

        return;
    }

    throw new LogicException('Uma verificacao incorreta foi aceita.');
}

verificarIgual(['a' => 1], ['a' => 1]);
exigirFalha(function (): void {
    verificarIgual(1, '1');
}, 'Esperado');
exigirFalha(function (): void {
    verificarExcecao(RuntimeException::class, function (): void {
        // No exception: the helper must reject this false positive.
    });
}, 'Excecao nao lancada');

$causa = new InvalidArgumentException('Causa original');
exigirFalha(function () use ($causa): void {
    verificarExcecao(TypeError::class, function () use ($causa): void {
        throw $causa;
    });
}, 'Excecao inesperada', $causa);

verificarExcecao(LogicException::class, function (): void {
    throw new InvalidArgumentException('Subclasse aceita');
});
verificarExcecao(Throwable::class, function (): void {
    throw new TypeError('Erros tambem sao Throwable');
});

echo 'OK: verificacoes' . PHP_EOL;
