import { test } from 'node:test';
import assert from 'node:assert/strict';
import { concluirTarefa } from './concluir-tarefa.mjs';

test('atualiza por id e preserva a origem e referencias nao alteradas', () => {
  const tarefas = Object.freeze([Object.freeze({ id: 7, concluida: false }), Object.freeze({ id: 2, concluida: false })]);
  const resultado = concluirTarefa(tarefas, 2);
  assert.notEqual(resultado, tarefas);
  assert.equal(resultado[0], tarefas[0]);
  assert.equal(resultado[1].concluida, true);
  assert.equal(tarefas[1].concluida, false);
  assert.equal(concluirTarefa(resultado, 2), resultado);
});

test('recusa identificador ausente duplicado ou invalido sem escolher item arbitrario', () => {
  assert.throws(() => concluirTarefa([], 1), RangeError);
  assert.throws(() => concluirTarefa([{ id: 1 }, { id: 1 }], 1), /duplicado/);
  assert.throws(() => concluirTarefa([{ id: 1 }], 1), TypeError);
  for (const id of ['1', 0, -1, NaN, Number.MAX_SAFE_INTEGER + 1]) {
    assert.throws(() => concluirTarefa([], id), TypeError);
  }
});
