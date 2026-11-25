<?php

declare(strict_types=1);

require_once __DIR__ . '/PedidosEmJson.php';
require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$diretorio = sys_get_temp_dir() . '/forge-caminhos-' . bin2hex(random_bytes(8));
if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
$arquivo = $diretorio . '/pedidos.json';
set_error_handler(function (int $nivel, string $mensagem): void { throw new ErrorException($mensagem, 0, $nivel); });
try {
    foreach ([$arquivo, $arquivo . '.lock'] as $naoRegular) {
        $repo = new Forge\Pedidos\PedidosEmJson($arquivo);
        mkdir($naoRegular);
        verificarExcecao(RuntimeException::class, function () use ($repo): void { $repo->buscar('a'); });
        verificarExcecao(RuntimeException::class, function () use ($repo): void {
            $repo->salvar(new Forge\Pedidos\Pedido('a', [['codigo' => 'Livro', 'quantidade' => 1]]));
        });
        verificarIgual(false, is_file($arquivo));
        if ($naoRegular === $arquivo) verificarIgual(false, file_exists($arquivo . '.lock'));
        rmdir($naoRegular);
    }
    $repo->salvar(new Forge\Pedidos\Pedido('a', [['codigo' => 'Livro', 'quantidade' => 1]]));
    verificarIgual(true, $repo->existe('a'));
} finally {
    restore_error_handler();
    foreach (glob($diretorio . '/*') as $caminho) {
        if (is_dir($caminho)) rmdir($caminho); else unlink($caminho);
    }
    rmdir($diretorio);
}
echo "OK: dados e lock exigem arquivos regulares antes do acesso\n";
