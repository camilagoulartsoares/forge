"""Run real PHP response serialization through the JavaScript API consumer, without HTTP."""

import json
from pathlib import Path
import shutil
import subprocess
import unittest

ROOT = Path(__file__).resolve().parents[1]
PHP = r"require $argv[1]; $response = Forge\Http\criarTarefaHttp(stream_get_contents(STDIN), 'application/json', function ($input) { return ['id' => 9007199254740991, 'titulo' => $input['titulo'], 'concluida' => false, 'proprietarioId' => 7]; }); echo json_encode($response, JSON_THROW_ON_ERROR);"
NODE = """
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { buscarTarefas, ErroHttp } from './03-vue/consumo-api/buscar-tarefas.mjs';
import { exportarTarefas } from './03-vue/fundamentos/exportar-tarefas.mjs';
import { importarTarefas } from './03-vue/fundamentos/importar-tarefas.mjs';
const resposta = JSON.parse(readFileSync(0, 'utf8'));
if (resposta.status === 201) {
  assert.equal(resposta.cabecalhos.Location, '/tarefas/' + resposta.corpo.id);
  assert.deepEqual(Object.keys(resposta.corpo).sort(), ['concluida', 'id', 'titulo']);
  const dados = await buscarTarefas('/tarefas', async () => new Response(JSON.stringify([resposta.corpo])));
  assert.deepEqual(importarTarefas([], exportarTarefas(dados)), dados);
  process.stdout.write(JSON.stringify(dados));
} else {
  const response = new Response(JSON.stringify(resposta.corpo), { status: resposta.status });
  await assert.rejects(buscarTarefas('/tarefas', async () => response), erro => erro instanceof ErroHttp && erro.status === resposta.status);
  assert.equal(response.bodyUsed, false);
  process.stdout.write(JSON.stringify(resposta.corpo));
}
"""


@unittest.skipUnless(shutil.which('php') and shutil.which('node'), 'PHP and Node CLIs required')
class ApiInteroperabilityTests(unittest.TestCase):
    def exchange(self, title):
        php = subprocess.run(['php', '-r', PHP, str(ROOT / '02-symfony/controllers/criar-tarefa.php')],
                             input=json.dumps({'titulo': title}), capture_output=True, text=True, encoding='utf-8',
                             cwd=ROOT, timeout=10)
        self.assertEqual(0, php.returncode, php.stderr)
        self.assertEqual('', php.stderr)
        node = subprocess.run(['node', '--input-type=module', '-e', NODE], input=php.stdout,
                              capture_output=True, text=True, encoding='utf-8', cwd=ROOT, timeout=10)
        self.assertEqual(0, node.returncode, node.stderr)
        self.assertEqual('', node.stderr)
        return json.loads(node.stdout)

    def test_created_titles_and_max_safe_id_survive_real_cross_runtime_round_trip(self):
        for title in ['Estudar PHP', '\uFEFF  Caf\u00e9  ', '\U0001f600' * 60]:
            with self.subTest(title=ascii(title)):
                result = self.exchange(title)
                expected = title.strip('\ufeff \t\r\n')
                self.assertEqual([{'id': 9007199254740991, 'titulo': expected, 'concluida': False}], result)

    def test_rejected_title_keeps_http_error_contract_and_body_unconsumed(self):
        self.assertEqual({'erro': 'entrada_invalida', 'campos': {'titulo': 'Titulo obrigatorio.'}}, self.exchange('   '))


if __name__ == '__main__':
    unittest.main()
