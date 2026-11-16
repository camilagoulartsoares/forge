import test from 'node:test';
import assert from 'node:assert/strict';
import { renomearTarefa } from './renomear-tarefa.mjs';

const origem = Object.freeze([
  Object.freeze({ id: 9, titulo: 'Antes', concluida: true }),
  Object.freeze({ id: 2, titulo: 'Outra', concluida: false }),
]);
test('renomeia pelo id, normaliza e preserva estado e referencias nao afetadas', () => {
  const nova = renomearTarefa(origem, 9, '  Depois  ');
  assert.deepEqual(nova[0], { id: 9, titulo: 'Depois', concluida: true });
  assert.equal(nova[1], origem[1]);
  assert.equal(origem[0].titulo, 'Antes');
  assert.equal(renomearTarefa(origem, 9, ' Antes '), origem);
});
test('falhas de titulo ou identidade nao alteram a lista', () => {
  for (const titulo of ['', ' ', null, 'x'.repeat(121)]) {
    assert.throws(() => renomearTarefa(origem, 9, titulo), TypeError);
  }
  assert.throws(() => renomearTarefa(origem, 0, 'Nova'), TypeError);
  assert.throws(() => renomearTarefa(origem, 1, 'Nova'), RangeError);
});
