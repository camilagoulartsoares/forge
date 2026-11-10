import { test } from 'node:test';
import assert from 'node:assert/strict';
import { pesquisarTarefas } from './pesquisar-tarefas.mjs';

const tarefas = Object.freeze([
  Object.freeze({ id: 1, titulo: 'Revisão da API' }),
  Object.freeze({ id: 2, titulo: 'Estudar PHP' }),
  Object.freeze({ id: 3, titulo: 'API: validação' }),
]);

test('pesquisa sem distinguir acentos, composicao Unicode ou maiusculas', () => {
  for (const consulta of ['REVISAO', 'revisa\u0303o']) {
    assert.deepEqual(pesquisarTarefas(tarefas, consulta).map((t) => t.id), [1]);
  }
});

test('exige todos os termos e trata pontuacao literalmente', () => {
  assert.deepEqual(pesquisarTarefas(tarefas, '  API revisao  ').map((t) => t.id), [1]);
  assert.deepEqual(pesquisarTarefas(tarefas, 'API:').map((t) => t.id), [3]);
  assert.deepEqual(pesquisarTarefas(tarefas, '.*'), []);
});

test('consulta vazia preserva ordem e retorna novo array', () => {
  const resultado = pesquisarTarefas(tarefas, ' \t ');
  assert.deepEqual(resultado, tarefas);
  assert.notEqual(resultado, tarefas);
  assert.throws(() => pesquisarTarefas(tarefas, null), TypeError);
});

test('termos repetidos nao exigem ocorrencias repetidas no titulo', () => {
  assert.deepEqual(pesquisarTarefas(tarefas, 'api API api').map((t) => t.id), [1, 3]);
});
