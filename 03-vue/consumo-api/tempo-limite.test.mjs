import test from 'node:test';
import assert from 'node:assert/strict';
import { getEventListeners } from 'node:events';
import { buscarTarefas } from './buscar-tarefas.mjs';

const aguardarAbort = (_url, { signal }) => new Promise((_resolver, rejeitar) => {
  signal.addEventListener('abort', () => rejeitar(signal.reason), { once: true });
});
test('tempo limite aborta consulta e preserva motivo do cancelamento externo', async () => {
  await assert.rejects(buscarTarefas('/tarefas', aguardarAbort, { timeoutMs: 5 }), { name: 'TimeoutError' });
  const controller = new AbortController();
  const promessa = buscarTarefas('/tarefas', aguardarAbort, { signal: controller.signal, timeoutMs: 10000 });
  const motivo = new Error('Cancelada pelo usuario');
  controller.abort(motivo);
  await assert.rejects(promessa, erro => erro === motivo);
});
test('remove timer e ouvinte tanto no sucesso quanto na falha', async (t) => {
  const controller = new AbortController();
  const limpar = t.mock.method(globalThis, 'clearTimeout');
  assert.deepEqual(await buscarTarefas('/', async () => ({ ok: true, json: async () => [] }),
    { signal: controller.signal, timeoutMs: 10000 }), []);
  assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
  await assert.rejects(buscarTarefas('/', async () => { throw new Error('Rede'); },
    { signal: controller.signal, timeoutMs: 10000 }), /Rede/);
  assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
  assert.equal(limpar.mock.callCount(), 2);
});
test('limites invalidos e sinal previamente cancelado nao iniciam requisicao', async () => {
  let chamadas = 0;
  const requisitar = async () => { chamadas += 1; };
  for (const timeoutMs of [0, -1, 1.5, NaN, Infinity, '5', 2147483648]) {
    await assert.rejects(buscarTarefas('/', requisitar, { timeoutMs }), RangeError);
  }
  await assert.rejects(buscarTarefas('/', requisitar, { timeoutMs: 5, signal: AbortSignal.abort() }), { name: 'AbortError' });
  assert.equal(chamadas, 0);
});

test('encerra no prazo mesmo quando a fonte ignora abort e trata rejeicao tardia', async () => {
  let rejeitar;
  const fonte = () => new Promise((_resolver, falhar) => { rejeitar = falhar; });
  await assert.rejects(buscarTarefas('/', fonte, { timeoutMs: 5 }), { name: 'TimeoutError' });
  rejeitar(new Error('Falha depois do timeout'));
  await Promise.resolve();
  const controller = new AbortController();
  const cancelada = buscarTarefas('/', () => new Promise(() => {}), { timeoutMs: 10000, signal: controller.signal });
  controller.abort();
  await assert.rejects(cancelada, { name: 'AbortError' });
});

test('tempo limite inclui corpo pendente e ignora sua resolucao tardia', async () => {
  let resolverCorpo;
  let sinal;
  const fonte = async (_url, { signal }) => {
    sinal = signal;
    return { ok: true, json: () => new Promise(resolver => { resolverCorpo = resolver; }) };
  };
  await assert.rejects(buscarTarefas('/', fonte, { timeoutMs: 5 }), { name: 'TimeoutError' });
  assert.equal(sinal.aborted, true);
  resolverCorpo([{ id: 1, titulo: 'Tardia', concluida: false }]);
  await Promise.resolve();
});
test('cancelamento externo interrompe corpo pendente com motivo original', async () => {
  const controller = new AbortController();
  let corpoIniciado;
  const iniciado = new Promise(resolver => { corpoIniciado = resolver; });
  const fonte = async () => ({ ok: true, json: () => { corpoIniciado(); return new Promise(() => {}); } });
  const consulta = buscarTarefas('/', fonte, { timeoutMs: 10000, signal: controller.signal });
  await iniciado;
  const motivo = new Error('Tela fechada');
  controller.abort(motivo);
  await assert.rejects(consulta, erro => erro === motivo);
});
