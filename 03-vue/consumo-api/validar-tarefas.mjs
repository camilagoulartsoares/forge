import { validarTitulo } from '../formularios/validar-titulo.mjs';

export function validarTarefas(tarefas) {
  if (!Array.isArray(tarefas)) throw new TypeError('Resposta de tarefas invalida.');
  const ids = new Set();
  for (const tarefa of tarefas) {
    if (tarefa === null || typeof tarefa !== 'object' || Array.isArray(tarefa)
      || !Number.isSafeInteger(tarefa.id) || tarefa.id <= 0
      || !validarTitulo(tarefa.titulo).valido
      || typeof tarefa.concluida !== 'boolean') {
      throw new TypeError('Resposta de tarefas invalida.');
    }
    if (ids.has(tarefa.id)) throw new TypeError('Resposta contem identificadores de tarefas duplicados.');
    ids.add(tarefa.id);
  }
  return tarefas;
}
