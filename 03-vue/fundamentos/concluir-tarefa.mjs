export function concluirTarefa(tarefas, id) {
  if (!Number.isSafeInteger(id) || id <= 0) throw new TypeError('Identificador invalido.');
  const indices = tarefas.flatMap((tarefa, indice) => tarefa.id === id ? [indice] : []);
  if (indices.length === 0) throw new RangeError('Tarefa nao encontrada.');
  if (indices.length > 1) throw new RangeError('Identificador duplicado.');
  const indice = indices[0];
  if (typeof tarefas[indice].concluida !== 'boolean') throw new TypeError('Estado invalido.');
  if (tarefas[indice].concluida) return tarefas;
  return tarefas.map((tarefa, i) => i === indice ? { ...tarefa, concluida: true } : tarefa);
}
