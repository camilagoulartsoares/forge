import { validarTitulo } from '../formularios/validar-titulo.mjs';
import { identificadorValido } from '../fundamentos/identificador.mjs';

export function validarTarefas(tarefas) {
  if (!Array.isArray(tarefas)) throw new TypeError('Resposta de tarefas invalida.');
  const ids = new Set();
  for (let indice = 0; indice < tarefas.length; indice += 1) {
    const item = Object.getOwnPropertyDescriptor(tarefas, indice);
    if (!item?.enumerable || !Object.hasOwn(item, 'value')) throw new TypeError('Resposta de tarefas invalida.');
    const tarefa = item.value;
    if (tarefa === null || typeof tarefa !== 'object' || Array.isArray(tarefa)
      || !['id', 'titulo', 'concluida'].every(campo => {
        const descritor = Object.getOwnPropertyDescriptor(tarefa, campo);
        return descritor?.enumerable && Object.hasOwn(descritor, 'value');
      })
      || !identificadorValido(tarefa.id)
      || !validarTitulo(tarefa.titulo).valido
      || typeof tarefa.concluida !== 'boolean') {
      throw new TypeError('Resposta de tarefas invalida.');
    }
    if (ids.has(tarefa.id)) throw new TypeError('Resposta contem identificadores de tarefas duplicados.');
    ids.add(tarefa.id);
  }
  return tarefas;
}
