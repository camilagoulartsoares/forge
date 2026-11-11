import { test } from 'node:test';
import assert from 'node:assert/strict';
import { criarConsultaTarefas } from './consulta-tarefas.mjs';

test('resposta antiga nao sobrescreve dados ou carregamento atuais', async () => {
  const pendentes = new Map();
  const consulta = criarConsultaTarefas((url, _request, { signal }) => new Promise((resolve, reject) => {
    pendentes.set(url, { resolve, reject, signal });
  }));
  const a = consulta.recarregar('A');
  const b = consulta.recarregar('B');
  assert.equal(pendentes.get('A').signal.aborted, true);
  pendentes.get('A').reject(new Error('falha antiga'));
  await a;
  assert.equal(consulta.estado.carregando, true);
  assert.equal(consulta.estado.erro, null);
  pendentes.get('B').resolve([{ id: 2, titulo: 'Atual', concluida: false }]);
  await b;
  assert.equal(consulta.estado.dados[0].id, 2);
  assert.equal(consulta.estado.carregando, false);
});

test('cancelamento e descarte impedem alteracoes tardias', async () => {
  let resolver;
  const consulta = criarConsultaTarefas(() => new Promise((resolve) => { resolver = resolve; }));
  const promessa = consulta.recarregar('A');
  consulta.descartar();
  resolver([{ id: 1 }]);
  await promessa;
  assert.deepEqual(consulta.estado.dados, []);
  assert.equal(consulta.estado.carregando, false);
  await assert.rejects(consulta.recarregar('B'), /descartada/);
});

test('instancias sao isoladas e uma nova tentativa limpa o erro', async () => {
  let falhar = true;
  const consulta = criarConsultaTarefas(async () => {
    if (falhar) throw new Error('offline');
    return [{ id: 1, titulo: 'Tarefa', concluida: false }];
  });
  const outra = criarConsultaTarefas();
  await consulta.recarregar('A');
  assert.match(consulta.estado.erro.message, /offline/);
  falhar = false;
  await consulta.recarregar('A');
  assert.equal(consulta.estado.erro, null);
  assert.equal(Object.isFrozen(consulta.estado.dados[0]), true);
  assert.deepEqual(outra.estado.dados, []);
});

test('fonte injetada invalida nao publica dados no estado', async () => {
  const consulta = criarConsultaTarefas(async () => [null]);
  await consulta.recarregar('A');
  assert.deepEqual(consulta.estado.dados, []);
  assert.match(consulta.estado.erro.message, /Resposta de tarefas invalida/);
  assert.equal(consulta.estado.carregando, false);
});
