import { test } from 'node:test';
import assert from 'node:assert/strict';
import { filtrarTarefas } from './filtrar-tarefas.mjs';

const tarefas = Object.freeze([
  Object.freeze({ id: 1, concluida: false }),
  Object.freeze({ id: 2, concluida: true }),
  Object.freeze({ id: 3, concluida: false }),
]);

test('filtra estados e preserva a ordem', () => {
  assert.deepEqual(filtrarTarefas(tarefas, 'pendentes').map((t) => t.id), [1, 3]);
  assert.deepEqual(filtrarTarefas(tarefas, 'concluidas').map((t) => t.id), [2]);
});

test('retorna novo array sem modificar a origem', () => {
  const resultado = filtrarTarefas(tarefas);
  assert.deepEqual(resultado, tarefas);
  assert.notEqual(resultado, tarefas);
  assert.equal(resultado[0], tarefas[0]);
});

test('aceita vazio e rejeita filtro desconhecido', () => {
  assert.deepEqual(filtrarTarefas([], 'pendentes'), []);
  assert.throws(() => filtrarTarefas(tarefas, 'inexistente'), RangeError);
});

test('rejeita estados de conclusao invalidos em vez de omitir tarefas silenciosamente', () => {
  for (const concluida of [0, 1, 'false', undefined, null]) {
    for (const estado of ['todas', 'pendentes', 'concluidas']) {
      assert.throws(() => filtrarTarefas([{ id: 1, concluida }], estado), TypeError);
    }
  }
  assert.throws(() => filtrarTarefas(new Array(1)), TypeError);
  assert.throws(() => filtrarTarefas({}), TypeError);
});
