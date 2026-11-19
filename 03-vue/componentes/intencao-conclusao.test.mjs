import test from 'node:test';
import assert from 'node:assert/strict';
import { criarIntencaoConclusao } from './intencao-conclusao.mjs';

test('gera evento com id e proximo estado sem modificar a prop', () => {
  for (const concluida of [false, true]) {
    const tarefa = Object.freeze({ id: 17, titulo: 'A'.repeat(120), concluida });
    assert.deepEqual(criarIntencaoConclusao(tarefa), { evento: 'alterar-conclusao', payload: { id: 17, concluida: !concluida } });
    assert.equal(criarIntencaoConclusao(tarefa, true), null);
    assert.equal(tarefa.concluida, concluida);
    assert.throws(() => criarIntencaoConclusao(tarefa, 'false'), TypeError);
  }
  assert.throws(() => criarIntencaoConclusao({ id: 0, titulo: 'A', concluida: false }), TypeError);
});
