<?php

declare(strict_types=1);

namespace Forge\Http;

require_once __DIR__ . '/ler-criacao-tarefa.php';
require_once __DIR__ . '/../api/resposta-erro.php';

function criarTarefaHttp(string $corpo, string $tipo, callable $criar): array
{
    try {
        $entrada = lerCriacaoTarefa($corpo, $tipo);
        $tarefa = $criar($entrada);
        $titulo = \Forge\Tarefas\validarTitulo(is_array($tarefa) ? ($tarefa['titulo'] ?? null) : null);
        if (!is_array($tarefa) || !\Forge\Identidade\identificadorValido($tarefa['id'] ?? null)
            || !$titulo['valido'] || $tarefa['titulo'] !== $titulo['titulo']
            || ($tarefa['concluida'] ?? null) !== false) {
            throw new \UnexpectedValueException('Servico retornou uma criacao invalida.');
        }
        return ['status' => 201,
            'cabecalhos' => ['Content-Type' => 'application/json', 'Location' => '/tarefas/' . $tarefa['id']],
            'corpo' => ['id' => $tarefa['id'], 'titulo' => $tarefa['titulo'], 'concluida' => false]];
    } catch (\Throwable $erro) {
        return respostaErro($erro);
    }
}
