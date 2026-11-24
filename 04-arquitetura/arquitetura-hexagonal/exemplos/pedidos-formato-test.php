<?php

declare(strict_types=1);

require_once __DIR__ . '/PedidosEmJson.php';
require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$diretorio = sys_get_temp_dir() . '/forge-formato-' . bin2hex(random_bytes(8));
if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
$arquivo = $diretorio . '/pedidos.json';
try {
    // Ordem das propriedades e escapes equivalentes nao mudam os dados do formato.
    file_put_contents($arquivo, '[{"itens":[{"quantidade":2,"codigo":"Caf\u00e9"}],"id":"a"}]');
    $repo = new Forge\Pedidos\PedidosEmJson($arquivo);
    verificarIgual([['codigo' => "Caf\xc3\xa9", 'quantidade' => 2]], $repo->buscar('a')->itens());
    $repo->salvar(new Forge\Pedidos\Pedido('b', [['codigo' => 'Livro', 'quantidade' => 1]]));
    $outraInstancia = new Forge\Pedidos\PedidosEmJson($arquivo);
    verificarIgual([['codigo' => "Caf\xc3\xa9", 'quantidade' => 2]], $outraInstancia->buscar('a')->itens());
    verificarIgual([['codigo' => 'Livro', 'quantidade' => 1]], $outraInstancia->buscar('b')->itens());
    verificarIgual(2, count(json_decode(file_get_contents($arquivo), true, 512, JSON_THROW_ON_ERROR)));
} finally {
    foreach (glob($diretorio . '/*') as $temporario) unlink($temporario);
    rmdir($diretorio);
}
echo "OK: variacoes JSON preservam semantica dos pedidos\n";
