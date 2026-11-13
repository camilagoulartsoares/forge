import { identificadorValido } from './identificador.mjs';

export function concluirTarefa(tarefas, id) {
  if (!identificadorValido(id)) throw new TypeError('Identificador invalido.');
  let indice = -1;
  for (let i = 0; i < tarefas.length; i += 1) {
    if (tarefas[i].id !== id) continue;
    if (indice !== -1) throw new RangeError('Identificador duplicado.');
    indice = i;
  }
  if (indice === -1) throw new RangeError('Tarefa nao encontrada.');
  if (typeof tarefas[indice].concluida !== 'boolean') throw new TypeError('Estado invalido.');
  if (tarefas[indice].concluida) return tarefas;
  return tarefas.map((tarefa, i) => i === indice ? { ...tarefa, concluida: true } : tarefa);
}
