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

test('aplica ao titulo recebido o mesmo contrato usado no formulario', () => {
  for (const titulo of ['a'.repeat(121), '😀'.repeat(61), '\uD800', ' ']) {
    assert.throws(() => validarTarefas([{ id: 1, titulo, concluida: false }]), TypeError);
  }
  assert.equal(validarTarefas([{ id: 1, titulo: '😀'.repeat(60), concluida: true }]).length, 1);
});
