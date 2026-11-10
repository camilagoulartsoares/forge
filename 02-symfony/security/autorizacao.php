<?php

declare(strict_types=1);

namespace Forge\Autorizacao;

function podeAlterarTarefa(?int $usuarioAutenticado, int $proprietario): bool
{
    return $usuarioAutenticado !== null && $usuarioAutenticado > 0
        && $proprietario > 0 && $usuarioAutenticado === $proprietario;
}

function filtrarTarefasAutorizadas(?int $usuarioAutenticado, array $tarefas): array
{
    return array_values(array_filter($tarefas, function ($tarefa) use ($usuarioAutenticado): bool {
        return is_array($tarefa) && isset($tarefa['proprietarioId'])
            && is_int($tarefa['proprietarioId'])
            && podeAlterarTarefa($usuarioAutenticado, $tarefa['proprietarioId']);
    }));
}
