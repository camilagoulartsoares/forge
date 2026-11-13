import test from 'node:test';
import assert from 'node:assert/strict';
import { selecionarTarefas } from './selecionar-tarefas.mjs';

const tarefas = [
  { id: 1, titulo: 'Estudar PHP', concluida: false },
  { id: 2, titulo: 'Testar PHP', concluida: true },
  { id: 3, titulo: 'Aplicar PHP', concluida: false },
];
test('compoe estado, pesquisa e ordem mantendo contagens distintas', () => {
  const resultado = selecionarTarefas(tarefas, { estado: 'pendentes', termo: 'php' });
  assert.deepEqual(resultado.itens.map(t => t.id), [3, 1]);
  assert.equal(resultado.total, 3);
  assert.equal(resultado.encontradas, 2);
  assert.deepEqual(selecionarTarefas([]), { itens: [], total: 0, encontradas: 0 });
  assert.throws(() => selecionarTarefas([{ ...tarefas[0], id: 0 }]), TypeError);
  assert.throws(() => selecionarTarefas(tarefas, { estado: 'outro' }), RangeError);
});
