import test from 'node:test';
import assert from 'node:assert/strict';
import { paginarTarefas } from './paginar-tarefas.mjs';

const tarefas = Array.from({ length: 5 }, (_, indice) => ({ id: indice + 1, titulo: `Tarefa ${indice}`, concluida: indice === 0 }));
test('pagina depois de filtrar e informa totais e ultima pagina parcial', () => {
  const opcoes = { estado: 'pendentes', tamanho: 3 };
  const primeira = paginarTarefas(tarefas, opcoes);
  assert.deepEqual(primeira.itens.map(t => t.id), [2, 3, 4]);
  assert.equal(primeira.total, 5);
  assert.equal(primeira.encontradas, 4);
  assert.equal(primeira.paginas, 2);
  assert.deepEqual(paginarTarefas(tarefas, { ...opcoes, pagina: 2 }).itens.map(t => t.id), [5]);
  assert.deepEqual(paginarTarefas(tarefas, { pagina: Number.MAX_SAFE_INTEGER }).itens, []);
  assert.equal(paginarTarefas([]).paginas, 0);
});
test('rejeita limites invalidos sem coercao', () => {
  for (const pagina of [0, -1, 1.5, '1', Infinity]) assert.throws(() => paginarTarefas([], { pagina }), RangeError);
  for (const tamanho of [0, 101, 1.5, '20', NaN]) assert.throws(() => paginarTarefas([], { tamanho }), RangeError);
});
