<?php

declare(strict_types=1);

require_once __DIR__ . '/resposta-erro.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

foreach ([
    [new Forge\Identidade\IdentificadorInvalido('interno'), 422, 'entrada_invalida'],
    [new InvalidArgumentException('erro de programacao'), 500, 'erro_interno'],
    [new JsonException('interno'), 400, 'json_invalido'],
    [new Forge\Http\TipoConteudoNaoSuportado('interno'), 415, 'tipo_nao_suportado'],
    [new Forge\Http\EntradaInvalida(['titulo' => 'Obrigatorio.']), 422, 'entrada_invalida'],
    [new Forge\Aplicacao\AcessoNegado('interno'), 403, 'acesso_negado'],
    [new Forge\Aplicacao\TarefaNaoEncontrada('interno'), 404, 'tarefa_nao_encontrada'],
    [new RuntimeException('senha=segredo'), 500, 'erro_interno'],
    [new TypeError('caminho interno'), 500, 'erro_interno'],
] as [$erro, $status, $codigo]) {
    $resposta = Forge\Http\respostaErro($erro);
    verificarIgual($status, $resposta['status']);
    verificarIgual($codigo, $resposta['corpo']['erro']);
    verificarIgual(['Content-Type' => 'application/json'], $resposta['cabecalhos']);
    if ($status === 500) verificarIgual(['erro' => 'erro_interno'], $resposta['corpo']);
}
verificarIgual(['titulo' => 'Obrigatorio.'], Forge\Http\respostaErro(new Forge\Http\EntradaInvalida(['titulo' => 'Obrigatorio.']))['corpo']['campos']);
echo "OK: respostas de erro HTTP\n";

try {
    require_once __DIR__ . '/../routes/ler-identificador.php';
    Forge\Rotas\lerIdentificador('1e2');
    throw new LogicException('Deveria rejeitar.');
} catch (Forge\Identidade\IdentificadorInvalido $erro) {
    verificarIgual(['id' => 'Identificador invalido.'], Forge\Http\respostaErro($erro)['corpo']['campos']);
}
