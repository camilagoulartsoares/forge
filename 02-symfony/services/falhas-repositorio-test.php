<?php

declare(strict_types=1);

require_once __DIR__ . '/../controllers/concluir-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

foreach ([new Forge\Identidade\IdentificadorInvalido('ID interno'),
    new Forge\Http\CorpoJsonInvalido('JSON do armazenamento'),
    new Forge\Aplicacao\TarefaNaoEncontrada('Tabela interna'),
    new Forge\Aplicacao\AcessoNegado('Credencial do banco')] as $falha) {
    foreach (['buscar', 'salvar'] as $etapa) {
        $repo = new class($falha, $etapa) implements Forge\Aplicacao\RepositorioTarefas {
            private Throwable $falha;
            private string $etapa;
            public function __construct(Throwable $falha, string $etapa) { $this->falha = $falha; $this->etapa = $etapa; }
            public function buscar(int $id): ?Forge\Aplicacao\Tarefa {
                if ($this->etapa === 'buscar') throw $this->falha;
                return new Forge\Aplicacao\Tarefa($id, false, 7);
            }
            public function salvar(Forge\Aplicacao\Tarefa $tarefa): void { throw $this->falha; }
        };
        $servico = new Forge\Aplicacao\ConcluirTarefa($repo);
        $resposta = Forge\Http\concluirTarefaHttp('1', 7, $servico);
        verificarIgual(500, $resposta['status']);
        verificarIgual(['erro' => 'erro_interno'], $resposta['corpo']);
        try {
            $servico->executarAutorizada(1, 7);
            throw new LogicException('Falha esperada.');
        } catch (RuntimeException $erro) {
            verificarIgual($falha, $erro->getPrevious());
        }
    }
}
echo "OK: falhas de infraestrutura nao sao erros HTTP do cliente\n";
