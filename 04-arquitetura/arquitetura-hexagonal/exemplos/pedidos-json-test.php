<?php

declare(strict_types=1);

require_once __DIR__ . '/PedidosEmJson.php';
require_once __DIR__ . '/RegistrarPedido.php';
require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use Forge\Pedidos\PedidosEmJson;
use Forge\Pedidos\Pedido;
use Forge\Pedidos\RegistrarPedido;

$diretorio = sys_get_temp_dir() . '/forge-pedidos-' . bin2hex(random_bytes(8));
if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
$arquivo = $diretorio . '/pedidos.json';
try {
    $a = new PedidosEmJson($arquivo);
    $b = new PedidosEmJson($arquivo);
    verificarIgual(false, $a->existe('1'));
    (new RegistrarPedido($a))->executar('1', [['codigo' => 'Livro', 'quantidade' => 2]]);
    verificarIgual([['codigo' => 'Livro', 'quantidade' => 2]], $b->buscar('1')->itens());
    $antes = file_get_contents($arquivo);
    verificarExcecao(DomainException::class, function () use ($b) {
        $b->salvar(new Pedido('1', [['codigo' => 'Outro', 'quantidade' => 1]]));
    });
    verificarIgual($antes, file_get_contents($arquivo));
    $b->salvar(new Pedido('2', [['codigo' => 'Caneta', 'quantidade' => 1]]));
    verificarIgual(true, $a->existe('2'));
    foreach (['{', '{}', '[null]', '[{"id":"x","itens":[]}]',
        '[' . substr($antes, 1, -1) . ',' . substr($antes, 1, -1) . ']'] as $corrompido) {
        file_put_contents($arquivo, $corrompido);
        verificarExcecao(Throwable::class, function () use ($a) {
            $a->salvar(new Pedido('3', [['codigo' => 'Novo', 'quantidade' => 1]]));
        });
        verificarIgual($corrompido, file_get_contents($arquivo));
    }
} finally {
    foreach (glob($diretorio . '/*') as $temporario) unlink($temporario);
    rmdir($diretorio);
}
echo "OK: persistencia JSON e preservacao de dados invalidos\n";
