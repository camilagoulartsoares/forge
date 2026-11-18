<?php

declare(strict_types=1);

namespace Forge\Pedidos {
    function is_link(string $caminho): bool
    {
        return in_array($caminho, $GLOBALS['linksSimulados'] ?? [], true) || \is_link($caminho);
    }
}
namespace {
    require_once __DIR__ . '/PedidosEmJson.php';
    require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';
    $diretorio = sys_get_temp_dir() . '/forge-links-' . bin2hex(random_bytes(8));
    if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
    $arquivo = realpath($diretorio) . DIRECTORY_SEPARATOR . 'store.json';
    file_put_contents($arquivo, '[]');
    try {
        $repo = new Forge\Pedidos\PedidosEmJson($arquivo);
        foreach ([$arquivo, $arquivo . '.lock'] as $link) {
            $GLOBALS['linksSimulados'] = [$link];
            verificarExcecao(RuntimeException::class, function () use ($arquivo) { new Forge\Pedidos\PedidosEmJson($arquivo); });
            verificarExcecao(RuntimeException::class, function () use ($repo) { $repo->buscar('1'); });
            verificarExcecao(RuntimeException::class, function () use ($repo) {
                $repo->salvar(new Forge\Pedidos\Pedido('1', [['codigo' => 'A', 'quantidade' => 1]]));
            });
            verificarIgual('[]', file_get_contents($arquivo));
        }
    } finally {
        unset($GLOBALS['linksSimulados']);
        foreach (glob($diretorio . '/*') as $caminho) unlink($caminho);
        rmdir($diretorio);
    }
    echo "OK: arquivos e locks simbolicos sao recusados\n";
}
