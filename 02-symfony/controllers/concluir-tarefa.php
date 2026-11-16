<?php

declare(strict_types=1);

namespace Forge\Http;

require_once __DIR__ . '/../api/resposta-erro.php';
require_once __DIR__ . '/../routes/ler-identificador.php';

function concluirTarefaHttp(string $segmento, ?int $usuario, \Forge\Aplicacao\ConcluirTarefa $servico): array
{
    try {
        if (!\Forge\Identidade\identificadorValido($usuario)) throw new \Forge\Aplicacao\AcessoNegado('Identidade invalida.');
        $tarefa = $servico->executarAutorizada(\Forge\Rotas\lerIdentificador($segmento), $usuario);
        return ['status' => 200, 'cabecalhos' => ['Content-Type' => 'application/json'],
            'corpo' => ['id' => $tarefa->id(), 'concluida' => $tarefa->concluida()]];
    } catch (\Throwable $erro) {
        return respostaErro($erro);
    }
}
