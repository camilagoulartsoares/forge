<?php

declare(strict_types=1);

namespace Forge\Aplicacao;

use function Forge\Identidade\identificadorValido;

require_once __DIR__ . '/../api/identificador.php';

use InvalidArgumentException;
use OutOfBoundsException;
use DomainException;
use UnexpectedValueException;
use function Forge\Autorizacao\podeAlterarTarefa;

require_once __DIR__ . '/../security/autorizacao.php';

final class AcessoNegado extends DomainException {}
final class TarefaNaoEncontrada extends OutOfBoundsException {}

final class Tarefa
{
    private int $id;
    private bool $concluida;
    private ?int $proprietario;

    public function __construct(int $id, bool $concluida = false, ?int $proprietario = null)
    {
        if (!identificadorValido($id)) {
            throw new InvalidArgumentException('Identificador deve ser um inteiro positivo seguro.');
        }
        $this->id = $id;
        $this->concluida = $concluida;
        if ($proprietario !== null && $proprietario <= 0) {
            throw new InvalidArgumentException('Proprietario deve ser positivo.');
        }
        $this->proprietario = $proprietario;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function concluida(): bool
    {
        return $this->concluida;
    }

    public function concluir(): self
    {
        return $this->concluida ? $this : new self($this->id, true, $this->proprietario);
    }

    public function proprietario(): ?int
    {
        return $this->proprietario;
    }
}

interface RepositorioTarefas
{
    public function buscar(int $id): ?Tarefa;
    public function salvar(Tarefa $tarefa): void;
}

final class ConcluirTarefa
{
    private RepositorioTarefas $repositorio;

    public function __construct(RepositorioTarefas $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    public function executar(int $id): Tarefa
    {
        return $this->concluir($this->obter($id));
    }

    public function executarAutorizada(int $id, ?int $usuarioAutenticado): Tarefa
    {
        if ($usuarioAutenticado === null || $usuarioAutenticado <= 0) {
            throw new AcessoNegado('Operacao nao autorizada.');
        }
        $tarefa = $this->obter($id);
        if ($tarefa->proprietario() === null || !podeAlterarTarefa($usuarioAutenticado, $tarefa->proprietario())) {
            throw new AcessoNegado('Operacao nao autorizada.');
        }

        return $this->concluir($tarefa);
    }

    private function obter(int $id): Tarefa
    {
        if (!identificadorValido($id)) {
            throw new InvalidArgumentException('Identificador deve ser um inteiro positivo seguro.');
        }
        $tarefa = $this->repositorio->buscar($id);
        if ($tarefa === null) {
            throw new TarefaNaoEncontrada('Tarefa nao encontrada.');
        }
        if ($tarefa->id() !== $id) {
            throw new UnexpectedValueException('Repositorio retornou outra tarefa.');
        }

        return $tarefa;
    }

    private function concluir(Tarefa $tarefa): Tarefa
    {
        if ($tarefa->concluida()) {
            return $tarefa;
        }
        $resultado = $tarefa->concluir();
        $this->repositorio->salvar($resultado);

        return $resultado;
    }
}
