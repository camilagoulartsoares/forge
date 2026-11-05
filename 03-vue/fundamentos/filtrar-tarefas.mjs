export function filtrarTarefas(tarefas, estado = 'todas') {
  if (!['todas', 'pendentes', 'concluidas'].includes(estado)) {
    throw new RangeError('Estado de filtro desconhecido.');
  }

  return tarefas.filter((tarefa) => estado === 'todas'
    || tarefa.concluida === (estado === 'concluidas'));
}
