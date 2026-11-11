<?php

declare(strict_types=1);

require_once __DIR__ . '/validar-titulo.php';
require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

use function Forge\Tarefas\validarTitulo;

verificarIgual(['valido' => true, 'titulo' => 'Estudar PHP'], validarTitulo("\u{00A0}Estudar PHP\u{FEFF}"));
verificarIgual(false, validarTitulo(null)['valido']);
verificarIgual(false, validarTitulo("\xFF")['valido']);
verificarIgual(false, validarTitulo("\u{2003}")['valido']);
verificarIgual(true, validarTitulo(str_repeat("\u{1F600}", 60))['valido']);
verificarIgual(false, validarTitulo(str_repeat("\u{1F600}", 61))['valido']);
verificarIgual(true, validarTitulo("\u{0085}")['valido']);
verificarIgual(true, validarTitulo(str_repeat("\u{4E00}", 120))['valido']);
verificarIgual(false, validarTitulo(str_repeat("\u{4E00}", 121))['valido']);
verificarIgual(false, validarTitulo(str_repeat('a', 1000000))['valido']);
$casos = json_decode(file_get_contents(__DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/casos-titulo.json'), true, 512, JSON_THROW_ON_ERROR);
foreach ($casos as $caso) {
    $resultado = validarTitulo($caso['entrada']);
    verificarIgual($caso['valido'], $resultado['valido']);
    if ($caso['valido']) {
        verificarIgual($caso['titulo'], $resultado['titulo']);
    }
}
echo 'OK: titulo PHP' . PHP_EOL;
