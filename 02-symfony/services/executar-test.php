<?php

declare(strict_types=1);

require_once __DIR__ . '/../../01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php';

foreach ([['1', 0, "{\"id\":1,\"concluida\":true}\n", ''],
    ['2', 1, '', "Tarefa nao encontrada.\n"],
    ['1e2', 2, '', "Identificador invalido.\n"],
    [null, 2, '', "Uso: php 02-symfony/services/executar.php <id>\n"]] as [$id, $codigo, $saida, $erro]) {
    $comando = [PHP_BINARY, __DIR__ . '/executar.php'];
    if ($id !== null) $comando[] = $id;
    $processo = proc_open($comando, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($processo)) throw new RuntimeException('Falha ao iniciar CLI.');
    verificarIgual($saida, stream_get_contents($pipes[1]));
    verificarIgual($erro, stream_get_contents($pipes[2]));
    fclose($pipes[1]);
    fclose($pipes[2]);
    verificarIgual($codigo, proc_close($processo));
}
echo "OK: CLI do servico de tarefas\n";
