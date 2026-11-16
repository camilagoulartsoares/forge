<?php

declare(strict_types=1);

namespace Forge\Autorizacao;

use function Forge\Identidade\identificadorValido;

require_once __DIR__ . '/../api/identificador.php';

function podeAlterarTarefa(?int $usuarioAutenticado, int $proprietario): bool
{
    return identificadorValido($usuarioAutenticado) && identificadorValido($proprietario)
        && $usuarioAutenticado === $proprietario;
}

function filtrarTarefasAutorizadas(?int $usuarioAutenticado, array $tarefas): array
{
    return array_values(array_filter($tarefas, function ($tarefa) use ($usuarioAutenticado): bool {
        return is_array($tarefa) && isset($tarefa['proprietarioId'])
            && is_int($tarefa['proprietarioId'])
            && podeAlterarTarefa($usuarioAutenticado, $tarefa['proprietarioId']);
    }));
}
