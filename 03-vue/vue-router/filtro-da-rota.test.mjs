import { test } from 'node:test';
import assert from 'node:assert/strict';
import { lerFiltroDaRota, atualizarFiltroDaRota } from './filtro-da-rota.mjs';
import { estadosDeTarefa } from '../fundamentos/estados.mjs';
import { filtrarTarefas } from '../fundamentos/filtrar-tarefas.mjs';

test('todos os estados publicados sao aceitos tanto pela rota quanto pelo filtro', () => {
  for (const estado of estadosDeTarefa) {
    assert.equal(lerFiltroDaRota({ estado }), estado);
    assert.deepEqual(filtrarTarefas([], estado), []);
  }
  assert.equal(Object.isFrozen(estadosDeTarefa), true);
});

test('le somente estados singulares reconhecidos', () => {
  for (const estado of ['todas', 'pendentes', 'concluidas']) {
    assert.equal(lerFiltroDaRota({ estado }), estado);
  }
  for (const estado of [null, undefined, '', 'outro', ['pendentes'], ['pendentes', 'concluidas']]) {
    assert.equal(lerFiltroDaRota({ estado }), 'todas');
  }
  assert.equal(lerFiltroDaRota(null), 'todas');
});

test('atualiza estado sem perder parametros ou alterar a origem', () => {
  const origem = Object.freeze({ busca: 'PHP', estado: 'pendentes' });
  assert.deepEqual(atualizarFiltroDaRota(origem, 'concluidas'), { busca: 'PHP', estado: 'concluidas' });
  assert.deepEqual(atualizarFiltroDaRota(origem, 'todas'), { busca: 'PHP' });
  assert.equal(origem.estado, 'pendentes');
  assert.throws(() => atualizarFiltroDaRota(origem, 'outro'), RangeError);
});
