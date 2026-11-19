<?php

// Intencional: demonstra que strict_types pertence ao arquivo chamador.
declare(strict_types=0);

namespace Forge\Tipos;

require_once __DIR__ . '/calcular-total.php';

function totalComCoercao($preco, $quantidade): int
{
    return \calcularTotal($preco, $quantidade);
}
