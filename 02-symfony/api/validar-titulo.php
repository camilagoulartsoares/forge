<?php

declare(strict_types=1);

namespace Forge\Tarefas;

function validarTitulo($valor): array
{
    if (!is_string($valor)) {
        return ['valido' => false, 'erro' => 'Informe um titulo textual.'];
    }
    if (preg_match('//u', $valor) !== 1) {
        return ['valido' => false, 'erro' => 'Titulo deve usar UTF-8 valido.'];
    }

    // Match String.trim whitespace explicitly; PCRE \s has different semantics.
    $espacos = '\x{0009}-\x{000D}\x{0020}\x{00A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}\x{FEFF}';
    $titulo = preg_replace('/^[' . $espacos . ']+|[' . $espacos . ']+$/u', '', $valor);
    if ($titulo === '') {
        return ['valido' => false, 'erro' => 'Titulo obrigatorio.'];
    }

    // Every code point uses at most three UTF-8 bytes per UTF-16 unit.
    if (strlen($titulo) > 360) {
        return ['valido' => false, 'erro' => 'Titulo deve ter ate 120 unidades UTF-16.'];
    }
    $unidades = preg_match_all('/./us', $titulo)
        + preg_match_all('/[\x{10000}-\x{10FFFF}]/u', $titulo);
    if ($unidades > 120) {
        return ['valido' => false, 'erro' => 'Titulo deve ter ate 120 unidades UTF-16.'];
    }

    return ['valido' => true, 'titulo' => $titulo];
}
