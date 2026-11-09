<?php

declare(strict_types=1);

namespace Forge\Saudacao;

use DateTimeImmutable;
use DateTimeZone;

interface Relogio
{
    public function agora(): DateTimeImmutable;
}

final class RelogioSistema implements Relogio
{
    public function agora(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}

final class Saudacao
{
    private Relogio $relogio;
    private DateTimeZone $fuso;

    public function __construct(Relogio $relogio, DateTimeZone $fuso)
    {
        $this->relogio = $relogio;
        $this->fuso = $fuso;
    }

    public function mensagem(): string
    {
        $hora = (int) $this->relogio->agora()->setTimezone($this->fuso)->format('G');
        if ($hora >= 6 && $hora < 12) {
            return 'Bom dia';
        }

        return $hora >= 12 && $hora < 18 ? 'Boa tarde' : 'Boa noite';
    }
}
