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
foreach (['2024-03-09T10:00:00+00:00' => 'Boa noite', '2024-03-10T10:00:00+00:00' => 'Bom dia'] as $instante => $mensagem) {
    $fixo = relogioFixo($instante);
    $antes = $fixo->agora()->format(DATE_ATOM);
    $saudacao = new Saudacao($fixo, new DateTimeZone('America/New_York'));
    verificarIgual($mensagem, $saudacao->mensagem());
    verificarIgual($antes, $fixo->agora()->format(DATE_ATOM));
}
$vespera = new Saudacao(relogioFixo('2026-01-01T02:59:59+00:00'), new DateTimeZone('America/Sao_Paulo'));
verificarIgual('Boa noite', $vespera->mensagem());
echo 'OK: saudacao com relogio injetado' . PHP_EOL;


// A mesma instancia precisa consultar o relogio novamente a cada mensagem.
$movel = new class implements Relogio {
    public DateTimeImmutable $instante;
    public function __construct() { $this->instante = new DateTimeImmutable('2026-01-01T08:59:59+00:00'); }
    public function agora(): DateTimeImmutable { return $this->instante; }
};
$mesmaSaudacao = new Saudacao($movel, new DateTimeZone('America/Sao_Paulo'));
foreach (['2026-01-01T08:59:59+00:00' => 'Boa noite', '2026-01-01T09:00:00+00:00' => 'Bom dia',
    '2026-01-01T15:00:00+00:00' => 'Boa tarde', '2026-01-01T21:00:00+00:00' => 'Boa noite'] as $instante => $mensagem) {
    $movel->instante = new DateTimeImmutable($instante);
    verificarIgual($mensagem, $mesmaSaudacao->mensagem());
    verificarIgual($instante, $movel->instante->format(DATE_ATOM));
}
