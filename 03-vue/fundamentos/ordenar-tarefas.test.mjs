import { test } from 'node:test';
import assert from 'node:assert/strict';
import { ordenarTarefas } from './ordenar-tarefas.mjs';

const tarefas = Object.freeze([
  Object.freeze({ id: 3, titulo: 'Beta', criadaEm: '2026-01-01T00:00:00.000Z' }),
  Object.freeze({ id: 2, titulo: 'alfa', criadaEm: '2026-01-02T00:00:00.000Z' }),
  Object.freeze({ id: 1, titulo: 'Alfa', criadaEm: '2026-01-01T00:00:00.000Z' }),
]);

test('ordena por titulo e desempata pelo identificador', () => {
  assert.deepEqual(ordenarTarefas(tarefas).map((t) => t.id), [1, 2, 3]);
});

test('ordena por data UTC e desempata pelo identificador', () => {
  assert.deepEqual(ordenarTarefas(tarefas, 'data').map((t) => t.id), [1, 3, 2]);
});

test('preserva a origem e aceita vazio ou um item', () => {
  assert.deepEqual(ordenarTarefas([]), []);
  assert.deepEqual(ordenarTarefas([tarefas[0]]), [tarefas[0]]);
  const resultado = ordenarTarefas(tarefas);
  assert.notEqual(resultado, tarefas);
  assert.equal(tarefas[0].id, 3);
  assert.equal(resultado[0], tarefas[2]);
});

test('rejeita criterios desconhecidos inclusive nomes herdados', () => {
  assert.throws(() => ordenarTarefas(tarefas, 'outro'), RangeError);
  assert.throws(() => ordenarTarefas(tarefas, 'toString'), RangeError);
});

test('nao aceita criterios convertidos implicitamente para string', () => {
  for (const criterio of [['titulo'], { toString: () => 'data' }, null, 1]) {
    assert.throws(() => ordenarTarefas(tarefas, criterio), RangeError);
  }
});
