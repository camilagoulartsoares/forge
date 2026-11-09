import { test } from 'node:test';
import assert from 'node:assert/strict';
import { buscarTarefas, ErroHttp } from './buscar-tarefas.mjs';

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

test('expoe status HTTP sem exigir interpretar texto de erro', async () => {
  for (const status of [401, 403, 404, 429, 503]) {
    await assert.rejects(buscarTarefas('/tarefas', async () => ({ ok: false, status })), (erro) => {
      assert.ok(erro instanceof ErroHttp);
      assert.equal(erro.name, 'ErroHttp');
      assert.equal(erro.status, status);
      return true;
    });
  }
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

test('rejeita identificadores repetidos para evitar chaves ambiguas na interface', async () => {
  const tarefas = [
    { id: 1, titulo: 'Primeira', concluida: false },
    { id: 1, titulo: 'Outra', concluida: true },
  ];
  await assert.rejects(buscarTarefas('/tarefas', async () => ({
    ok: true, json: async () => tarefas,
  })), /duplicados/);
});

test('encaminha cancelamento e preserva a identidade da falha', async () => {
  const controller = new AbortController();
  controller.abort();
  const motivo = controller.signal.reason;
  await assert.rejects(buscarTarefas('/tarefas', async (_url, { signal }) => {
    assert.equal(signal, controller.signal);
    signal.throwIfAborted();
  }, { signal: controller.signal }), (erro) => erro === motivo);
});

test('nao inicia requisicao com sinal previamente cancelado', async () => {
  const controller = new AbortController();
  controller.abort(new Error('cancelado antes'));
  let chamadas = 0;
  await assert.rejects(buscarTarefas('/tarefas', async () => {
    chamadas += 1;
  }, { signal: controller.signal }), (erro) => erro === controller.signal.reason);
  assert.equal(chamadas, 0);
});

test('descarta resultado cancelado durante leitura do JSON', async () => {
  const controller = new AbortController();
  await assert.rejects(buscarTarefas('/tarefas', async () => ({
    ok: true,
    json: async () => {
      controller.abort(new Error('consulta substituida'));
      return [];
    },
  }), { signal: controller.signal }), (erro) => erro === controller.signal.reason);
});
