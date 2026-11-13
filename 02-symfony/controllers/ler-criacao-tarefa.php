<?php

declare(strict_types=1);

namespace Forge\Http;

require_once __DIR__ . '/../api/validar-titulo.php';

final class EntradaInvalida extends \InvalidArgumentException
{
    public array $campos;

    public function __construct(array $campos)
    {
        parent::__construct('Entrada invalida.');
        $this->campos = $campos;
    }
}

final class TipoConteudoNaoSuportado extends \RuntimeException {}

function lerCriacaoTarefa(string $corpo, string $tipo = 'application/json'): array
{
    if (strtolower(trim(explode(';', $tipo, 2)[0])) !== 'application/json') {
        throw new TipoConteudoNaoSuportado('Esperado application/json.');
    }
    $entrada = json_decode($corpo, false, 512, JSON_THROW_ON_ERROR);
    if (!$entrada instanceof \stdClass) {
        throw new \JsonException('Esperado objeto JSON.');
    }
    $titulo = \Forge\Tarefas\validarTitulo($entrada->titulo ?? null);
    if (!$titulo['valido']) {
        throw new EntradaInvalida(['titulo' => $titulo['erro']]);
    }
    // Nao aceita id, proprietario ou concluida fornecidos pelo cliente.
    return ['titulo' => $titulo['titulo']];
}
