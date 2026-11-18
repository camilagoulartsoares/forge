import test from 'node:test';
import assert from 'node:assert/strict';
import { resumirTarefas } from './resumo-tarefas.mjs';
import { concluirTarefa } from '../fundamentos/concluir-tarefa.mjs';

test('deriva resumo da mesma lista e reflete conclusao sem sincronizacao manual', () => {
  const original = Object.freeze([
    Object.freeze({ id: 1, titulo: 'A', concluida: false }),
    Object.freeze({ id: 2, titulo: 'B', concluida: true }),
  ]);
  assert.deepEqual(resumirTarefas(original), { total: 2, pendentes: 1, concluidas: 1 });
  assert.deepEqual(resumirTarefas(concluirTarefa(original, 1)), { total: 2, pendentes: 0, concluidas: 2 });
  assert.equal(original[0].concluida, false);
  assert.deepEqual(resumirTarefas([]), { total: 0, pendentes: 0, concluidas: 0 });
  assert.throws(() => resumirTarefas([{ id: 1, titulo: 'A', concluida: 1 }]), TypeError);
});
