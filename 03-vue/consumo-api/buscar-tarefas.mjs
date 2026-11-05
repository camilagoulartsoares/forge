export async function buscarTarefas(url, requisitar = globalThis.fetch) {
  const resposta = await requisitar(url, { headers: { Accept: 'application/json' } });
  if (!resposta.ok) {
    throw new Error(`Falha HTTP: ${resposta.status}`);
  }

  const tarefas = await resposta.json();
  if (!Array.isArray(tarefas) || !tarefas.every((tarefa) => tarefa !== null
    && Number.isSafeInteger(tarefa.id) && tarefa.id > 0
    && typeof tarefa.titulo === 'string' && tarefa.titulo.trim().length > 0
    && typeof tarefa.concluida === 'boolean')) {
    throw new TypeError('Resposta de tarefas invalida.');
  }

  return tarefas;
}
