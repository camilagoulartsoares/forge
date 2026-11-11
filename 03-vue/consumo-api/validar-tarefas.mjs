export function validarTarefas(tarefas) {
  if (!Array.isArray(tarefas)) throw new TypeError('Resposta de tarefas invalida.');
  const ids = new Set();
  for (const tarefa of tarefas) {
    if (tarefa === null || typeof tarefa !== 'object' || Array.isArray(tarefa)
      || !Number.isSafeInteger(tarefa.id) || tarefa.id <= 0
      || typeof tarefa.titulo !== 'string' || tarefa.titulo.trim().length === 0
      || typeof tarefa.concluida !== 'boolean') {
      throw new TypeError('Resposta de tarefas invalida.');
    }
    if (ids.has(tarefa.id)) throw new TypeError('Resposta contem identificadores de tarefas duplicados.');
    ids.add(tarefa.id);
  }
  return tarefas;
}
