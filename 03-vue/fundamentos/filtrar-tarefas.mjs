import { estadoValido } from './estados.mjs';

export function filtrarTarefas(tarefas, estado = 'todas') {
  if (!estadoValido(estado)) {
    throw new RangeError('Estado de filtro desconhecido.');
  }

  return tarefas.filter((tarefa) => estado === 'todas'
    || tarefa.concluida === (estado === 'concluidas'));
}
