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

test('integra contrato da API com pesquisa normalizada e desempate estavel em todas as permutacoes', () => {
  const itens = Object.freeze([
    Object.freeze({ id: 9, titulo: '\u00c1rvore PHP', concluida: false }),
    Object.freeze({ id: 2, titulo: 'arvore php', concluida: false }),
    Object.freeze({ id: 4, titulo: 'Arvore PHP', concluida: true }),
  ]);
  const permutacoes = [[0, 1, 2], [0, 2, 1], [1, 0, 2], [1, 2, 0], [2, 0, 1], [2, 1, 0]];
  for (const ordem of permutacoes) {
    const entrada = Object.freeze(ordem.map(indice => itens[indice]));
    const resultado = selecionarTarefas(entrada, { estado: 'pendentes', termo: 'PHP arvore' });
    assert.deepEqual(resultado.itens.map(tarefa => tarefa.id), [2, 9]);
    assert.equal(resultado.itens[0], itens[1]);
    assert.deepEqual(entrada.map(tarefa => tarefa.id), ordem.map(indice => itens[indice].id));
  }
});
