import { test } from 'node:test';
import assert from 'node:assert/strict';
import { buscarTarefas } from './buscar-tarefas.mjs';

test('encaminha URL e Accept e aceita lista valida ou vazia', async () => {
  for (const tarefas of [[], [{ id: 1, titulo: 'Estudar', concluida: false }]]) {
    const resultado = await buscarTarefas('/tarefas', async (url, options) => {
      assert.equal(url, '/tarefas');
      assert.equal(options.headers.Accept, 'application/json');
      return { ok: true, json: async () => tarefas };
    });
    assert.deepEqual(resultado, tarefas);
  }
});

test('rejeita HTTP sem tentar interpretar corpo de erro', async () => {
  await assert.rejects(buscarTarefas('/tarefas', async () => ({ ok: false, status: 503 })), /503/);
});

test('propaga falha de rede e JSON malformado', async () => {
  await assert.rejects(buscarTarefas('/tarefas', async () => { throw new Error('offline'); }), /offline/);
  await assert.rejects(buscarTarefas('/tarefas', async () => ({
    ok: true, json: async () => { throw new SyntaxError('JSON'); },
  })), SyntaxError);
});

test('rejeita formato inesperado e itens invalidos', async () => {
  for (const dados of [{}, null, [null], [{}], [{ id: 1, titulo: ' ', concluida: false }]]) {
    await assert.rejects(buscarTarefas('/tarefas', async () => ({
      ok: true, json: async () => dados,
    })), TypeError);
  }
});
