<?php

declare(strict_types=1);

namespace Forge\Pedidos {
    function flock($recurso, int $modo): bool
    {
        if ($modo !== LOCK_UN) $GLOBALS['modosLock'][] = $modo;
        return \flock($recurso, $modo);
    }
}
namespace {
    require_once __DIR__ . '/PedidosEmJson.php';
    require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';
    $diretorio = sys_get_temp_dir() . '/forge-sh-' . bin2hex(random_bytes(8));
    if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
    try {
        $repo = new Forge\Pedidos\PedidosEmJson($diretorio . '/store.json');
        verificarIgual(null, $repo->buscar('1'));
        $repo->salvar(new Forge\Pedidos\Pedido('1', [['codigo' => 'A', 'quantidade' => 1]]));
        verificarIgual(true, $repo->existe('1'));
        verificarIgual([LOCK_SH, LOCK_EX, LOCK_SH], $GLOBALS['modosLock']);
    } finally {
        unset($GLOBALS['modosLock']);
        foreach (glob($diretorio . '/*') as $caminho) unlink($caminho);
        rmdir($diretorio);
    }
    echo "OK: leituras compartilham lock e escritas continuam exclusivas\n";
}
