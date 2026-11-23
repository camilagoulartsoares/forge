import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';
import { identificadorValido } from './identificador.mjs';

// Aplica o estado desejado emitido pelo componente; repetir a intencao e idempotente.
export function alterarConclusao(tarefas, id, concluida) {
  validarTarefas(tarefas);
  if (!identificadorValido(id) || typeof concluida !== 'boolean') throw new TypeError('Intencao invalida.');
  const indice = tarefas.findIndex(tarefa => tarefa.id === id);
  if (indice < 0) throw new RangeError('Tarefa nao encontrada.');
  if (tarefas[indice].concluida === concluida) return tarefas;
  return tarefas.map((tarefa, posicao) => posicao === indice ? { ...tarefa, concluida } : tarefa);
}
