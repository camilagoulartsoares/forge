<?php

declare(strict_types=1);

require_once __DIR__ . '/saudacao.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use Forge\Saudacao\Relogio;
use Forge\Saudacao\Saudacao;

function relogioFixo(string $instante): Relogio
{
    return new class($instante) implements Relogio {
        private DateTimeImmutable $instante;

        public function __construct(string $instante)
        {
            $this->instante = new DateTimeImmutable($instante);
        }

        public function agora(): DateTimeImmutable
        {
            return $this->instante;
        }
    };
}

foreach (['05:59:59' => 'Boa noite', '06:00:00' => 'Bom dia', '11:59:59' => 'Bom dia',
    '12:00:00' => 'Boa tarde', '17:59:59' => 'Boa tarde', '18:00:00' => 'Boa noite'] as $hora => $esperado) {
    $servico = new Saudacao(relogioFixo('2026-01-01T' . $hora . '+00:00'), new DateTimeZone('UTC'));
    verificarIgual($esperado, $servico->mensagem());
}

$servico = new Saudacao(relogioFixo('2026-01-01T12:00:00+00:00'), new DateTimeZone('America/Sao_Paulo'));
verificarIgual('Bom dia', $servico->mensagem());
verificarIgual('Bom dia', $servico->mensagem());
echo 'OK: saudacao com relogio injetado' . PHP_EOL;
