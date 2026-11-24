import { identificadorValido } from './identificador.mjs';

export function concluirTarefa(tarefas, id) {
  if (!identificadorValido(id)) throw new TypeError('Identificador invalido.');
  if (!Array.isArray(tarefas)) throw new TypeError('Lista de tarefas invalida.');
  let indice = -1;
  const ids = new Set();
  for (let i = 0; i < tarefas.length; i += 1) {
    const tarefa = tarefas[i];
    if (!Object.hasOwn(tarefas, i) || tarefa === null || typeof tarefa !== 'object'
      || !identificadorValido(tarefa.id) || typeof tarefa.concluida !== 'boolean') {
      throw new TypeError('Tarefa invalida.');
    }
    if (ids.has(tarefa.id)) throw new RangeError('Identificador duplicado.');
    ids.add(tarefa.id);
    if (tarefa.id !== id) continue;
    indice = i;
  }
  if (indice === -1) throw new RangeError('Tarefa nao encontrada.');
  if (tarefas[indice].concluida) return tarefas;
  return tarefas.map((tarefa, i) => i === indice ? { ...tarefa, concluida: true } : tarefa);
}
