<?php

declare(strict_types=1);

require_once __DIR__ . '/concluir-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

$repositorio = new class implements Forge\Aplicacao\RepositorioTarefas {
    public int $leituras = 0;
    public int $escritas = 0;
    public function buscar(int $id): ?Forge\Aplicacao\Tarefa
    {
        $this->leituras++;
        throw new LogicException('Nao deve consultar antes de validar a identidade.');
    }
    public function salvar(Forge\Aplicacao\Tarefa $tarefa): void { $this->escritas++; }
};
$servico = new Forge\Aplicacao\ConcluirTarefa($repositorio);
foreach ([null, 0, -1] as $usuario) {
    // Mesmo um ID de tarefa invalido nao deve expor detalhes antes da autorizacao.
    foreach ([0, 1, 999] as $id) {
        verificarExcecao(Forge\Aplicacao\AcessoNegado::class, function () use ($servico, $id, $usuario) {
            $servico->executarAutorizada($id, $usuario);
        });
    }
}
verificarIgual(0, $repositorio->leituras);
verificarIgual(0, $repositorio->escritas);
echo "OK: identidade negada antes de qualquer leitura\n";
