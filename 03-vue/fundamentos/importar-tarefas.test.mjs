import test from 'node:test';
import assert from 'node:assert/strict';
import { importarTarefas } from './importar-tarefas.mjs';

const tarefa = Object.freeze({ id: 1, titulo: 'Original', concluida: false });
const origem = Object.freeze([tarefa]);
test('importa lote valido e preserva origem', () => {
  const resultado = importarTarefas(origem, '[{"id":2,"titulo":"Nova","concluida":false}]');
  assert.equal(resultado.length, 2);
  assert.equal(resultado[0], tarefa);
  assert.notEqual(importarTarefas(origem, '[]'), origem);
});
test('lote malformado, invalido ou conflitante nao produz importacao parcial', () => {
  for (const json of ['{', '{}', '[null]', JSON.stringify([tarefa]),
    '[{"id":2,"titulo":"Nova","concluida":false},{"id":3}]']) {
    assert.throws(() => importarTarefas(origem, json));
    assert.deepEqual(origem, [tarefa]);
  }
  assert.throws(() => importarTarefas(origem, []), TypeError);
});

test('importacao recusa nomes JSON repetidos inclusive com escapes equivalentes', () => {
  for (const json of ['[{"id":2,"id":3,"titulo":"Nova","concluida":false}]',
    '[{"id":2,"titulo":"Nova","ti\\u0074ulo":"Outra","concluida":false}]']) {
    assert.throws(() => importarTarefas([], json), SyntaxError);
  }
});

test('valida a propria colecao retornada usando os indices da origem', () => {
  const lista = [tarefa];
  let percursos = 0;
  Object.defineProperty(lista, Symbol.iterator, { value: function* () {
    percursos += 1;
    yield percursos === 1 ? tarefa : null;
  } });
  Object.freeze(lista);
  assert.deepEqual(importarTarefas(lista, '[]'), [tarefa]);
  assert.equal(percursos, 0);
  assert.equal(lista[0], tarefa);

  const invalidaAoIterar = [tarefa];
  invalidaAoIterar[Symbol.iterator] = function* () { yield null; };
  assert.deepEqual(importarTarefas(invalidaAoIterar, '[]'), [tarefa]);
  assert.throws(() => importarTarefas(invalidaAoIterar, JSON.stringify([tarefa])), /duplicados/);
  assert.equal(invalidaAoIterar[0], tarefa);
});
