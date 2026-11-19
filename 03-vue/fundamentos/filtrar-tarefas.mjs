import { estadoValido } from './estados.mjs';

export function filtrarTarefas(tarefas, estado = 'todas') {
  if (!estadoValido(estado)) {
    throw new RangeError('Estado de filtro desconhecido.');
  }

  if (!Array.isArray(tarefas)) throw new TypeError('Lista de tarefas invalida.');
  for (let indice = 0; indice < tarefas.length; indice += 1) {
    if (!Object.hasOwn(tarefas, indice) || typeof tarefas[indice]?.concluida !== 'boolean') {
      throw new TypeError('Estado de conclusao invalido.');
    }
  }

  return tarefas.filter((tarefa) => estado === 'todas'
    || tarefa.concluida === (estado === 'concluidas'));
}
