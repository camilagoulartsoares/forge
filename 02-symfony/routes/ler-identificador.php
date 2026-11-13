<?php

declare(strict_types=1);

namespace Forge\Rotas;

require_once __DIR__ . '/../api/identificador.php';

function lerIdentificador(string $valor): int
{
    $limite = PHP_INT_SIZE >= 8 ? '9007199254740991' : (string) PHP_INT_MAX;
    if (!preg_match('/\A[1-9][0-9]*\z/D', $valor)
        || strlen($valor) > strlen($limite)
        || (strlen($valor) === strlen($limite) && strcmp($valor, $limite) > 0)) {
        throw new \InvalidArgumentException('Identificador decimal positivo seguro esperado.');
    }
    $id = (int) $valor;
    if (!\Forge\Identidade\identificadorValido($id)) throw new \InvalidArgumentException('Identificador invalido.');
    return $id;
}
