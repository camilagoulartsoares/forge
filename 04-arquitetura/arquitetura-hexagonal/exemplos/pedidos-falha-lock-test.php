<?php

declare(strict_types=1);

namespace Forge\Pedidos {
    function fopen(string $arquivo, string $modo)
    {
        if (($GLOBALS['falhaLock'] ?? '') === 'abrir') return false;
        return \fopen($arquivo, $modo);
    }
    function flock($recurso, int $modo): bool
    {
        if (($GLOBALS['falhaLock'] ?? '') === 'adquirir' && $modo !== LOCK_UN) return false;
        return \flock($recurso, $modo);
    }
    function fclose($recurso): bool
    {
        $GLOBALS['locksFechados'] = ($GLOBALS['locksFechados'] ?? 0) + 1;
        return \fclose($recurso);
    }
}
namespace {
    require_once __DIR__ . '/PedidosEmJson.php';
    require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';
    $diretorio = sys_get_temp_dir() . '/forge-lockfail-' . bin2hex(random_bytes(8));
    if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
    $arquivo = $diretorio . '/store.json';
    file_put_contents($arquivo, '[]');
    try {
        $repo = new Forge\Pedidos\PedidosEmJson($arquivo);
        foreach (['abrir' => 0, 'adquirir' => 1] as $falha => $fechados) {
            $GLOBALS['locksFechados'] = 0;
            $GLOBALS['falhaLock'] = $falha;
            verificarExcecao(RuntimeException::class, function () use ($repo) {
                $repo->salvar(new Forge\Pedidos\Pedido('1', [['codigo' => 'A', 'quantidade' => 1]]));
            });
            verificarIgual($fechados, $GLOBALS['locksFechados']);
            verificarIgual('[]', file_get_contents($arquivo));
            verificarIgual([], glob($diretorio . '/pedidos-*'));
        }
        unset($GLOBALS['falhaLock']);
        $repo->salvar(new Forge\Pedidos\Pedido('1', [['codigo' => 'A', 'quantidade' => 1]]));
        verificarIgual(true, $repo->existe('1'));
    } finally {
        unset($GLOBALS['falhaLock'], $GLOBALS['locksFechados']);
        foreach (glob($diretorio . '/*') as $caminho) unlink($caminho);
        rmdir($diretorio);
    }
    echo "OK: falhas de lock nao gravam dados e liberam o descritor\n";
}
