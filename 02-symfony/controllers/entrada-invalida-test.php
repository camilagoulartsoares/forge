<?php

declare(strict_types=1);

require_once __DIR__ . '/criar-tarefa.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

foreach ([[], ['mensagem'], ['titulo' => ['detalhe' => 'nao e texto']], ['titulo' => new stdClass()],
    ['titulo' => "\xff"], ["\xff" => 'Erro'], ['' => 'Erro'], ['titulo' => '']] as $campos) {
    $resposta = Forge\Http\criarTarefaHttp('{"titulo":"A"}', 'application/json', function () use ($campos) {
        throw new Forge\Http\EntradaInvalida($campos);
    });
    verificarIgual(500, $resposta['status']);
    verificarIgual(['erro' => 'erro_interno'], $resposta['corpo']);
}
$campos = ['titulo' => 'Regra de negocio.'];
$erro = new Forge\Http\EntradaInvalida($campos);
$campos['titulo'] = 'Alterado';
$copia = $erro->campos();
$copia['titulo'] = 'Outra alteracao';
verificarIgual(['titulo' => 'Regra de negocio.'], $erro->campos());
verificarIgual(422, Forge\Http\respostaErro($erro)['status']);
json_encode(Forge\Http\respostaErro($erro), JSON_THROW_ON_ERROR);
echo "OK: erros de campos sao mapas textuais serializaveis\n";


$mensagemReferenciada = 'Texto validado';
$camposReferenciados = ['titulo' => &$mensagemReferenciada];
$erroComReferencia = new Forge\Http\EntradaInvalida($camposReferenciados);
$mensagemReferenciada = "\xff";
verificarIgual(['titulo' => 'Texto validado'], $erroComReferencia->campos());
$copia = $erroComReferencia->campos();
$copia['titulo'] = 'Mutacao da copia';
verificarIgual(['titulo' => 'Texto validado'], $erroComReferencia->campos());
json_encode(Forge\Http\respostaErro($erroComReferencia), JSON_THROW_ON_ERROR);
