<?php

declare(strict_types=1);

namespace Forge\Pedidos {
    // Simula o fallback documentado de tempnam sem depender das permissoes do SO.
    function tempnam(string $diretorio, string $prefixo)
    {
        $caminho = \tempnam(\sys_get_temp_dir(), $prefixo);
        $GLOBALS['temporarioExterno'] = $caminho;
        return $caminho;
    }
}

namespace {
    require_once __DIR__ . '/PedidosEmJson.php';
    require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';
    $diretorio = sys_get_temp_dir() . '/forge-temp-' . bin2hex(random_bytes(8));
    if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
    $arquivo = $diretorio . '/store.json';
    file_put_contents($arquivo, '[]');
    try {
        $repo = new Forge\Pedidos\PedidosEmJson($arquivo);
        verificarExcecao(RuntimeException::class, function () use ($repo) {
            $repo->salvar(new Forge\Pedidos\Pedido('1', [['codigo' => 'A', 'quantidade' => 1]]));
        });
        verificarIgual('[]', file_get_contents($arquivo));
        verificarIgual(false, file_exists($GLOBALS['temporarioExterno']));
    } finally {
        if (isset($GLOBALS['temporarioExterno']) && is_file($GLOBALS['temporarioExterno'])) unlink($GLOBALS['temporarioExterno']);
        foreach (glob($diretorio . '/*') as $caminho) unlink($caminho);
        rmdir($diretorio);
    }
    echo "OK: fallback externo nao compromete escrita atomica\n";
}
