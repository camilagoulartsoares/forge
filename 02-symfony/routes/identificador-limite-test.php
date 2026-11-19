<?php

declare(strict_types=1);

namespace Forge\Rotas {
    function preg_match(string $padrao, string $texto)
    {
        $GLOBALS['verificacoesDeFormato'] = ($GLOBALS['verificacoesDeFormato'] ?? 0) + 1;
        return \preg_match($padrao, $texto);
    }
}
namespace {
    require_once __DIR__ . '/ler-identificador.php';
    require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';
    $GLOBALS['verificacoesDeFormato'] = 0;
    verificarExcecao(Forge\Identidade\IdentificadorInvalido::class, function () {
        Forge\Rotas\lerIdentificador(str_repeat('9', 1000000));
    });
    verificarIgual(0, $GLOBALS['verificacoesDeFormato']);
    verificarIgual(17, Forge\Rotas\lerIdentificador('17'));
    verificarIgual(1, $GLOBALS['verificacoesDeFormato']);
    echo "OK: rota extensa rejeitada antes da verificacao de formato\n";
}
