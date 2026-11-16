import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';
import { identificadorValido } from './identificador.mjs';

export function removerTarefa(tarefas, id) {
  validarTarefas(tarefas);
  if (!identificadorValido(id)) throw new TypeError('Identificador invalido.');
  if (!tarefas.some(tarefa => tarefa.id === id)) throw new RangeError('Tarefa nao encontrada.');
  return tarefas.filter(tarefa => tarefa.id !== id);
}
