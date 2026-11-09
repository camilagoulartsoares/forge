export async function buscarTarefas(url, requisitar = globalThis.fetch, { signal } = {}) {
  const resposta = await requisitar(url, { headers: { Accept: 'application/json' }, signal });
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

  if (new Set(tarefas.map((tarefa) => tarefa.id)).size !== tarefas.length) {
    throw new TypeError('Resposta contem identificadores de tarefas duplicados.');
  }

  return tarefas;
}
