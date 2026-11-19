<?php

declare(strict_types=1);

require_once __DIR__ . '/pedidos.php';
require_once __DIR__ . '/PedidosEmJson.php';
require_once __DIR__ . '/../../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$diretorio = sys_get_temp_dir() . '/forge-id-' . bin2hex(random_bytes(8));
if (!mkdir($diretorio)) throw new RuntimeException('Falha ao preparar teste.');
try {
    $adaptadores = [new Forge\Pedidos\PedidosEmMemoria(), new Forge\Pedidos\PedidosEmJson($diretorio . '/store.json')];
    $ids = ['1', '01', '1e0', '9007199254740993', '__proto__', '../apenas-id', "pedido-\xf0\x9f\x98\x80"];
    foreach ($adaptadores as $repositorio) {
        foreach ($ids as $indice => $id) {
            $repositorio->salvar(new Forge\Pedidos\Pedido($id, [['codigo' => 'Produto', 'quantidade' => $indice + 1]]));
        }
        foreach ($ids as $indice => $id) {
            verificarIgual($id, $repositorio->buscar($id)->id());
            verificarIgual($indice + 1, $repositorio->buscar($id)->itens()[0]['quantidade']);
            verificarExcecao(DomainException::class, function () use ($repositorio, $id) {
                $repositorio->salvar(new Forge\Pedidos\Pedido($id, [['codigo' => 'Outro', 'quantidade' => 99]]));
            });
            verificarIgual($indice + 1, $repositorio->buscar($id)->itens()[0]['quantidade']);
        }
        verificarIgual(null, $repositorio->buscar('ausente'));
    }
    verificarIgual(2, count(glob($diretorio . '/*')));
} finally {
    foreach (glob($diretorio . '/*') as $caminho) unlink($caminho);
    rmdir($diretorio);
}
echo "OK: identificadores textuais preservados pelos dois adaptadores\n";
