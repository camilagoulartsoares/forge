import test from 'node:test';
import assert from 'node:assert/strict';
import { getEventListeners } from 'node:events';
import { aguardarComCancelamento } from './aguardar-cancelamento.mjs';

test('reutilizar o sinal em sucessos e falhas nao acumula ouvintes', async () => {
  const controller = new AbortController();
  for (let indice = 0; indice < 100; indice += 1) {
    assert.equal(await aguardarComCancelamento(Promise.resolve(indice), controller.signal), indice);
    const erro = new Error('Falha da fonte');
    await assert.rejects(aguardarComCancelamento(Promise.reject(erro), controller.signal), recebido => recebido === erro);
    assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
  }
  assert.equal(controller.signal.aborted, false);
});

test('abort libera ouvinte e absorve rejeicao tardia da fonte', async () => {
  const controller = new AbortController();
  let rejeitar;
  const fonte = new Promise((_resolve, reject) => { rejeitar = reject; });
  const espera = aguardarComCancelamento(fonte, controller.signal);
  const motivo = { codigo: 'SAIR' };
  controller.abort(motivo);
  await assert.rejects(espera, recebido => recebido === motivo);
  assert.equal(getEventListeners(controller.signal, 'abort').length, 0);
  rejeitar(new Error('A fonte encerrou depois'));
  await new Promise(setImmediate);
});

test('sinal previamente abortado preserva motivo e nao deixa listener', async () => {
  const signal = AbortSignal.abort(new Error('Cancelado'));
  await assert.rejects(aguardarComCancelamento(new Promise(() => {}), signal), erro => erro === signal.reason);
  assert.equal(getEventListeners(signal, 'abort').length, 0);
});

test('cancelamento previo vence fontes ja resolvidas ou rejeitadas', async () => {
  const signal = AbortSignal.abort(new Error('Cancelado antes da espera'));
  for (const fonte of [Promise.resolve('Nao publicar'), Promise.reject(new Error('Falha da fonte'))]) {
    await assert.rejects(aguardarComCancelamento(fonte, signal), erro => erro === signal.reason);
    assert.equal(getEventListeners(signal, 'abort').length, 0);
  }
  await new Promise(setImmediate);
});
