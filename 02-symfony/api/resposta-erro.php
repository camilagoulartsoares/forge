<?php

declare(strict_types=1);

namespace Forge\Http;

require_once __DIR__ . '/../controllers/ler-criacao-tarefa.php';
require_once __DIR__ . '/../services/concluir-tarefa.php';

// Descreve a resposta; o framework e responsavel por emiti-la.
function respostaErro(\Throwable $erro): array
{
    $status = 500;
    $corpo = ['erro' => 'erro_interno'];
    if ($erro instanceof EntradaInvalida) {
        $status = 422;
        $corpo = ['erro' => 'entrada_invalida', 'campos' => $erro->campos];
    } elseif ($erro instanceof \JsonException) {
        $status = 400;
        $corpo = ['erro' => 'json_invalido'];
    } elseif ($erro instanceof TipoConteudoNaoSuportado) {
        $status = 415;
        $corpo = ['erro' => 'tipo_nao_suportado'];
    } elseif ($erro instanceof \Forge\Aplicacao\AcessoNegado) {
        $status = 403;
        $corpo = ['erro' => 'acesso_negado'];
    } elseif ($erro instanceof \Forge\Aplicacao\TarefaNaoEncontrada) {
        $status = 404;
        $corpo = ['erro' => 'tarefa_nao_encontrada'];
    }
    return ['status' => $status, 'cabecalhos' => ['Content-Type' => 'application/json'], 'corpo' => $corpo];
}
