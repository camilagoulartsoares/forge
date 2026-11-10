<?php

declare(strict_types=1);

require_once __DIR__ . '/pedidos.php';
require_once dirname(__DIR__, 3) . '/01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use Forge\Pedidos\ArmazenamentoPedidos;
use Forge\Pedidos\Pedido;
use Forge\Pedidos\PedidosEmMemoria;
use Forge\Pedidos\RegistrarPedido;

$memoria = new PedidosEmMemoria();
$registrar = new RegistrarPedido($memoria);
$itens = [['codigo' => ' Livro ', 'quantidade' => 2]];
$pedido = $registrar->executar(' A ', $itens);
verificarIgual('A', $pedido->id());
verificarIgual([['codigo' => 'Livro', 'quantidade' => 2]], $pedido->itens());
verificarIgual($pedido, $memoria->buscar('A'));
$itens[0]['quantidade'] = 99;
verificarIgual(2, $pedido->itens()[0]['quantidade']);
verificarExcecao(DomainException::class, function () use ($registrar): void {
    $registrar->executar('A', [['codigo' => 'Outro', 'quantidade' => 1]]);
});
verificarIgual($pedido, $memoria->buscar('A'));

$spy = new class implements ArmazenamentoPedidos {
    public int $gravacoes = 0;

    public function existe(string $id): bool
    {
        return false;
    }

    public function salvar(Pedido $pedido): void
    {
        $this->gravacoes++;
        throw new RuntimeException('Armazenamento indisponivel');
    }
};
$caso = new RegistrarPedido($spy);
foreach ([[], [['codigo' => '', 'quantidade' => 1]], [['codigo' => 'A', 'quantidade' => 0]],
    [['codigo' => 'A', 'quantidade' => -1]], [['codigo' => 'A', 'quantidade' => '1']], [null]] as $invalidos) {
    verificarExcecao(InvalidArgumentException::class, function () use ($caso, $invalidos): void {
        $caso->executar('B', $invalidos);
    });
}
verificarExcecao(InvalidArgumentException::class, function () use ($caso, $itens): void {
    $caso->executar('   ', $itens);
});
verificarIgual(0, $spy->gravacoes);
verificarExcecao(RuntimeException::class, function () use ($caso, $itens): void {
    $caso->executar('B', $itens);
});
verificarIgual(1, $spy->gravacoes);
// Check the adapter independently of the use case's duplicate check.
$direto = new PedidosEmMemoria();
verificarIgual(false, $direto->existe('ausente'));
verificarIgual(null, $direto->buscar('ausente'));
$primeiro = new Pedido('direto', [['codigo' => 'A', 'quantidade' => 1]]);
$direto->salvar($primeiro);
verificarExcecao(DomainException::class, function () use ($direto): void {
    $direto->salvar(new Pedido('direto', [['codigo' => 'B', 'quantidade' => 9]]));
});
verificarIgual($primeiro, $direto->buscar('direto'));
$outraMemoria = new PedidosEmMemoria();
verificarIgual(false, $outraMemoria->existe('direto'));
$copia = $pedido->itens();
$copia[0]['quantidade'] = 999;
$copia[] = ['codigo' => 'Injetado', 'quantidade' => 1];
verificarIgual([['codigo' => 'Livro', 'quantidade' => 2]], $pedido->itens());
foreach ([true, 1.5, null] as $quantidadeInvalida) {
    verificarExcecao(InvalidArgumentException::class, function () use ($caso, $quantidadeInvalida): void {
        $caso->executar('lote', [
            ['codigo' => 'Valido', 'quantidade' => 1],
            ['codigo' => 'Invalido', 'quantidade' => $quantidadeInvalida],
        ]);
    });
}
verificarIgual(1, $spy->gravacoes);
echo 'OK: registro de pedidos' . PHP_EOL;
