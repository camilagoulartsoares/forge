<?php

declare(strict_types=1);

namespace Forge\Http;

require_once __DIR__ . '/../api/validar-titulo.php';
require_once __DIR__ . '/../../01-fundamentos/php/introducao/exemplos/json-estrito.php';

final class EntradaInvalida extends \InvalidArgumentException
{
    private array $campos;

    public function __construct(array $campos)
    {
        if ($campos === []) throw new \InvalidArgumentException('Informe erros de campos.');
        foreach ($campos as $campo => $mensagem) {
            if (!is_string($campo) || trim($campo) === '' || preg_match('//u', $campo) !== 1
                || !is_string($mensagem) || trim($mensagem) === '' || preg_match('//u', $mensagem) !== 1) {
                throw new \InvalidArgumentException('Erros de campos devem ser textos UTF-8.');
            }
        }
        parent::__construct('Entrada invalida.');
        $this->campos = $campos;
    }

    public function campos(): array
    {
        return $this->campos;
    }
}

final class CorpoMuitoGrande extends \LengthException {}

final class CorpoJsonInvalido extends \JsonException {}

final class TipoConteudoNaoSuportado extends \RuntimeException {}

function lerCriacaoTarefa(string $corpo, string $tipo = 'application/json'): array
{
    if (preg_match('/[\x00-\x08\x0A-\x1F\x7F]/', $tipo)
        || strtolower(trim(explode(';', $tipo, 2)[0])) !== 'application/json') {
        throw new TipoConteudoNaoSuportado('Esperado application/json.');
    }
    if (strlen($corpo) > 16384) throw new CorpoMuitoGrande('Corpo excede 16 KiB.');
    try {
        $entrada = \Forge\Json\decodificarJson($corpo);
    } catch (\JsonException $erro) {
        throw new CorpoJsonInvalido('Corpo JSON invalido.', $erro->getCode(), $erro);
    }
    if (!$entrada instanceof \stdClass) {
        throw new CorpoJsonInvalido('Esperado objeto JSON.');
    }
    $titulo = \Forge\Tarefas\validarTitulo($entrada->titulo ?? null);
    if (!$titulo['valido']) {
        throw new EntradaInvalida(['titulo' => $titulo['erro']]);
    }
    // Nao aceita id, proprietario ou concluida fornecidos pelo cliente.
    return ['titulo' => $titulo['titulo']];
}
