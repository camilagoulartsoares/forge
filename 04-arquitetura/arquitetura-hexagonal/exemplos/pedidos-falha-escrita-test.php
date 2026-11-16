<?php

declare(strict_types=1);

namespace Forge\Pedidos {
    function file_put_contents(string $caminho, string $dados)
    {
        if (($GLOBALS['falhaEscrita'] ?? '') === 'parcial') return \file_put_contents($caminho, substr($dados, 0, 3));
        return \file_put_contents($caminho, $dados);
    }

    function rename(string $origem, string $destino): bool
    {
        if (($GLOBALS['falhaEscrita'] ?? '') === 'rename') return false;
        return \rename($origem, $destino);
    }
}

namespace {
    require_once __DIR__ . '/PedidosEmJson.php';
    require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';
    $diretorio = sys_get_temp_dir() . '/forge-io-' . bin2hex(random_bytes(8));
    if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
    $arquivo = $diretorio . '/store.json';
    try {
        $repo = new Forge\Pedidos\PedidosEmJson($arquivo);
        $repo->salvar(new Forge\Pedidos\Pedido('original', [['codigo' => 'A', 'quantidade' => 1]]));
        $original = file_get_contents($arquivo);
        foreach (['parcial', 'rename'] as $falha) {
            $GLOBALS['falhaEscrita'] = $falha;
            verificarExcecao(RuntimeException::class, function () use ($repo) {
                $repo->salvar(new Forge\Pedidos\Pedido('nova', [['codigo' => 'B', 'quantidade' => 1]]));
            });
            verificarIgual($original, file_get_contents($arquivo));
            verificarIgual([], glob($diretorio . '/pedidos-*'));
            verificarIgual(null, (new Forge\Pedidos\PedidosEmJson($arquivo))->buscar('nova'));
        }
        $GLOBALS['falhaEscrita'] = '';
        (new Forge\Pedidos\PedidosEmJson($arquivo))->salvar(new Forge\Pedidos\Pedido('nova', [['codigo' => 'B', 'quantidade' => 1]]));
        verificarIgual(true, $repo->existe('original'));
        verificarIgual(true, $repo->existe('nova'));
    } finally {
        unset($GLOBALS['falhaEscrita']);
        foreach (glob($diretorio . '/*') as $caminho) unlink($caminho);
        rmdir($diretorio);
    }
    echo "OK: falhas de escrita preservam dados e liberam recursos\n";
}
