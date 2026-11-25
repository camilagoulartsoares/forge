<?php

declare(strict_types=1);

namespace Forge\Http;

require_once __DIR__ . '/../api/resposta-erro.php';
require_once __DIR__ . '/../routes/ler-identificador.php';

function concluirTarefaHttp(string $segmento, ?int $usuario, \Forge\Aplicacao\ConcluirTarefa $servico): array
{
    if (!\Forge\Identidade\identificadorValido($usuario)) {
        return respostaErro(new \Forge\Aplicacao\AcessoNegado('Identidade invalida.'));
    }
    try {
        $tarefa = $servico->executarAutorizada(\Forge\Rotas\lerIdentificador($segmento), $usuario);
        return ['status' => 200, 'cabecalhos' => ['Content-Type' => 'application/json'],
            'corpo' => ['id' => $tarefa->id(), 'concluida' => $tarefa->concluida()]];
    } catch (\Forge\Aplicacao\AcessoNegado $erro) {
        // Nao distingue tarefa alheia de recurso ausente para uma identidade valida.
        return respostaErro(new \Forge\Aplicacao\TarefaNaoEncontrada('Tarefa nao encontrada.', 0, $erro));
    } catch (\Throwable $erro) {
        return respostaErro($erro);
    }
}
