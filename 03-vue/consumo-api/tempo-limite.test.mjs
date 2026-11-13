import test from 'node:test';
import assert from 'node:assert/strict';
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
  const remover = t.mock.method(controller.signal, 'removeEventListener');
  const limpar = t.mock.method(globalThis, 'clearTimeout');
  assert.deepEqual(await buscarTarefas('/', async () => ({ ok: true, json: async () => [] }),
    { signal: controller.signal, timeoutMs: 10000 }), []);
  await assert.rejects(buscarTarefas('/', async () => { throw new Error('Rede'); },
    { signal: controller.signal, timeoutMs: 10000 }), /Rede/);
  assert.equal(remover.mock.callCount(), 2);
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
