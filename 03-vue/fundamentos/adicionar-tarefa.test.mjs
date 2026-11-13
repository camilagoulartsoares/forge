import test from 'node:test';
import assert from 'node:assert/strict';
import { adicionarTarefa } from './adicionar-tarefa.mjs';

test('normaliza titulo e cria pendente sem alterar a lista', () => {
  const origem = Object.freeze([{ id: 1, titulo: 'Antes', concluida: true }]);
  const resultado = adicionarTarefa(origem, 2, '  Estudar  ');
  assert.deepEqual(resultado[1], { id: 2, titulo: 'Estudar', concluida: false });
  assert.equal(resultado[0], origem[0]);
  assert.equal(origem.length, 1);
  assert.throws(() => adicionarTarefa(origem, 1, 'Nova'), RangeError);
  for (const [id, titulo] of [[0, 'Nova'], [2, ' '], [2, 123], [2, 'a'.repeat(121)]]) {
    assert.throws(() => adicionarTarefa(origem, id, titulo), TypeError);
  }
});
