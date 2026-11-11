import { test } from 'node:test';
import assert from 'node:assert/strict';
import { validarTarefas } from './validar-tarefas.mjs';

test('valida sem copiar ou alterar a lista', () => {
  const lista = Object.freeze([Object.freeze({ id: 1, titulo: 'A', concluida: false })]);
  assert.equal(validarTarefas(lista), lista);
  assert.deepEqual(validarTarefas([]), []);
});

test('rejeita listas esparsas, itens nao objetos e ids repetidos', () => {
  for (const lista of [null, {}, new Array(1), [undefined], [null], [[]], [false]]) {
    assert.throws(() => validarTarefas(lista), /Resposta de tarefas invalida/);
  }
  const tarefa = { id: 1, titulo: 'A', concluida: false };
  assert.throws(() => validarTarefas([tarefa, tarefa]), /duplicados/);
});
