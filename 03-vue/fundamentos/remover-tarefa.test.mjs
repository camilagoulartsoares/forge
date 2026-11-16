import test from 'node:test';
import assert from 'node:assert/strict';
import { removerTarefa } from './remover-tarefa.mjs';

test('remove pelo id apos reordenacao, preservando referencias e origem', () => {
  const a = Object.freeze({ id: 30, titulo: 'A', concluida: false });
  const b = Object.freeze({ id: 2, titulo: 'B', concluida: true });
  const origem = Object.freeze([b, a]);
  const resultado = removerTarefa(origem, 30);
  assert.deepEqual(resultado, [b]);
  assert.equal(resultado[0], b);
  assert.equal(origem.length, 2);
  assert.deepEqual(removerTarefa([a], 30), []);
  assert.throws(() => removerTarefa(origem, 1), RangeError);
  assert.throws(() => removerTarefa(origem, '2'), TypeError);
  assert.throws(() => removerTarefa([a, a], 30), TypeError);
});
