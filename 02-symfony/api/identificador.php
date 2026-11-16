<?php

declare(strict_types=1);

namespace Forge\Identidade;

final class IdentificadorInvalido extends \InvalidArgumentException {}

function identificadorValido($valor): bool
{
    return is_int($valor) && $valor > 0 && $valor <= 9007199254740991;
}
