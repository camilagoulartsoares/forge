import test from 'node:test';
import assert from 'node:assert/strict';
import { exportarTarefas } from './exportar-tarefas.mjs';
import { importarTarefas } from './importar-tarefas.mjs';

test('exporta somente contrato publico sem hooks ou metadados locais', () => {
  const tarefa = { id: 1, titulo: 'Estudar \u00e7\u00e3o \ud83d\ude00', concluida: true,
    proprietarioId: 99, toJSON() { throw new Error('Nao deve serializar entidade completa'); } };
  tarefa.circular = tarefa;
  Object.freeze(tarefa);
  const json = exportarTarefas(Object.freeze([tarefa]));
  assert.deepEqual(importarTarefas([], json), [{ id: 1, titulo: tarefa.titulo, concluida: true }]);
  assert.equal(json.includes('proprietario'), false);
  assert.equal(exportarTarefas([]), '[]');
  assert.throws(() => exportarTarefas([{ id: 1, titulo: '', concluida: false }]), TypeError);
});
